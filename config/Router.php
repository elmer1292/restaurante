<?php
class Router {
    protected $routes = [];

    public function add($route, $controller, $action) {
        $this->routes[$route] = ['controller' => $controller, 'action' => $action];
    }

    public function dispatch($uri) {
        if (array_key_exists($uri, $this->routes)) {
            $controllerName = $this->routes[$uri]['controller'];
            $actionName = $this->routes[$uri]['action'];

            $controllerFile = 'controllers/' . $controllerName . '.php';
            if (file_exists($controllerFile)) {
                require_once $controllerFile;
                $controller = new $controllerName();
                $controller->$actionName();
            } else {
                $this->handleNotFound();
            }
        } else {
            $this->handleNotFound();
        }
    }

    private function handleNotFound() {
        // Si la petición es AJAX (fetch), responder con JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'Ruta no encontrada']);
            exit;
        }
        require_once 'controllers/ErrorController.php';
        $errorController = new ErrorController();
        $errorController->notFound();
    }
}
