<?php
require_once '../../config/Session.php';
require_once '../../config/database.php';
require_once '../../models/UserModel.php';

Session::init();
if (!Session::isLoggedIn() || Session::getUserRole() !== 'Administrador') {
    header('Location: ../../login.php');
    exit;
}

$userRole = Session::getUserRole();
$mensaje = isset($_GET['mensaje']) ? $_GET['mensaje'] : '';

$userModel = new UserModel();
$empleados = $userModel->getAllUsers();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Empleados - RestBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include '../shared/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
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
                                <td><?php echo htmlspecialchars($empleado['ID_Usuario']); ?></td>
                                <td><?php echo htmlspecialchars($empleado['Nombre']); ?></td>
                                <td><?php echo htmlspecialchars($empleado['Usuario']); ?></td>
                                <td><?php echo htmlspecialchars($empleado['Rol']); ?></td>
                                <td>
                                    <span class="badge <?php echo $empleado['Estado'] ? 'bg-success' : 'bg-danger'; ?>">
                                        <?php echo $empleado['Estado'] ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editarEmpleado(<?php echo $empleado['ID_Usuario']; ?>)">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarEmpleado(<?php echo $empleado['ID_Usuario']; ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal para Agregar/Editar Empleado -->
    <div class="modal fade" id="empleadoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestionar Empleado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="empleadoForm" action="../../controllers/UserController.php" method="POST">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../assets/js/empleados.js"></script>
</body>
</html>