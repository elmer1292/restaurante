<?php
$mesaModel = new MesaModel();
$mesas = $mesaModel->getAllTables();
$totalMesas = $mesaModel->getTotalTables();

$mensaje = '';
if (isset($_SESSION['mensaje'])) {
    $mensaje = $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
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
                        <form method="get" action="<?php echo BASE_URL; ?>mesa" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
                            <input type="hidden" name="id_mesa" value="<?php echo $mesa['ID_Mesa']; ?>">
                            <button type="submit" class="btn btn-sm btn-info">
                                <i class="bi bi-list"></i> Ver Detalle
                            </button>
                        </form>
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
                <?php require_once __DIR__ . '/../../helpers/Csrf.php'; ?>
                <input type="hidden" name="csrf_token" value="<?= Csrf::getToken() ?>">
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
<script src="<?php echo BASE_URL; ?>assets/js/mesas.js"></script>
