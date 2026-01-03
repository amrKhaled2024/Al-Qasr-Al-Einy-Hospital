<?php
// Test password hash
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Password: $password<br>";
echo "Hash: $hash<br>";
echo "Verify: " . (password_verify($password, $hash) ? 'YES' : 'NO');

// Test database connection
try {
    $pdo = new PDO('mysql:host=localhost;dbname=hospital_management', 'root', '');
    echo "<br>Database connection: OK";
} catch (PDOException $e) {
    echo "<br>Database connection failed: " . $e->getMessage();
}
?>