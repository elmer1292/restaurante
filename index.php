<?php
require_once 'config/autoloader.php';
require_once 'config/Session.php';
require_once 'config/database.php';
require_once 'config/Router.php';

Session::init();

if (!Session::isLoggedIn()) {
    header('Location: login.php');
    exit();
}

$router = new Router();

// Define routes
$router->add('/restaurante/', 'views/dashboard.php');
$router->add('/restaurante/empleados', 'views/empleados/index.php');
$router->add('/restaurante/productos', 'views/productos/index.php');
$router->add('/restaurante/mesas', 'views/mesas/index.php');
$router->add('/restaurante/comandas', 'views/comandas/index.php');
$router->add('/restaurante/ventas', 'views/ventas/index.php');

// Dispatch the router
$uri = $_SERVER['REQUEST_URI'];
$router->dispatch($uri);
