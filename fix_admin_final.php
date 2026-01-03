<?php
// fix_admin_final.php - Complete solution
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🚀 Fixing Admin Login - Complete Solution</h2>";

// Define database constants if not already defined
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'hospital_management');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');
if (!defined('APP_URL')) define('APP_URL', 'http://localhost/OOP_PROJECT/hospital-management-system');

echo "<h3>Step 1: Testing Database Connection</h3>";

try {
    // Direct PDO connection first
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<p style='color: green;'>✅ Direct database connection successful!</p>";
    
    // Create admin user if doesn't exist
    echo "<h3>Step 2: Checking/Creating Admin User</h3>";
    
    $adminEmail = 'admin@hospital.com';
    $adminPassword = 'admin123';
    
    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        echo "<p>No admin user found. Creating one...</p>";
        
        $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
        
        $insert = $pdo->prepare("
            INSERT INTO users (name, email, password, role, phone, status) 
            VALUES (?, ?, ?, 'admin', '+201000000000', 'active')
        ");
        
        if ($insert->execute(['Main Administrator', $adminEmail, $hash])) {
            echo "<p style='color: green;'>✅ Admin user created successfully!</p>";
            echo "<p>Email: $adminEmail</p>";
            echo "<p>Password: $adminPassword</p>";
        }
    } else {
        echo "<p>Admin user found:</p>";
        echo "<pre>";
        print_r([
            'id' => $admin['id'],
            'name' => $admin['name'],
            'email' => $admin['email'],
            'role' => $admin['role'],
            'status' => $admin['status']
        ]);
        echo "</pre>";
        
        // Update password to ensure it's correct
        $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->execute([$hash, $adminEmail]);
        
        echo "<p style='color: green;'>✅ Admin password reset to: $adminPassword</p>";
    }
    
    // Verify all users in database
    echo "<h3>Step 3: All Users in Database</h3>";
    
    $stmt = $pdo->query("SELECT id, name, email, role, status FROM users ORDER BY role, id");
    $users = $stmt->fetchAll();
    
    if (empty($users)) {
        echo "<p>No users found in database</p>";
    } else {
        echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f2f2f2;'>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
              </tr>";
        
        foreach ($users as $user) {
            $rowColor = $user['role'] == 'admin' ? '#e8f4fc' : 
                       ($user['role'] == 'doctor' ? '#f0f8ff' : 
                       ($user['role'] == 'patient' ? '#f9f9f9' : '#ffffff'));
            
            echo "<tr style='background-color: $rowColor;'>
                    <td>{$user['id']}</td>
                    <td>{$user['name']}</td>
                    <td>{$user['email']}</td>
                    <td>{$user['role']}</td>
                    <td>{$user['status']}</td>
                  </tr>";
        }
        echo "</table>";
    }
    
    // Test password verification
    echo "<h3>Step 4: Testing Password Verification</h3>";
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE email = ?");
    $stmt->execute([$adminEmail]);
    $adminData = $stmt->fetch();
    
    if ($adminData) {
        $isValid = password_verify($adminPassword, $adminData['password']);
        echo "<p>Password verification for '$adminEmail': ";
        echo $isValid ? "<span style='color: green;'>✅ SUCCESS</span>" : 
                        "<span style='color: red;'>❌ FAILED</span>";
        echo "</p>";
        
        if (!$isValid) {
            echo "<p>Generating new hash...</p>";
            $newHash = password_hash($adminPassword, PASSWORD_DEFAULT);
            echo "<p>New hash: " . substr($newHash, 0, 30) . "...</p>";
        }
    }
    
    // Create test users for all roles
    echo "<h3>Step 5: Creating Test Users for All Roles</h3>";
    
    $testUsers = [
        ['name' => 'Test Doctor', 'email' => 'doctor@hospital.com', 'password' => 'doctor123', 'role' => 'doctor'],
        ['name' => 'Test Patient', 'email' => 'patient@hospital.com', 'password' => 'patient123', 'role' => 'patient'],
        ['name' => 'Test Receptionist', 'email' => 'reception@hospital.com', 'password' => 'reception123', 'role' => 'receptionist']
    ];
    
    foreach ($testUsers as $testUser) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$testUser['email']]);
        
        if (!$stmt->fetch()) {
            $hash = password_hash($testUser['password'], PASSWORD_DEFAULT);
            $insert = $pdo->prepare("
                INSERT INTO users (name, email, password, role, status) 
                VALUES (?, ?, ?, ?, 'active')
            ");
            
            if ($insert->execute([$testUser['name'], $testUser['email'], $hash, $testUser['role']])) {
                echo "<p>✅ Created {$testUser['role']}: {$testUser['email']} / {$testUser['password']}</p>";
            }
        } else {
            echo "<p>⏩ {$testUser['role']} {$testUser['email']} already exists</p>";
        }
    }
    
    echo "<h3>Step 6: Testing the Actual Login</h3>";
    
    echo '<div style="background: #f9f9f9; padding: 20px; border-radius: 10px; margin: 20px 0;">';
    echo '<h4>Test Credentials:</h4>';
    echo '<table border="1" cellpadding="10" style="border-collapse: collapse;">';
    echo '<tr style="background-color: #e8f4fc;">
            <th>Role</th>
            <th>Email</th>
            <th>Password</th>
            <th>Test Link</th>
          </tr>';
    
    $allUsers = [
        ['admin', 'admin@hospital.com', 'admin123'],
        ['doctor', 'doctor@hospital.com', 'doctor123'],
        ['patient', 'patient@hospital.com', 'patient123'],
        ['receptionist', 'reception@hospital.com', 'reception123']
    ];
    
    foreach ($allUsers as $user) {
        list($role, $email, $password) = $user;
        
        // Test password
        $stmt = $pdo->prepare("SELECT password, status FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch();
        
        $passwordValid = $userData ? password_verify($password, $userData['password']) : false;
        $statusOk = $userData && $userData['status'] == 'active';
        
        echo "<tr>";
        echo "<td>$role</td>";
        echo "<td>$email</td>";
        echo "<td>$password " . 
             ($passwordValid ? "<span style='color: green;'>✅</span>" : "<span style='color: red;'>❌</span>") .
             "</td>";
        echo '<td>';
        if ($passwordValid && $statusOk) {
            echo '<form method="POST" action="public/index.php?page=login" target="_blank" style="display: inline;">
                    <input type="hidden" name="email" value="' . $email . '">
                    <input type="hidden" name="password" value="' . $password . '">
                    <input type="hidden" name="role" value="' . $role . '">
                    <button type="submit" style="padding: 5px 10px; background: #1a6aa2; color: white; border: none; border-radius: 3px; cursor: pointer;">
                        Test Login
                    </button>
                  </form>';
        } else {
            echo '<span style="color: red;">Cannot test - check credentials</span>';
        }
        echo '</td>';
        echo "</tr>";
    }
    
    echo '</table>';
    echo '</div>';
    
    echo '<h3>Step 7: Quick Manual Test</h3>';
    echo '<p>Try logging in manually:</p>';
    echo '<ol>';
    echo '<li>Go to: <a href="' . APP_URL . '/public/index.php?page=login" target="_blank">Login Page</a></li>';
    echo '<li>Use Email: <strong>admin@hospital.com</strong></li>';
    echo '<li>Use Password: <strong>admin123</strong></li>';
    echo '<li>Select Role: <strong>Admin</strong></li>';
    echo '<li>Click Login</li>';
    echo '</ol>';
    
    echo '<p style="color: blue; font-weight: bold;">If it still doesn\'t work, the issue is in your AuthController.php. Let me help you fix it...</p>';
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
    echo "<p>Common issues:</p>";
    echo "<ol>";
    echo "<li>Database doesn't exist: Run this SQL first:</li>";
    echo "</ol>";
    
    echo "<pre style='background: #333; color: #fff; padding: 10px;'>";
    echo "CREATE DATABASE IF NOT EXISTS hospital_management;
USE hospital_management;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'receptionist', 'doctor', 'patient') NOT NULL,
    phone VARCHAR(20),
    specialization VARCHAR(100),
    department_id INT,
    status ENUM('active', 'inactive', 'on_leave') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);";
    echo "</pre>";
}
?>
