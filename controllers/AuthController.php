<?php
// controllers/AuthController.php - WORKING VERSION
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Models\UserFactory;

class AuthController extends Controller {
    
    public function login() {
        // If already logged in, redirect to dashboard
        $auth = Auth::getInstance();
        if ($auth->check()) {
            $user = $auth->user();
            $this->redirectToDashboard($user->getRole());
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $selectedRole = $_POST['role'] ?? '';
            
            // Debug logging
            error_log("=== LOGIN ATTEMPT ===");
            error_log("Email: $email");
            error_log("Selected Role: $selectedRole");
            
            if (empty($email) || empty($password)) {
                $this->view('auth/login', ['error' => 'Email and password are required']);
                return;
            }
            
            // Attempt login
            $user = $auth->login($email, $password);
            
            if ($user) {
                $actualRole = $user->getRole();
                error_log("Login SUCCESS - User Role: $actualRole");
                
                // 🔥 REMOVED STRICT ROLE CHECKING 🔥
                // Just redirect to the appropriate dashboard based on actual role
                $this->redirectToDashboard($actualRole);
                
            } else {
                error_log("Login FAILED for: $email");
                $this->view('auth/login', [
                    'error' => 'Invalid email or password. Please check your credentials.'
                ]);
            }
        } else {
            // GET request - show login form
            $this->view('auth/login');
        }
    }
    
    private function redirectToDashboard($role) {
        $dashboardPages = [
            'admin' => 'admin-dashboard',
            'doctor' => 'doctor-dashboard',
            'receptionist' => 'receptionist-dashboard',
            'patient' => 'patient-dashboard'
        ];
        
        $page = $dashboardPages[$role] ?? 'home';
        
        error_log("Redirecting $role to: $page");
        $this->redirect('?page=' . $page);
    }
    
    public function register() {
        $auth = Auth::getInstance();
        if ($auth->check()) {
            $user = $auth->user();
            $this->redirectToDashboard($user->getRole());
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            $phone = trim($_POST['phone'] ?? '');
            
            // Validation
            $errors = [];
            if (empty($name)) $errors[] = "Name is required";
            if (empty($email)) $errors[] = "Email is required";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
            if (empty($password)) $errors[] = "Password is required";
            if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
            if ($password !== $confirmPassword) $errors[] = "Passwords do not match";
            
            if (!empty($errors)) {
                $this->view('auth/register', ['error' => implode('<br>', $errors)]);
                return;
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Create user
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
                'phone' => $phone,
                'role' => 'patient',
                'status' => 'active'
            ];
            
            try {
                $user = UserFactory::createUser('patient', $userData);
                $db = \Core\Database::getInstance();
                
                // Save to database
                $sql = "INSERT INTO users (name, email, password, phone, role, status) 
                        VALUES (?, ?, ?, ?, ?, ?)";
                $db->execute($sql, [
                    $name, $email, $hashedPassword, $phone, 'patient', 'active'
                ]);
                
                // Auto login
                $auth->login($email, $password);
                $this->redirect('?page=patient-dashboard');
                
            } catch (\Exception $e) {
                $this->view('auth/register', ['error' => 'Registration failed: ' . $e->getMessage()]);
            }
        } else {
            $this->view('auth/register');
        }
    }
    
    public function logout() {
        $auth = Auth::getInstance();
        $auth->logout();
        $this->redirect('?page=login');
    }
}
?>