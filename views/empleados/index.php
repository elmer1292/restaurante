<?php
require_once 'config/Session.php';
require_once dirname(__DIR__, 2) . '/helpers/Pagination.php';

Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Administrador') {
    header('Location: login.php');
    exit;
}

$userRole = Session::getUserRole();
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';

$userModel = new UserModel();
$params = getPageParams($_GET, 50);
$empleados = $userModel->getAllUsersPaginated($params['offset'], $params['limit']);
$totalEmpleados = $userModel->getTotalUsers();
$totalPages = isset($totalEmpleados) && $params['limit'] > 0 ? ceil($totalEmpleados / $params['limit']) : 1;

?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Empleados</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#empleadoModal">
        <i class="bi bi-plus-circle"></i> Nuevo Empleado
    </button>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($mensaje); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Usuario</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($empleados as $empleado): ?>
            <tr>
                <td><?php echo htmlspecialchars($empleado['ID_usuario']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Usuario']); ?></td>
                <td><?php echo htmlspecialchars($empleado['Rol']); ?></td>
                <td>
                    <span class="badge <?php echo ($empleado['Estado'] == 1) ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo ($empleado['Estado'] == 1) ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarEmpleado(<?php echo htmlspecialchars($empleado['ID_usuario']); ?>)">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarEmpleado(<?php echo htmlspecialchars($empleado['ID_usuario']); ?>)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<!-- Controles de paginación -->
<nav aria-label="Paginación de empleados">
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

<!-- Modal para Agregar/Editar Empleado -->
<div class="modal fade" id="empleadoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Gestionar Empleado</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
            <form id="empleadoForm" action="/restaurante/controllers/EmpleadoController.php" method="POST">
                <?php require_once '../../helpers/Csrf.php'; ?>
                <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
                    <input type="hidden" name="action" value="create">
                    <input type="hidden" name="id" id="empleadoId">
                    
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="password" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted">Dejar en blanco para mantener la contraseña actual al editar</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="rol" class="form-label">Rol</label>
                        <select class="form-select" id="rol" name="rol" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Mesero">Mesero</option>
                            <option value="Cajero">Cajero</option>
                            <option value="Cocina">Cocina</option>
                            <option value="Barra">Barra</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado" required>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="assets\js\empleados.js"></script>