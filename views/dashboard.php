<?php
require_once 'config/database.php';

// Obtener estadísticas del dashboard
$db = new Database();
$conn = $db->connect();
$stmt = $conn->query('CALL sp_GetDashboardStats()');
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Dashboard</h2>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <span class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:2rem;">
                        <i class="bi bi-grid-1x2"></i>
                    </span>
                </div>
                <div>
                    <h6 class="card-subtitle mb-1 text-muted">Mesas Ocupadas</h6>
                    <h3 class="card-title mb-0"><?php echo $stats['MesasOcupadas']; ?> <span class="text-muted">/ <?php echo $stats['TotalMesas']; ?></span></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <span class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:2rem;">
                        <i class="bi bi-receipt"></i>
                    </span>
                </div>
                <div>
                    <h6 class="card-subtitle mb-1 text-muted">Órdenes Hoy</h6>
                    <h3 class="card-title mb-0"><?php echo $stats['OrdenesHoy']; ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body d-flex align-items-center">
                <div class="me-3">
                    <span class="bg-warning text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:56px;height:56px;font-size:2rem;">
                        <i class="bi bi-cash-coin"></i>
                    </span>
                </div>
                <div>
                    <h6 class="card-subtitle mb-1 text-muted">Ventas Diarias</h6>
                    <h3 class="card-title mb-0">C$<?php echo number_format($stats['VentasDiarias'], 2); ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>
