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
    foreach ($productosPorCategoria as $categoria => $items): ?>
        <div class="mb-3">
            <h5 class="text-primary"><?php echo htmlspecialchars($categoria); ?></h5>
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
    <?php endforeach; ?>
</div>
