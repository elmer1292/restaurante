<?php

require_once 'config/autoloader.php';
require_once 'config/Session.php';
require_once 'config/Router.php';

Session::init();

$router = new Router();

// Cargar rutas
require_once 'config/routes.php';

// Obtener la URI y limpiarla
$uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$uri = str_replace('restaurante/', '', $uri); // Ajusta 'restaurante/' si tu proyecto está en un subdirectorio
$uri = ($uri === 'index.php' || $uri === '') ? '/' : $uri;

// Redirigir si no está logueado (a menos que esté intentando acceder a la página de login)
if (!Session::isLoggedIn() && $uri !== 'login') {
    header('Location: login'); // Redirigir a la ruta 'login' del router
    exit();
}

// La lógica de autenticación y carga de vistas compartidas se maneja dentro de los controladores a través de BaseController.
// El Router se encarga de despachar la solicitud al controlador y acción correctos.
$router->dispatch($uri);
