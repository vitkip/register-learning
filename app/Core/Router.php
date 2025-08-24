<?php

/**
 * Router class for handling URL routing
 */
class Router {
    private $routes = [];

    /**
     * Add a GET route
     */
    public function get($uri, $controller) {
        $this->routes['GET'][$uri] = $controller;
    }

    /**
     * Add a POST route
     */
    public function post($uri, $controller) {
        $this->routes['POST'][$uri] = $controller;
    }

    /**
     * Add a PUT route
     */
    public function put($uri, $controller) {
        $this->routes['PUT'][$uri] = $controller;
    }

    /**
     * Add a DELETE route
     */
    public function delete($uri, $controller) {
        $this->routes['DELETE'][$uri] = $controller;
    }

    /**
     * Handle the current request
     */
    public function dispatch() {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_GET['url'] ?? '/';
        
        if (isset($this->routes[$method][$uri])) {
            $controller = $this->routes[$method][$uri];
            
            if (is_callable($controller)) {
                call_user_func($controller);
            } else {
                // Handle controller@method format
                list($controllerName, $methodName) = explode('@', $controller);
                $controllerPath = 'app/Controllers/' . $controllerName . '.php';
                
                if (file_exists($controllerPath)) {
                    require_once $controllerPath;
                    $controllerInstance = new $controllerName();
                    
                    if (method_exists($controllerInstance, $methodName)) {
                        call_user_func([$controllerInstance, $methodName]);
                    }
                }
            }
        }
    }
}