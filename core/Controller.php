<?php
// core/Controller.php - Ensure isPost() method exists
namespace Core;

class Controller {
    protected function view($view, $data = []) {
        extract($data);
        $viewPath = __DIR__ . "/../views/$view.php";
        
        if (file_exists($viewPath)) {
            require_once $viewPath;
        } else {
            die("View not found: $view");
        }
    }
    
    protected function redirect($url) {
        // If it's already a full URL, use it directly
        if (strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0) {
            header("Location: " . $url);
        } 
        // If it starts with ?, it's a relative URL to current page
        else if (strpos($url, '?') === 0) {
            $baseUrl = $_SERVER['PHP_SELF'];
            header("Location: " . $baseUrl . $url);
        }
        // If it's just a page name
        else {
            $baseUrl = dirname($_SERVER['PHP_SELF']);
            header("Location: " . $baseUrl . "/index.php?page=" . $url);
        }
        exit();
    }
    
    // This method should be called from controllers, not from Auth class
    protected function isPost() {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    // Add helper method to check GET requests
    protected function isGet() {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }
}
?>