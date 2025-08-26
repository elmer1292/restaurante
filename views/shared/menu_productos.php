<?php
require_once __DIR__ . '/../../models/ProductModel.php';
$productModel = new ProductModel();
// ...existing code...
require_once dirname(__DIR__, 2) . '/helpers/Pagination.php';
$params = getPageParams($_GET, 50);
$productos = $productModel->getAllProducts($params['offset'], $params['limit']);
$totalProductos = $productModel->getTotalProducts();
$totalPages = isset($totalProductos) && $params['limit'] > 0 ? ceil($totalProductos / $params['limit']) : 1;
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

<!-- Controles de paginación -->
<nav aria-label="Paginación de productos">
    <ul class="pagination">
        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?php echo $i == $params['page'] ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>&limit=<?php echo $params['limit']; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>
