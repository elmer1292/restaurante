<?php
require_once 'config/database.php';

// Obtener estadísticas del dashboard
$db = new Database();
$conn = $db->connect();
$stmt = $conn->query('CALL sp_GetDashboardStats()');
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2 class="mb-4">Dashboard</h2>
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <h3 class="h5">Mesas Ocupadas</h3>
            <p class="h2"><?php echo $stats['MesasOcupadas']; ?>/<?php echo $stats['TotalMesas']; ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h3 class="h5">Órdenes Hoy</h3>
            <p class="h2"><?php echo $stats['OrdenesHoy']; ?></p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <h3 class="h5">Ventas Diarias</h3>
            <p class="h2">$<?php echo number_format($stats['VentasDiarias'], 2); ?></p>
        </div>
    </div>
</div>
