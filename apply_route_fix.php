<?php
// apply_route_fix.php
session_start();

echo "<h2>Applying Route Fix</h2>";

$indexFile = __DIR__ . '/public/index.php';

if (!file_exists($indexFile)) {
    die("<p style='color: red;'>❌ index.php not found at: $indexFile</p>");
}

// Read the current file
$content = file_get_contents($indexFile);

// Check if routes array exists
if (strpos($content, '$routes = [') === false) {
    die("<p style='color: red;'>❌ Could not find routes array in index.php</p>");
}

// Define the admin routes to add
$adminRoutes = [
    "'admin-dashboard' => ['AdminController', 'dashboard'],",
    "'admin-doctors' => ['AdminController', 'doctors'],",
    "'admin-add-doctor' => ['AdminController', 'addDoctor'],",
    "'admin-edit-doctor' => ['AdminController', 'editDoctor'],",
    "'admin-delete-doctor' => ['AdminController', 'deleteDoctor'],",
    "'admin-users' => ['AdminController', 'users'],",
    "'admin-add-user' => ['AdminController', 'addUser'],",
    "'admin-edit-user' => ['AdminController', 'editUser'],",
    "'admin-delete-user' => ['AdminController', 'deleteUser'],",
    "'admin-departments' => ['AdminController', 'departments'],",
    "'admin-add-department' => ['AdminController', 'addDepartment'],",
    "'admin-edit-department' => ['AdminController', 'editDepartment'],",
    "'admin-delete-department' => ['AdminController', 'deleteDepartment'],",
    "'admin-appointments' => ['AdminController', 'appointments'],",
    "'admin-add-appointment' => ['AdminController', 'addAppointment'],",
    "'admin-edit-appointment' => ['AdminController', 'editAppointment'],",
    "'admin-delete-appointment' => ['AdminController', 'deleteAppointment'],"
];

// Create the routes block
$routesBlock = "    // Admin routes\n";
foreach ($adminRoutes as $route) {
    $routesBlock .= "    " . $route . "\n";
}

// Check if admin routes already exist
if (strpos($content, "'admin-add-doctor'") !== false) {
    echo "<p style='color: orange;'>⚠️ Admin routes already exist in index.php</p>";
    
    // Just ensure all routes are there
    $missingRoutes = [];
    foreach ($adminRoutes as $route) {
        $routeName = explode("'", $route)[1];
        if (strpos($content, "'$routeName'") === false) {
            $missingRoutes[] = $route;
        }
    }
    
    if (empty($missingRoutes)) {
        echo "<p style='color: green;'>✅ All admin routes are already present</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Missing " . count($missingRoutes) . " routes</p>";
        
        // Add missing routes before the closing bracket of the routes array
        $pos = strrpos($content, '];');
        if ($pos !== false) {
            $newContent = substr($content, 0, $pos);
            $newContent .= "\n    // Added missing admin routes\n";
            foreach ($missingRoutes as $route) {
                $newContent .= "    " . $route . "\n";
            }
            $newContent .= substr($content, $pos);
            
            file_put_contents($indexFile, $newContent);
            echo "<p style='color: green;'>✅ Added missing routes to index.php</p>";
        }
    }
} else {
    // Insert admin routes before the closing bracket of routes array
    $pos = strrpos($content, '];');
    if ($pos !== false) {
        $newContent = substr($content, 0, $pos);
        $newContent .= "\n" . $routesBlock;
        $newContent .= substr($content, $pos);
        
        // Backup original file
        $backupFile = __DIR__ . '/public/index.php.backup_' . date('Ymd_His');
        copy($indexFile, $backupFile);
        
        // Write new content
        file_put_contents($indexFile, $newContent);
        
        echo "<p style='color: green;'>✅ Added admin routes to index.php</p>";
        echo "<p>Backup saved to: " . basename($backupFile) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ Could not find end of routes array</p>";
    }
}

// Show the updated routes section
echo "<h3>Updated Routes Section:</h3>";
$updatedContent = file_get_contents($indexFile);
if (preg_match('/\$routes\s*=\s*\[(.*?)\];/s', $updatedContent, $matches)) {
    echo "<pre>" . htmlspecialchars($matches[0]) . "</pre>";
}

echo '<p><a href="debug_routing.php">Go back to debug</a></p>';
echo '<p><a href="public/index.php?page=admin-add-doctor">Test admin-add-doctor route</a></p>';
?>