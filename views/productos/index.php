<?php
require_once 'config/Session.php';

Session::init();
Session::checkRole(['Administrador']);

$productModel = new ProductModel();
$productos = $productModel->getAllProducts();
$categorias = $productModel->getAllCategories();

$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

?>

<h1 class="h2 mb-4">Gestión de Productos</h1>

<?php if ($mensaje): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($mensaje); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
<?php endif; ?>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Lista de Productos</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productoModal">
            <i class="bi bi-plus"></i> Nuevo Producto
        </button>
    </div>
    <div class="card-body">
        <table id="productosTable" class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Precio Costo</th>
                    <th>Precio Venta</th>
                    <th>Stock</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($producto['ID_Producto']); ?></td>
                    <td><?php echo htmlspecialchars($producto['Nombre_Producto']); ?></td>
                    <td><?php echo htmlspecialchars($producto['Nombre_Categoria']); ?></td>
                    <td>$<?php echo number_format($producto['Precio_Costo'], 2); ?></td>
                    <td>$<?php echo number_format($producto['Precio_Venta'], 2); ?></td>
                    <td><?php echo htmlspecialchars($producto['Stock']); ?></td>
                    <td>
                        <button class="btn btn-sm btn-primary edit-producto" 
                                data-id="<?php echo $producto['ID_Producto']; ?>"
                                data-nombre="<?php echo htmlspecialchars($producto['Nombre_Producto']); ?>"
                                data-categoria="<?php echo $producto['ID_Categoria']; ?>"
                                data-costo="<?php echo $producto['Precio_Costo']; ?>"
                                data-venta="<?php echo $producto['Precio_Venta']; ?>"
                                data-stock="<?php echo $producto['Stock']; ?>">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Crear/Editar Producto -->
<div class="modal fade" id="productoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nuevo Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productoForm" action="procesar_producto.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_producto" id="id_producto">
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    <div class="mb-3">
                        <label for="categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="categoria" name="categoria" required>
                            <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo $categoria['ID_Categoria']; ?>">
                                <?php echo htmlspecialchars($categoria['Nombre_Categoria']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="precio_costo" class="form-label">Precio Costo</label>
                        <input type="number" step="0.01" class="form-control" id="precio_costo" name="precio_costo" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_venta" class="form-label">Precio Venta</label>
                        <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" required>
                    </div>
                    <div class="mb-3">
                        <label for="stock" class="form-label">Stock</label>
                        <input type="number" class="form-control" id="stock" name="stock" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="/restaurante/assets/js/productos.js"></script>
