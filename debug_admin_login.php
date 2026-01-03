<?php
// debug_admin_login.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Admin Login Debug</h2>";

// Include necessary files
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Auth.php';
require_once __DIR__ . '/models/UserFactory.php';

echo "<h3>1. Testing Database Connection</h3>";
try {
    $db = \Core\Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    // Check all admin users
    $stmt = $db->prepare("SELECT * FROM users WHERE role = 'admin'");
    $stmt->execute();
    $admins = $stmt->fetchAll();
    
    if (empty($admins)) {
        echo "<p style='color: red;'>❌ No admin users found in database!</p>";
        
        // Create an admin user
        echo "<p>Creating admin user...</p>";
        $password = 'admin123';
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $insert = $db->prepare("
            INSERT INTO users (name, email, password, role, status) 
            VALUES ('Admin User', 'admin@hospital.com', ?, 'admin', 'active')
        ");
        
        if ($insert->execute([$hash])) {
            echo "<p style='color: green;'>✅ Admin user created!</p>";
            echo "<p>Email: admin@hospital.com</p>";
            echo "<p>Password: admin123</p>";
        }
    } else {
        echo "<p>Found " . count($admins) . " admin users:</p>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Password Hash</th></tr>";
        foreach ($admins as $admin) {
            echo "<tr>";
            echo "<td>{$admin['id']}</td>";
            echo "<td>{$admin['name']}</td>";
            echo "<td>{$admin['email']}</td>";
            echo "<td>{$admin['status']}</td>";
            echo "<td>" . substr($admin['password'], 0, 30) . "...</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}

echo "<h3>2. Testing Auth::login() Method Directly</h3>";
try {
    $auth = Core\Auth::getInstance();
    
    // Test admin login
    $adminEmail = 'admin@hospital.com';
    $adminPassword = 'admin123';
    
    echo "<p>Testing Auth::login('$adminEmail', '$adminPassword')</p>";
    
    $adminUser = $auth->login($adminEmail, $adminPassword);
    
    if ($adminUser) {
        echo "<p style='color: green;'>✅ Auth::login() SUCCESS!</p>";
        echo "<p>User ID: " . $adminUser->getId() . "</p>";
        echo "<p>User Role: " . $adminUser->getRole() . "</p>";
        echo "<p>User Name: " . $adminUser->getName() . "</p>";
        
        // Check session
        echo "<h4>Session Data:</h4>";
        echo "<pre>";
        print_r($_SESSION);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ Auth::login() FAILED for admin!</p>";
        
        // Let's debug why
        echo "<h4>Debugging the failure:</h4>";
        
        // Check if user exists
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$adminEmail]);
        $userData = $stmt->fetch();
        
        if ($userData) {
            echo "<p>✅ User found in database</p>";
            echo "<p>Status: " . $userData['status'] . "</p>";
            
            // Test password verification
            $isValid = password_verify($adminPassword, $userData['password']);
            echo "<p>Password verification: " . ($isValid ? "✅ VALID" : "❌ INVALID") . "</p>";
            
            if (!$isValid) {
                echo "<p>Let's fix the password...</p>";
                $newHash = password_hash($adminPassword, PASSWORD_DEFAULT);
                $update = $db->prepare("UPDATE users SET password = ? WHERE email = ?");
                $update->execute([$newHash, $adminEmail]);
                echo "<p>Password updated. New hash created.</p>";
                
                // Try login again
                $adminUser = $auth->login($adminEmail, $adminPassword);
                if ($adminUser) {
                    echo "<p style='color: green;'>✅ Login works after password fix!</p>";
                }
            }
        } else {
            echo "<p>❌ User not found in database</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Auth error: " . $e->getMessage() . "</p>";
}

echo "<h3>3. Testing Patient Login (for comparison)</h3>";
try {
    // Check if patient exists
    $patientEmail = 'patient@hospital.com';
    $patientPassword = 'patient123';
    
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'patient'");
    $stmt->execute([$patientEmail]);
    $patient = $stmt->fetch();
    
    if ($patient) {
        echo "<p>Patient found: {$patient['name']}</p>";
        
        // Test patient login
        $patientUser = $auth->login($patientEmail, $patientPassword);
        
        if ($patientUser) {
            echo "<p style='color: green;'>✅ Patient login works!</p>";
            echo "<p>This means Auth::login() works for patients but not admins.</p>";
            
            // Logout
            $auth->logout();
        } else {
            echo "<p style='color: red;'>❌ Patient login also fails!</p>";
        }
    } else {
        echo "<p>No patient user found with email: $patientEmail</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Patient test error: " . $e->getMessage() . "</p>";
}

echo "<h3>4. Testing the Actual Login Flow</h3>";
echo '<form method="POST" action="public/index.php?page=login" target="_blank">
    <input type="hidden" name="email" value="admin@hospital.com">
    <input type="hidden" name="password" value="admin123">
    <input type="hidden" name="role" value="admin">
    <button type="submit" style="padding: 10px 20px; background: #1a6aa2; color: white; border: none; border-radius: 5px;">
        Test Admin Login Form
    </button>
</form>';

echo "<h3>5. Check Your AuthController</h3>";
echo "<p>The issue might be in AuthController.php. Let me show you what to check:</p>";
echo "<pre style='background: #f5f5f5; padding: 10px;'>";
echo "Check if AuthController is properly checking the role.\n";
echo "In AuthController::login(), look for this logic:\n\n";
echo "if (\$user) {
    \$actualRole = \$user->getRole();
    
    // This might be the problem - checking if selected role matches
    if (empty(\$selectedRole) || \$selectedRole === \$actualRole) {
        \$this->redirectToDashboard(\$actualRole);
    } else {
        // Shows error about wrong role
        \$auth->logout();
        \$this->view('auth/login', [
            'error' => \"Please select correct role: \" . ucfirst(\$actualRole)
        ]);
    }
}";
echo "</pre>";
?>