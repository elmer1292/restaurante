<?php
require_once dirname(__DIR__, 2) . '/config/Session.php';
Session::init();
$userRole = Session::get('user_role');
$nombreCompleto = Session::get('nombre_completo');
$currentPage = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$currentPage = str_replace('restaurante/', '', $currentPage);
$baseUrl = '/restaurante';
?>

<nav class="col-md-3 col-lg-2 d-md-block bg-dark sidebar collapse">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h4 class="text-white">RestBar</h4>
            <div class="text-white">
                <p class="mb-1"><?php echo htmlspecialchars($nombreCompleto ?? ''); ?></p>
                <small><?php echo htmlspecialchars($userRole ?? ''); ?></small>
            </div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'user/perfil') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/user/perfil">
                    <i class="bi bi-person-circle"></i>
                    Mi Perfil
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $currentPage === '' || $currentPage === 'index.php' ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <?php if ($userRole === 'Administrador'): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'empleados') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/empleados">
                    <i class="bi bi-people"></i>
                    Empleados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'productos') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/productos">
                    <i class="bi bi-box"></i>
                    Productos
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($userRole, ['Administrador', 'Mesero'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'mesas') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/mesas">
                    <i class="bi bi-grid"></i>
                    Mesas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'comandas') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/comandas">
                    <i class="bi bi-receipt"></i>
                    Comandas
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($userRole, ['Administrador', 'Cajero'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'ventas') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/ventas">
                   <i class="bi bi-cash-coin"></i>
                   Ventas
                </a>
            </li>
            <?php endif; ?>

            <li class="nav-item">
                <a class="nav-link text-white" href="<?php echo $baseUrl; ?>/logout.php">
                    <i class="bi bi-box-arrow-right"></i>
                    Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav>

<link rel="stylesheet" href="/restaurante/assets/css/styles.css">