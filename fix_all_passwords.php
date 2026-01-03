<?php
// fix_all_passwords.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Fixing All User Passwords</h2>";

// Define database constants
if (!defined('DB_HOST')) define('DB_HOST', 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', 'hospital_management');
if (!defined('DB_USER')) define('DB_USER', 'root');
if (!defined('DB_PASS')) define('DB_PASS', '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<p style='color: green;'>✅ Database connected</p>";
    
    // Fix passwords for all users
    $usersToFix = [
        ['doctor@hospital.com', 'doctor123'],
        ['patient@hospital.com', 'patient123'],
        ['reception@hospital.com', 'reception123'],
        ['sarah@hospital.com', 'doctor123'],
        ['omar@hospital.com', 'doctor123'],
        ['fatima@hospital.com', 'doctor123']
    ];
    
    echo "<h3>Fixing User Passwords:</h3>";
    
    foreach ($usersToFix as $user) {
        list($email, $password) = $user;
        
        // Check if user exists
        $stmt = $pdo->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $userData = $stmt->fetch();
        
        if ($userData) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            
            $update = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $update->execute([$hash, $email]);
            
            // Verify
            $verify = password_verify($password, $hash);
            
            echo "<p>{$userData['name']} ({$email}): ";
            echo $verify ? "<span style='color: green;'>✅ Fixed - Password: $password</span>" :
                          "<span style='color: red;'>❌ Failed</span>";
            echo "</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ User not found: $email</p>";
        }
    }
    
    // Show all users with their roles
    echo "<h3>All Users with Fixed Passwords:</h3>";
    
    $stmt = $pdo->query("SELECT id, name, email, role, status FROM users ORDER BY role, id");
    $users = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr style='background-color: #f2f2f2;'>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Test Password</th>
          </tr>";
    
    $passwords = [
        'admin@hospital.com' => 'admin123',
        'doctor@hospital.com' => 'doctor123',
        'patient@hospital.com' => 'patient123',
        'reception@hospital.com' => 'reception123',
        'sarah@hospital.com' => 'doctor123',
        'omar@hospital.com' => 'doctor123',
        'fatima@hospital.com' => 'doctor123'
    ];
    
    foreach ($users as $user) {
        $password = $passwords[$user['email']] ?? 'unknown';
        
        $rowColor = $user['role'] == 'admin' ? '#e8f4fc' : 
                   ($user['role'] == 'doctor' ? '#f0f8ff' : 
                   ($user['role'] == 'patient' ? '#f9f9f9' : '#ffffff'));
        
        echo "<tr style='background-color: $rowColor;'>
                <td>{$user['id']}</td>
                <td>{$user['name']}</td>
                <td>{$user['email']}</td>
                <td>{$user['role']}</td>
                <td>{$user['status']}</td>
                <td>$password</td>
              </tr>";
    }
    echo "</table>";
    
    echo "<h3>Test Login Links:</h3>";
    echo '<table border="1" cellpadding="10" style="border-collapse: collapse; margin: 20px 0;">';
    echo '<tr style="background-color: #e8f4fc;">
            <th>Role</th>
            <th>Email</th>
            <th>Password</th>
            <th>Test</th>
          </tr>';
    
    $testUsers = [
        ['Admin', 'admin@hospital.com', 'admin123'],
        ['Doctor', 'doctor@hospital.com', 'doctor123'],
        ['Patient', 'patient@hospital.com', 'patient123'],
        ['Receptionist', 'reception@hospital.com', 'reception123']
    ];
    
    foreach ($testUsers as $testUser) {
        list($role, $email, $password) = $testUser;
        
        echo "<tr>";
        echo "<td><strong>$role</strong></td>";
        echo "<td>$email</td>";
        echo "<td>$password</td>";
        echo '<td>';
        echo '<form method="POST" action="public/index.php?page=login" target="_blank" style="display: inline;">
                <input type="hidden" name="email" value="' . $email . '">
                <input type="hidden" name="password" value="' . $password . '">
                <input type="hidden" name="role" value="' . strtolower($role) . '">
                <button type="submit" style="padding: 5px 10px; background: #1a6aa2; color: white; border: none; border-radius: 3px; cursor: pointer;">
                    Test Login
                </button>
              </form>';
        echo '</td>';
        echo "</tr>";
    }
    
    echo '</table>';
    
    echo '<h3>Next Steps:</h3>';
    echo '<ol>';
    echo '<li>Replace your <strong>controllers/AuthController.php</strong> with the fixed version above</li>';
    echo '<li>Clear browser cache or use incognito mode</li>';
    echo '<li>Try logging in with admin credentials</li>';
    echo '</ol>';
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database Error: " . $e->getMessage() . "</p>";
}
?>