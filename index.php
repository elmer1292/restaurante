<?php
require_once 'config/Session.php';
require_once 'config/database.php';

Session::init();

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$userRole = Session::get('user_role');
echo $userRole;
$userRoleId = Session::get('user_role_id');
$userId= Session::get('user_id');
echo $userRoleId;
$username = Session::get('username');
$nombreCompleto = Session::get('nombre_completo');

// Obtener estadísticas del dashboard
$db = new Database();
$conn = $db->connect();
$stmt = $conn->query('CALL sp_GetDashboardStats()');
$stats = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Restaurante</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            padding-top: 20px;
        }
        .sidebar .nav-link {
            color: white;
            margin-bottom: 5px;
        }
        .sidebar .nav-link:hover {
            background-color: #34495e;
        }
        .sidebar .nav-link.active {
            background-color: #3498db;
        }
        .main-content {
            padding: 20px;
        }
        .stat-card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
                <div class="text-center mb-4">
                    <h4 class="text-white">RestBar</h4>
                </div>
                <div class="text-white text-center mb-4">
                    <p class="mb-1"><?php echo htmlspecialchars($nombreCompleto); ?></p>
                    <small><?php echo htmlspecialchars($userRole); ?></small>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link active" href="index.php">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <?php if ($userRole === 'Administrador'): ?>
                    <a class="nav-link" href="views/empleados/">
                        <i class="bi bi-people"></i> Empleados
                    </a>
                    <a class="nav-link" href="views/productos/">
                        <i class="bi bi-box"></i> Productos
                    </a>
                    <?php endif; ?>
                    <?php if (in_array($userRole, ['Administrador', 'Mesero'])): ?>
                    <a class="nav-link" href="views/mesas/">
                        <i class="bi bi-grid"></i> Mesas
                    </a>
                    <a class="nav-link" href="views/comandas/">
                        <i class="bi bi-receipt"></i> Comandas
                    </a>
                    <?php endif; ?>
                    <?php if (in_array($userRole, ['Administrador', 'Cajero'])): ?>
                    <a class="nav-link" href="views/ventas/">
                        <i class="bi bi-cash-register"></i> Ventas
                    </a>
                    <?php endif; ?>
                    <a class="nav-link" href="logout.php">
                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
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
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>