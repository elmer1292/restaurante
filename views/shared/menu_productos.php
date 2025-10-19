<?php
require_once __DIR__ . '/../../models/ProductModel.php';
$productModel = new ProductModel();
$productos = $productModel->getAllProducts();

// Agrupar productos por categoría
$productosPorCategoria = [];
foreach ($productos as $producto) {
    $cat = $producto['Nombre_Categoria'] ?? 'Sin categoría';
    if (!isset($productosPorCategoria[$cat])) {
        $productosPorCategoria[$cat] = [];
    }
    $productosPorCategoria[$cat][] = $producto;
}
?>
<div class="menu-productos">
    <?php
    // Cargar mapa de tipos de categoría (food/drink)
    $categoryTypes = [];
    if (file_exists(__DIR__ . '/../../config/category_types.php')) {
        $categoryTypes = include __DIR__ . '/../../config/category_types.php';
    }
    $accordionId = 'accordionMenuProductos';
    $idx = 0;
    ?>
    <div class="accordion" id="<?php echo $accordionId; ?>">
    <?php foreach ($productosPorCategoria as $categoria => $items): ?>
        <?php $idx++; $catSafe = 'cat' . $idx; ?>
        <div class="accordion-item mb-2">
            <h2 class="accordion-header" id="heading-<?php echo $catSafe; ?>">
                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $catSafe; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $catSafe; ?>">
                    <?php echo htmlspecialchars($categoria); ?>
                </button>
            </h2>
            <div id="collapse-<?php echo $catSafe; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $catSafe; ?>" data-bs-parent="#<?php echo $accordionId; ?>">
                <div class="accordion-body p-0">
                    <div class="list-group">
                        <?php foreach ($items as $producto): ?>
                            <?php
                                // Prefer DB-provided is_food if available, otherwise fallback to config map
                                $isFood = '0';
                                if (isset($producto['is_food'])) {
                                    $isFood = ($producto['is_food'] == 1 || $producto['is_food'] === '1') ? '1' : '0';
                                } else {
                                    $catKey = strtolower(trim($producto['Nombre_Categoria'] ?? ''));
                                    $isFood = isset($categoryTypes[$catKey]) && $categoryTypes[$catKey] === 'food' ? '1' : '0';
                                }
                            ?>
                            <button type="button"
                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center btn-agregar-producto"
                                data-nombre="<?php echo htmlspecialchars($producto['Nombre_Producto'], ENT_QUOTES); ?>"
                                data-id="<?php echo $producto['ID_Producto']; ?>"
                                data-categoria="<?php echo htmlspecialchars($producto['Nombre_Categoria'] ?? '', ENT_QUOTES); ?>"
                                data-is-food="<?php echo $isFood; ?>"
                                data-precio="<?php echo $producto['Precio_Venta']; ?>">
                                <span><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></span>
                                <span class="badge bg-secondary">$<?php echo number_format($producto['Precio_Venta'], 2); ?></span>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>

<script>
// Ensure only one accordion item opens at a time (Bootstrap accordion already enforces this via data-bs-parent)
// Improve UX: scroll opened category into view on small screens
(function(){
    document.querySelectorAll('#<?php echo $accordionId; ?> .accordion-button').forEach(btn => {
        btn.addEventListener('click', function(){
            // Wait for collapse animation to finish then scroll
            const target = document.querySelector(this.getAttribute('data-bs-target'));
            if (!target) return;
            setTimeout(() => {
                if (target.classList.contains('show')) {
                    target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }, 350);
        });
    });
})();
</script>
