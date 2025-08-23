<?php
require_once 'config/Session.php';

Session::init();

$mensaje = 'Ha ocurrido un error';
$tipo = 'error';

if (isset($_GET['msg'])) {
    switch ($_GET['msg']) {
        case 'acceso_denegado':
            $mensaje = 'Acceso denegado. No tiene permisos para acceder a esta sección.';
            $tipo = 'acceso_denegado';
            break;
        case 'sesion_expirada':
            $mensaje = 'Su sesión ha expirado. Por favor, inicie sesión nuevamente.';
            $tipo = 'sesion';
            break;
        case 'pagina_no_encontrada':
            $mensaje = 'La página solicitada no fue encontrada.';
            $tipo = 'pagina_no_encontrada';
            break;
        case 'error_inesperado':
            $mensaje = 'Ha ocurrido un error inesperado. Por favor, intente de nuevo más tarde.';
            $tipo = 'error';
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - RestBar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
        }
        .error-container {
            text-align: center;
            padding: 2rem;
            max-width: 500px;
        }
        .error-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
        }
        .error-icon.acceso_denegado { color: #dc3545; }
        .error-icon.sesion { color: #ffc107; }
        .error-icon.error { color: #6c757d; } /* Default error color */
        .error-icon.pagina_no_encontrada { color: #0dcaf0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-icon <?php echo $tipo; ?>">
                <?php
                switch ($tipo) {
                    case 'acceso_denegado':
                        echo '<i class="bi bi-shield-lock"></i>';
                        break;
                    case 'sesion':
                        echo '<i class="bi bi-clock-history"></i>';
                        break;
                    case 'pagina_no_encontrada':
                        echo '<i class="bi bi-question-circle"></i>';
                        break;
                    default:
                        echo '<i class="bi bi-exclamation-triangle"></i>';
                }
                ?>
            </div>
            <h2 class="mb-4"><?php echo htmlspecialchars($mensaje); ?></h2>
            <?php if (Session::isLoggedIn()): ?>
                <a href="index.php" class="btn btn-primary">Volver al Inicio</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Iniciar Sesión</a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>