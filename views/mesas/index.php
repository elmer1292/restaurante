<?php
require_once 'config/Session.php';

Session::init();
Session::checkRole(['Administrador', 'Mesero', 'Cajero']);

$userRole = isset($_SESSION['user']['Rol']) ? $_SESSION['user']['Rol'] : '';
$mesaModel = new MesaModel();
$mesas = $mesaModel->getAllTables();

$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}

require_once 'views/shared/header.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Gestión de Mesas</h1>
    <?php if ($userRole === 'Administrador'): ?>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#mesaModal">
        <i class="bi bi-plus-circle"></i> Nueva Mesa
    </button>
    <?php endif; ?>
</div>

<?php if ($mensaje): ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <?php echo htmlspecialchars($mensaje); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="row row-cols-1 row-cols-md-3 row-cols-lg-4 g-4">
    <?php foreach ($mesas as $mesa): ?>
    <div class="col">
        <div class="card mesa-card h-100 <?php echo $mesa['Estado'] ? 'mesa-ocupada' : 'mesa-libre'; ?>" 
             data-id="<?php echo $mesa['ID_Mesa']; ?>"
             data-estado="<?php echo $mesa['Estado']; ?>">
            <div class="card-body text-center">
                <h5 class="card-title">Mesa <?php echo htmlspecialchars($mesa['Numero_Mesa']); ?></h5>
                <p class="card-text">
                    Capacidad: <?php echo htmlspecialchars($mesa['Capacidad']); ?> personas<br>
                    Estado: <?php echo $mesa['Estado'] ? 'Ocupada' : 'Libre'; ?>
                </p>
                <div class="btn-group">
                    <?php if ($userRole === 'Administrador'): ?>
                    <button class="btn btn-sm btn-light edit-mesa" 
                            data-id="<?php echo $mesa['ID_Mesa']; ?>"
                            data-numero="<?php echo $mesa['Numero_Mesa']; ?>"
                            data-capacidad="<?php echo $mesa['Capacidad']; ?>">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <?php endif; ?>
                    <button class="btn btn-sm btn-light toggle-estado"
                            data-id="<?php echo $mesa['ID_Mesa']; ?>"
                            data-estado="<?php echo $mesa['Estado']; ?>">
                        <?php echo $mesa['Estado'] ? '<i class="bi bi-unlock"></i>' : '<i class="bi bi-lock"></i>'; ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal para Crear/Editar Mesa -->
<div class="modal fade" id="mesaModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Nueva Mesa</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="mesaForm" action="procesar_mesa.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="id_mesa" id="id_mesa">
                    <div class="mb-3">
                        <label for="numero_mesa" class="form-label">Número de Mesa</label>
                        <input type="number" class="form-control" id="numero_mesa" name="numero_mesa" required>
                    </div>
                    <div class="mb-3">
                        <label for="capacidad" class="form-label">Capacidad</label>
                        <input type="number" class="form-control" id="capacidad" name="capacidad" required>
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
<script>
    $(document).ready(function() {
        $('.edit-mesa').click(function(e) {
            e.stopPropagation();
            $('#modalTitle').text('Editar Mesa');
            $('#id_mesa').val($(this).data('id'));
            $('#numero_mesa').val($(this).data('numero'));
            $('#capacidad').val($(this).data('capacidad'));
            $('#mesaModal').modal('show');
        });

        $('.toggle-estado').click(function(e) {
            e.stopPropagation();
            const idMesa = $(this).data('id');
            const estadoActual = $(this).data('estado');
            const nuevoEstado = estadoActual ? 0 : 1;

            $.post('procesar_mesa.php', {
                action: 'toggle_estado',
                id_mesa: idMesa,
                estado: nuevoEstado
            }, function(response) {
                location.reload();
            });
        });

        $('#mesaModal').on('hidden.bs.modal', function() {
            $('#modalTitle').text('Nueva Mesa');
            $('#mesaForm').trigger('reset');
            $('#id_mesa').val('');
        });

        $('.mesa-card').click(function() {
            const idMesa = $(this).data('id');
            const estado = $(this).data('estado');
            
            if (estado === 0) {
                // Si la mesa está libre, crear nueva comanda
                window.location.href = '../comandas/nueva.php?mesa=' + idMesa;
            } else {
                // Si la mesa está ocupada, agregar productos
                window.location.href = '../comandas/agregar_productos.php?mesa=' + idMesa;
            }
        });
    });
</script>

<?php require_once 'views/shared/footer.php'; ?>