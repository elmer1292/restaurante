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
        require_once 'controllers/ErrorController.php';
        $errorController = new ErrorController();
        $errorController->notFound();
    }
}
