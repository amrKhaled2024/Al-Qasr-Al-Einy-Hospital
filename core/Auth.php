<?php
// core/Auth.php - FIXED VERSION
namespace Core;

use Models\UserFactory;
use \PDO;

class Auth {
    private static $instance = null;
    
    private function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function login($email, $password) {
        $db = Database::getInstance()->getConnection();
        
        // Debug logging
        error_log("Auth::login() called with email: $email");
        
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Debug: Check if user was found
        if ($userData) {
            error_log("User found in database: ID={$userData['id']}, Email={$userData['email']}, Role={$userData['role']}");
        } else {
            error_log("No active user found with email: $email");
        }
        
        if ($userData) {
            // Verify password
            $passwordMatch = password_verify($password, $userData['password']);
            error_log("Password verification result: " . ($passwordMatch ? "MATCH" : "NO MATCH"));
            
            if ($passwordMatch) {
                try {
                    $user = UserFactory::createFromDatabase($userData);
                    
                    $_SESSION['user_id'] = $user->getId();
                    $_SESSION['user_role'] = $user->getRole();
                    $_SESSION['user_name'] = $user->getName();
                    $_SESSION['user_email'] = $user->getEmail();
                    
                    // Debug: Log successful login
                    error_log("Login successful! User ID: " . $user->getId() . ", Role: " . $user->getRole());
                    
                    return $user;
                } catch (\Exception $e) {
                    error_log("Error creating user object: " . $e->getMessage());
                    return false;
                }
            } else {
                error_log("Password doesn't match for email: $email");
                error_log("Input password: $password");
                error_log("Stored hash: " . $userData['password']);
            }
        }
        
        return false;
    }
    
    public function logout() {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    public function check() {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    public function user() {
        if (!$this->check()) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($userData) {
            return UserFactory::createFromDatabase($userData);
        }
        
        return null;
    }
    
    public function requireAuth() {
        if (!$this->check()) {
            $this->redirectToLogin();
        }
    }
    
    public function requireRole($role) {
        $this->requireAuth();
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
            $this->redirectToLogin();
        }
    }
    
    private function redirectToLogin() {
        header("Location: " . APP_URL . "/public/index.php?page=login");
        exit();
    }
}
?>