<?php

require_once 'config/autoloader.php';
require_once 'config/Session.php';
require_once 'config/Router.php';

Session::init();

// Redirigir si no está logueado
if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

// Obtener rol y ruta actual
$userRole = Session::getUserRole();

$currentPage = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$currentPage = str_replace('restaurante/', '', $currentPage);
if ($currentPage === 'index.php' || $currentPage === '' || $currentPage === '/' || $currentPage === 'restaurante') {
    $currentPage = '';
}

// Definir rutas y permisos
$routes = [
    '' => ['view' => 'views/dashboard.php', 'roles' => ['Administrador', 'Mesero', 'Cajero']],
    'empleados' => ['view' => 'views/empleados/index.php', 'roles' => ['Administrador']],
    'productos' => ['view' => 'views/productos/index.php', 'roles' => ['Administrador']],
    'mesas' => ['view' => 'views/mesas/index.php', 'roles' => ['Administrador', 'Mesero', 'Cajero']],
    'comandas' => ['view' => 'views/comandas/index.php', 'roles' => ['Administrador', 'Mesero', 'Cajero']],
    'ventas' => ['view' => 'views/ventas/index.php', 'roles' => ['Administrador', 'Cajero']],
];

// Cargar header
require_once 'views/shared/header.php';

// Mostrar vista según ruta y rol
if (isset($routes[$currentPage])) {
    $route = $routes[$currentPage];
    if (in_array($userRole, $route['roles'])) {
        require_once $route['view'];
    } else {
        // Acceso denegado
        require_once 'error.php';
    }
} else {
    // Página no encontrada
    require_once 'views/error/404.php';
}

// Cargar footer
require_once 'views/shared/footer.php';
