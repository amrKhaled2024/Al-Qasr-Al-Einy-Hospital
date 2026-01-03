<?php
// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Simple autoloader
spl_autoload_register(function($className) {
    $baseDir = __DIR__ . '/../';
    $className = str_replace('\\', '/', $className);
    
    $paths = [
        $baseDir . $className . '.php',
        $baseDir . 'core/' . $className . '.php',
        $baseDir . 'models/' . $className . '.php',
        $baseDir . 'controllers/' . $className . '.php'
    ];
    
    foreach ($paths as $path) {
        if (file_exists($path)) {
            require_once $path;
            return;
        }
    }
});

// Get requested page
$page = $_GET['page'] ?? 'home';

// Simple router
$routes = [
    'home' => ['HomeController', 'index'],
    'cancel-appointment' => ['AppointmentController', 'cancel'],
    'update-appointment-status' => ['AppointmentController', 'updateStatus'],
    
    // Auth Routes
    'login' => ['AuthController', 'login'],
    'register' => ['AuthController', 'register'],
    'logout' => ['AuthController', 'logout'],
    
    // Admin routes
    'admin-dashboard' => ['AdminController', 'dashboard'],
    'admin-doctors' => ['AdminController', 'doctors'],
    'admin-add-doctor' => ['AdminController', 'addDoctor'],
    'admin-edit-doctor' => ['AdminController', 'editDoctor'],
    'admin-delete-doctor' => ['AdminController', 'deleteDoctor'],
    'admin-users' => ['AdminController', 'users'],
    'admin-add-user' => ['AdminController', 'addUser'],
    'admin-edit-user' => ['AdminController', 'editUser'],
    'admin-delete-user' => ['AdminController', 'deleteUser'],
    'admin-departments' => ['AdminController', 'departments'],
    'admin-add-department' => ['AdminController', 'addDepartment'],
    'admin-edit-department' => ['AdminController', 'editDepartment'],
    'admin-delete-department' => ['AdminController', 'deleteDepartment'],
    'admin-appointments' => ['AdminController', 'appointments'],
    'admin-add-appointment' => ['AdminController', 'addAppointment'],
    'admin-edit-appointment' => ['AdminController', 'editAppointment'],
    'admin-delete-appointment' => ['AdminController', 'deleteAppointment'],
    
    // Doctor routes
    'doctor-dashboard' => ['DoctorController', 'dashboard'],
    'doctor-appointments' => ['DoctorController', 'appointments'],
    'doctor-schedule' => ['DoctorController', 'schedule'],
    'doctor-patients' => ['DoctorController', 'patients'],
    'doctor-profile' => ['DoctorController', 'profile'],
    'update-appointment-status' => ['DoctorController', 'updateAppointmentStatus'],

    // Receptionist routes
    'receptionist-dashboard' => ['ReceptionistController', 'dashboard'],
    'receptionist-patients' => ['ReceptionistController', 'patients'],
    'receptionist-appointments' => ['ReceptionistController', 'appointments'],
    'receptionist-doctors' => ['ReceptionistController', 'doctors'],
    'receptionist-edit-appointment' => ['ReceptionistController', 'editAppointment'],
    'receptionist-cancel-appointment' => ['ReceptionistController', 'cancelAppointment'],
    'receptionist-book-for-patient' => ['ReceptionistController', 'bookForPatient'],
    
    // Patient routes
    'patient-dashboard' => ['PatientController', 'dashboard'],
    'patient-appointments' => ['PatientController', 'appointments'],
    'patient-book' => ['PatientController', 'book'],
    'patient-profile' => ['PatientController', 'profile']
];

// Route the request
if (isset($routes[$page])) {
    if (is_callable($routes[$page])) {
        // Call the function directly
        $routes[$page]();
    } else {
        $controllerName = 'Controllers\\' . $routes[$page][0];
        $actionName = $routes[$page][1];
        
        if (class_exists($controllerName)) {
            $controller = new $controllerName();
            
            if (method_exists($controller, $actionName)) {
                $controller->$actionName();
            } else {
                showError("Action '$actionName' not found");
            }
        } else {
            showError("Controller '$controllerName' not found");
        }
    }
} else {
    showError("Page '$page' not found");
}

function showError($message) {
    $errorHtml = <<<HTML
    <!DOCTYPE html>
    <html>
    <head>
        <title>Error - Kasr Al Ainy Hospital</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f5f7fa;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .error-container {
                background-color: white;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                text-align: center;
                max-width: 400px;
            }
            .error-title {
                color: #e74c3c;
                font-size: 24px;
                margin-bottom: 20px;
            }
            .error-message {
                color: #333;
                margin-bottom: 20px;
            }
            .btn {
                display: inline-block;
                padding: 10px 20px;
                background-color: #1a6aa2;
                color: white;
                text-decoration: none;
                border-radius: 5px;
            }
            .btn:hover {
                background-color: #0d4d7a;
            }
        </style>
    </head>
    <body>
        <div class="error-container">
            <div class="error-title">Error</div>
            <div class="error-message">$message</div>
            <a href="?page=home" class="btn">Go to Home</a>
        </div>
    </body>
    </html>
    HTML;
    
    echo $errorHtml;
    exit();
}
?>