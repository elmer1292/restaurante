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
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mesas - RestBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .mesa-card {
            cursor: pointer;
            transition: transform 0.2s;
        }
        .mesa-card:hover {
            transform: translateY(-5px);
        }
        .mesa-ocupada {
            background-color: #dc3545 !important;
            color: white;
        }
        .mesa-libre {
            background-color: #198754 !important;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <?php include 'views/shared/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
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
            </main>
        </div>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>