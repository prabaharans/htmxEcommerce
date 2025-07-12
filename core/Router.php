<?php

class Router {
    private $routes = [];
    
    public function get($path, $handler) {
        $this->addRoute('GET', $path, $handler);
    }
    
    public function post($path, $handler) {
        $this->addRoute('POST', $path, $handler);
    }
    
    private function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }
    
    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];
        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                $pattern = $this->convertPathToPattern($route['path']);
                if (preg_match($pattern, $requestPath, $matches)) {
                    array_shift($matches); // Remove full match
                    $this->callHandler($route['handler'], $matches);
                    return;
                }
            }
        }
        
        // 404 Not Found
        http_response_code(404);
        echo "Page not found";
    }
    
    private function convertPathToPattern($path) {
        $pattern = preg_replace('/\{([^}]+)\}/', '([^/]+)', $path);
        return '#^' . $pattern . '$#';
    }
    
    private function callHandler($handler, $params = []) {
        list($controllerName, $method) = explode('@', $handler);
        
        require_once "controllers/{$controllerName}.php";
        $controller = new $controllerName();
        
        call_user_func_array([$controller, $method], $params);
    }
}
?>
