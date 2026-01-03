<?php
// reset_admin_passwords.php
// Place this in your project root and run it

session_start();

// Include database connection
require_once __DIR__ . '/core/Database.php';

echo "<h2>Resetting Admin Passwords</h2>";

try {
    $db = \Core\Database::getInstance()->getConnection();
    
    // Delete existing admins first
    echo "<p>Clearing existing admin users...</p>";
    $db->query("DELETE FROM users WHERE role = 'admin'");
    
    // Create 5 admin users with KNOWN passwords
    $admins = [
        ['name' => 'Super Admin', 'email' => 'superadmin@hospital.com', 'password' => 'admin123'],
        ['name' => 'System Admin', 'email' => 'sysadmin@hospital.com', 'password' => 'admin456'],
        ['name' => 'Hospital Director', 'email' => 'director@hospital.com', 'password' => 'hospital123'],
        ['name' => 'IT Manager', 'email' => 'itmanager@hospital.com', 'password' => 'itadmin123'],
        ['name' => 'Finance Admin', 'email' => 'finance@hospital.com', 'password' => 'finance123']
    ];
    
    foreach ($admins as $admin) {
        $hash = password_hash($admin['password'], PASSWORD_DEFAULT);
        
        $stmt = $db->prepare("
            INSERT INTO users (name, email, password, role, phone, status) 
            VALUES (?, ?, ?, 'admin', '+201000000001', 'active')
        ");
        
        $result = $stmt->execute([
            $admin['name'],
            $admin['email'],
            $hash
        ]);
        
        echo "<p>";
        if ($result) {
            echo "✅ Created admin: {$admin['email']} with password: {$admin['password']}";
        } else {
            echo "❌ Failed to create: {$admin['email']}";
        }
        echo "</p>";
    }
    
    // Verify the passwords work
    echo "<h3>Verifying Passwords:</h3>";
    foreach ($admins as $admin) {
        $stmt = $db->prepare("SELECT password FROM users WHERE email = ?");
        $stmt->execute([$admin['email']]);
        $user = $stmt->fetch();
        
        if ($user) {
            $isValid = password_verify($admin['password'], $user['password']);
            echo "<p>{$admin['email']}: " . ($isValid ? "✅ Password VERIFIED" : "❌ Password INVALID") . "</p>";
        }
    }
    
    // Show all admins
    echo "<h3>All Admin Users in Database:</h3>";
    $stmt = $db->prepare("SELECT id, name, email, status FROM users WHERE role = 'admin'");
    $stmt->execute();
    $allAdmins = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>";
    
    foreach ($allAdmins as $admin) {
        echo "<tr>
            <td>{$admin['id']}</td>
            <td>{$admin['name']}</td>
            <td>{$admin['email']}</td>
            <td>{$admin['status']}</td>
        </tr>";
    }
    echo "</table>";
    
    echo "<h3>Test Login Credentials:</h3>";
    echo "<ul>";
    foreach ($admins as $admin) {
        echo "<li><strong>Email:</strong> {$admin['email']} | <strong>Password:</strong> {$admin['password']}</li>";
    }
    echo "</ul>";
    
    echo '<p><a href="public/index.php?page=login">Go to Login Page</a></p>';
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}
?>