<?php
require_once __DIR__ . '/../../config/Session.php';
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
                <p class="mb-1"><?php echo htmlspecialchars($nombreCompleto); ?></p>
                <small><?php echo htmlspecialchars($userRole); ?></small>
            </div>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link text-white <?php echo $currentPage === '' || $currentPage === 'index.php' ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>
            </li>

            <?php if ($userRole === 'Administrador'): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'views/empleados') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/views/empleados/">
                    <i class="bi bi-people"></i>
                    Empleados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'views/productos') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/views/productos/">
                    <i class="bi bi-box"></i>
                    Productos
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($userRole, ['Administrador', 'Mesero'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'views/mesas') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/views/mesas/">
                    <i class="bi bi-grid"></i>
                    Mesas
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'comandas') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/views/comandas/">
                    <i class="bi bi-receipt"></i>
                    Comandas
                </a>
            </li>
            <?php endif; ?>

            <?php if (in_array($userRole, ['Administrador', 'Cajero'])): ?>
            <li class="nav-item">
                <a class="nav-link text-white <?php echo strpos($currentPage, 'ventas') !== false ? 'active' : ''; ?>" 
                   href="<?php echo $baseUrl; ?>/views/ventas/">
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

<style>
.sidebar {
    min-height: 100vh;
    box-shadow: inset -1px 0 0 rgba(0, 0, 0, .1);
}

.sidebar .nav-link {
    margin-bottom: .5rem;
    padding: .5rem 1rem;
    color: rgba(255, 255, 255, .8);
    font-size: 0.9rem;
}

.sidebar .nav-link:hover {
    color: #fff;
    background: rgba(255, 255, 255, .1);
}

.sidebar .nav-link.active {
    color: #fff;
    background: rgba(255, 255, 255, .2);
}

.sidebar .nav-link i {
    margin-right: .5rem;
}
</style>