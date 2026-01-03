<?php
// debug_routing.php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>🔧 Debug Routing Issues</h2>";

// Check if we can access the route directly
echo "<h3>1. Testing Direct Route Access</h3>";

$testUrls = [
    'public/index.php?page=admin-add-doctor',
    'public/index.php?page=admin-dashboard',
    'public/index.php?page=admin-doctors'
];

foreach ($testUrls as $url) {
    echo "<p><a href='$url' target='_blank'>Test: $url</a></p>";
}

// Check current directory
echo "<h3>2. Current Directory Structure</h3>";
echo "<p>Current directory: " . __DIR__ . "</p>";

// Check if files exist
echo "<h3>3. Required Files Check</h3>";
$files = [
    'public/index.php' => file_exists(__DIR__ . '/public/index.php'),
    'controllers/AdminController.php' => file_exists(__DIR__ . '/controllers/AdminController.php'),
    'views/admin/add-doctor.php' => file_exists(__DIR__ . '/views/admin/add-doctor.php'),
    'core/Auth.php' => file_exists(__DIR__ . '/core/Auth.php'),
    'core/Controller.php' => file_exists(__DIR__ . '/core/Controller.php'),
    'core/Database.php' => file_exists(__DIR__ . '/core/Database.php'),
];

foreach ($files as $file => $exists) {
    $color = $exists ? 'green' : 'red';
    $icon = $exists ? '✅' : '❌';
    echo "<p style='color: $color;'>$icon $file " . ($exists ? "exists" : "does NOT exist") . "</p>";
}

// Check .htaccess
echo "<h3>4. .htaccess Check</h3>";
$htaccess = file_exists(__DIR__ . '/public/.htaccess');
echo "<p>" . ($htaccess ? "✅" : "❌") . " public/.htaccess " . ($htaccess ? "exists" : "does NOT exist") . "</p>";

if ($htaccess) {
    echo "<pre>" . htmlspecialchars(file_get_contents(__DIR__ . '/public/.htaccess')) . "</pre>";
}

// Check Apache configuration
echo "<h3>5. Server Information</h3>";
echo "<p>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'N/A') . "</p>";
echo "<p>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'N/A') . "</p>";
echo "<p>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'N/A') . "</p>";

// Test form submission
echo "<h3>6. Test Form Submission</h3>";
echo '<form action="public/index.php" method="POST" target="_blank">
    <input type="hidden" name="page" value="admin-add-doctor">
    <input type="hidden" name="test" value="1">
    <input type="hidden" name="name" value="Test Doctor">
    <input type="hidden" name="email" value="test@hospital.com">
    <button type="submit" class="btn btn-primary">Test POST to admin-add-doctor</button>
</form>';

// Check what routes are actually defined
echo "<h3>7. Check Defined Routes</h3>";
echo "<p>Let's look at public/index.php routes...</p>";

if (file_exists(__DIR__ . '/public/index.php')) {
    $indexContent = file_get_contents(__DIR__ . '/public/index.php');
    
    // Extract routes array
    if (preg_match('/\$routes\s*=\s*\[(.*?)\];/s', $indexContent, $matches)) {
        echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
        
        // Check if admin-add-doctor is in routes
        if (strpos($matches[0], "'admin-add-doctor'") !== false) {
            echo "<p style='color: green;'>✅ Route 'admin-add-doctor' found in routes array</p>";
        } else {
            echo "<p style='color: red;'>❌ Route 'admin-add-doctor' NOT found in routes array!</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Could not find routes array in index.php</p>";
    }
}

// Quick fix button
echo "<h3>8. Quick Fix</h3>";
echo '<button onclick="applyQuickFix()" class="btn btn-success">Apply Quick Fix to Routes</button>';

?>

<script>
function applyQuickFix() {
    if (confirm('This will add the missing admin routes to your index.php. Continue?')) {
        window.location.href = 'apply_route_fix.php';
    }
}
</script>