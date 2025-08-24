<?php
class Router {
    private $routes = [];

    public function add($route, $controller) {
        $this->routes[$route] = $controller;
    }

    public function dispatch($uri) {
        if (array_key_exists($uri, $this->routes)) {
            require_once $this->routes[$uri];
        } else {
            // Handle 404 Not Found
            header("HTTP/1.0 404 Not Found");
            require_once 'views/error/404.php';
        }
    }
}
