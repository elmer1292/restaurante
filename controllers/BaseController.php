<?php

class BaseController {
    protected function render($viewPath, $data = []) {
        // Extraer los datos para que estén disponibles en la vista
        extract($data);

        // Incluir el encabezado común
        require_once 'views/shared/header.php';
        // Incluir la barra lateral común
        require_once 'views/shared/sidebar.php';

        // Incluir la vista específica
        require_once $viewPath;

        // Incluir el pie de página común
        require_once 'views/shared/footer.php';
    }

    protected function redirect($path) {
        // Asegura que todas las redirecciones incluyan la ruta base /restaurante
        $base = '/restaurante';
        // Si el path ya incluye la base, no la duplica
        if (strpos($path, $base) !== 0) {
            $path = $base . $path;
        }
        header('Location: ' . $path);
        exit();
    }
}
