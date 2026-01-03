<?php
namespace Core;

class HospitalSystem {
    private static $instance = null;
    private $auth;
    
    private function __construct() {
        $this->auth = Auth::getInstance();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function authenticateUser($email, $password) {
        return $this->auth->login($email, $password);
    }
    
    public function authorizeUser($requiredRole) {
        $user = $this->auth->user();
        return $user && $user->getRole() === $requiredRole;
    }
    
    public function getCurrentUser() {
        return $this->auth->user();
    }
    
    public function logout() {
        $this->auth->logout();
    }
    
    // Prevent cloning
    private function __clone() {}
    
    // Prevent unserialization
    public function __wakeup() {
        throw new \Exception("Cannot unserialize singleton");
    }
}
?>