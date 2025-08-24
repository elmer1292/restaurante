<?php

require_once 'config/autoloader.php';

$errorController = new ErrorController();

$mensaje = 'Ha ocurrido un error';
$tipo = 'error';

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'acceso_denegado':
            $errorController->accessDenied();
            break;
        case 'sesion_expirada':
            $errorController->generalError('Su sesión ha expirado. Por favor, inicie sesión nuevamente.');
            break;
        case 'pagina_no_encontrada':
            $errorController->notFound();
            break;
        case 'error_inesperado':
            $errorController->generalError('Ha ocurrido un error inesperado. Por favor, intente de nuevo más tarde.');
            break;
        default:
            $errorController->generalError($mensaje);
            break;
    }
} else {
    $errorController->generalError($mensaje);
}
