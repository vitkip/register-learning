<?php

/**
 * Main Application class
 * Handles routing and application lifecycle
 */
class App {
    private $router;
    private $controller;
    private $method;
    private $params = [];

    public function __construct() {
        $this->router = new Router();
        $this->parseUrl();
        $this->route();
    }

    /**
     * Parse the URL and extract controller, method, and parameters
     */
    private function parseUrl() {
        $url = $_GET['url'] ?? 'dashboard/index';
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $url = explode('/', $url);

        // Set controller (default: dashboard)
        $this->controller = !empty($url[0]) ? $url[0] : 'dashboard';
        
        // Set method (default: index)
        $this->method = !empty($url[1]) ? $url[1] : 'index';
        
        // Set parameters
        $this->params = array_slice($url, 2);
    }

    /**
     * Route to the appropriate controller and method
     */
    private function route() {
        // Convert controller name to proper format
        $controllerName = ucfirst($this->controller) . 'Controller';
        $controllerPath = 'app/Controllers/' . $controllerName . '.php';

        // Check if controller file exists
        if (file_exists($controllerPath)) {
            require_once $controllerPath;
            
            // Check if controller class exists
            if (class_exists($controllerName)) {
                $controllerInstance = new $controllerName();
                
                // Check if method exists
                if (method_exists($controllerInstance, $this->method)) {
                    call_user_func_array([$controllerInstance, $this->method], $this->params);
                } else {
                    $this->notFound();
                }
            } else {
                $this->notFound();
            }
        } else {
            $this->notFound();
        }
    }

    /**
     * Handle 404 errors
     */
    private function notFound() {
        http_response_code(404);
        require_once 'app/Views/layouts/main.php';
        echo '<div class="container mx-auto px-4 py-8 text-center">';
        echo '<h1 class="text-4xl font-bold text-gray-800 mb-4">404 - ໜ້າບໍ່ພົບ</h1>';
        echo '<p class="text-gray-600 mb-8">ຂໍອະໄພ, ໜ້າທີ່ທ່ານຊອກຫາບໍ່ມີຢູ່</p>';
        echo '<a href="' . BASE_URL . '" class="bg-amber-500 text-white px-6 py-3 rounded-lg hover:bg-amber-600 transition-colors">ກັບໜ້າຫຼັກ</a>';
        echo '</div>';
    }
}