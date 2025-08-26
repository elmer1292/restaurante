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
    <?php foreach ($productosPorCategoria as $categoria => $items): ?>
        <div class="mb-3">
            <h5 class="text-primary"><?php echo htmlspecialchars($categoria); ?></h5>
            <div class="list-group">
                <?php foreach ($items as $producto): ?>
                    <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" onclick="agregarProductoMenu(<?php echo $producto['ID_Producto']; ?>, '<?php echo htmlspecialchars($producto['Nombre_Producto']); ?>', <?php echo $producto['Precio_Venta']; ?>)">
                        <span><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></span>
                        <span class="badge bg-secondary">$<?php echo number_format($producto['Precio_Venta'], 2); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>
