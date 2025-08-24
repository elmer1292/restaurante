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
        header('Location: ' . $path);
        exit();
    }
}
