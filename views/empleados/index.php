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
                <td><?php echo htmlspecialchars($empleado['ID_usuario']) ?? '';?></td>
                <td><?php echo htmlspecialchars($empleado['Nombre'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($empleado['Usuario'] ?? ''); ?></td>
                <td><?php echo htmlspecialchars($empleado['Rol'] ?? ''); ?></td>
                <td>
                    <span class="badge <?php echo ($empleado['Estado'] == 1) ? 'bg-success' : 'bg-danger'; ?>">
                        <?php echo ($empleado['Estado'] == 1) ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </td>
                <td>
                        <button class="btn btn-sm btn-warning" onclick="abrirEmpleadoModal(<?php echo htmlspecialchars($empleado['ID_usuario']); ?>)">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <?php if ($empleado['Estado'] == 1): ?>
                            <button class="btn btn-sm btn-danger" onclick="abrirEliminarModal(<?php echo htmlspecialchars($empleado['ID_usuario']); ?>, 0)">
                                <i class="bi bi-trash"></i>
                            </button>
                        <?php else: ?>
                            <button class="btn btn-sm btn-success" onclick="abrirEliminarModal(<?php echo htmlspecialchars($empleado['ID_usuario']); ?>, 1)">
                                <i class="bi bi-check-circle"></i>
                            </button>
                        <?php endif; ?>
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
            <form id="empleadoForm" action="<?php echo BASE_URL; ?>controllers/EmpleadoController.php" method="POST">
                <!-- El helper Csrf.php debe cargarse en el autoloader/controlador, no en la vista -->
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
                            <option value="1">Administrador</option>
                            <option value="2">Mesero</option>
                            <option value="3">Cajero</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo">
                    </div>
                    <div class="mb-3">
                        <label for="telefono" class="form-label">Teléfono</label>
                        <input type="text" class="form-control" id="telefono" name="telefono">
                    </div>
                    <div class="mb-3">
                        <label for="fecha_contratacion" class="form-label">Fecha de contratación</label>
                        <input type="date" class="form-control" id="fecha_contratacion" name="fecha_contratacion">
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

<script src="<?php echo BASE_URL; ?>assets/js/empleados.js"></script>
<script>
// Mover el foco al body al cerrar los modales para evitar el warning de accesibilidad
document.addEventListener('DOMContentLoaded', function() {
    var empleadoModal = document.getElementById('empleadoModal');
    var confirmDeleteModal = document.getElementById('confirmDeleteModal');
    if (empleadoModal) {
        empleadoModal.addEventListener('hidden.bs.modal', function () {
            document.body.focus();
        });
    }
    if (confirmDeleteModal) {
        confirmDeleteModal.addEventListener('hidden.bs.modal', function () {
            document.body.focus();
        });
    }
});
</script>
<!-- Modal de confirmación para eliminar empleado -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmDeleteLabel">Confirmar eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                ¿Está seguro de que desea eliminar este empleado?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Eliminar</button>
            </div>
        </div>
    </div>
</div>