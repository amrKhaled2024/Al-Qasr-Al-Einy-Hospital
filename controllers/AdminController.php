<?php
// controllers/AdminController.php - COMPLETE CRUD VERSION
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Models\UserFactory;

class AdminController extends Controller {
    
    public function __construct() {
        $auth = Auth::getInstance();
        $auth->requireRole('admin');
    }
    
    // ============================
    // DASHBOARD
    // ============================
    public function dashboard() {
        $db = \Core\Database::getInstance();
        
        // Get statistics
        $stats = [
            'doctors' => $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'doctor' AND status = 'active'")[0]['count'] ?? 0,
            'patients' => $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'patient' AND status = 'active'")[0]['count'] ?? 0,
            'appointments' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE date = CURDATE()")[0]['count'] ?? 0,
            'departments' => $db->query("SELECT COUNT(*) as count FROM departments")[0]['count'] ?? 0,
            'receptionists' => $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'receptionist' AND status = 'active'")[0]['count'] ?? 0,
            'pending_appointments' => $db->query("SELECT COUNT(*) as count FROM appointments WHERE status = 'pending'")[0]['count'] ?? 0
        ];
        
        // Get recent appointments
        $recentAppointments = $db->query("
            SELECT a.*, 
                   p.name as patient_name,
                   d.name as doctor_name
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            JOIN users d ON a.doctor_id = d.id
            ORDER BY a.created_at DESC
            LIMIT 10
        ") ?? [];
        
        $this->view('admin/dashboard', [
            'stats' => $stats,
            'appointments' => $recentAppointments,
            'currentPage' => 'dashboard'
        ]);
    }
    
    // ============================
    // DOCTORS CRUD
    // ============================
    public function doctors() {
        $db = \Core\Database::getInstance();
        
        $doctors = $db->query("
            SELECT u.*, d.name as department_name 
            FROM users u 
            LEFT JOIN departments d ON u.department_id = d.id 
            WHERE u.role = 'doctor'
            ORDER BY u.name
        ") ?? [];
        
        $departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
        
        $this->view('admin/doctors', [
            'doctors' => $doctors,
            'departments' => $departments,
            'currentPage' => 'doctors'
        ]);
    }
    
    public function addDoctor() {
        $db = \Core\Database::getInstance();
        
        // Get departments for the form
        $departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect form data
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => password_hash($_POST['password'] ?? 'doctor123', PASSWORD_DEFAULT),
                'phone' => trim($_POST['phone'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'department_id' => $_POST['department_id'] ?? null,
                'role' => 'doctor',
                'status' => $_POST['status'] ?? 'active'
            ];
            
            // Validate required fields
            if (empty($data['name']) || empty($data['email']) || empty($data['specialization'])) {
                $_SESSION['error_message'] = 'Name, email, and specialization are required.';
                $this->view('admin/add-doctor', [
                    'departments' => $departments,
                    'formData' => $_POST,
                    'currentPage' => 'doctors'
                ]);
                return;
            }
            
            // Check if email already exists
            $existing = $db->query("SELECT id FROM users WHERE email = ?", [$data['email']]);
            if ($existing) {
                $_SESSION['error_message'] = 'Email already exists.';
                $this->view('admin/add-doctor', [
                    'departments' => $departments,
                    'formData' => $_POST,
                    'currentPage' => 'doctors'
                ]);
                return;
            }
            
            // Insert into database
            $result = $db->execute(
                "INSERT INTO users (name, email, password, phone, specialization, department_id, role, status) 
                VALUES (?, ?, ?, ?, ?, ?, 'doctor', ?)",
                [
                    $data['name'], 
                    $data['email'], 
                    $data['password'], 
                    $data['phone'], 
                    $data['specialization'], 
                    $data['department_id'], 
                    $data['status']
                ]
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Doctor added successfully!';
                header("Location: " . APP_URL . "/public/index.php?page=admin-doctors");
                exit();
            } else {
                $_SESSION['error_message'] = 'Failed to add doctor. Please try again.';
                $this->view('admin/add-doctor', [
                    'departments' => $departments,
                    'formData' => $_POST,
                    'currentPage' => 'doctors'
                ]);
            }
        } else {
            // Show the form
            $this->view('admin/add-doctor', [
                'departments' => $departments,
                'currentPage' => 'doctors'
            ]);
        }
    }
    
    public function editDoctor() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $id;
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'specialization' => trim($_POST['specialization'] ?? ''),
                'department_id' => $_POST['department_id'] ?? null,
                'status' => $_POST['status'] ?? 'active'
            ];
            
            // Check if email belongs to another user
            $existing = $db->query("SELECT id FROM users WHERE email = ? AND id != ?", [$data['email'], $id]);
            if ($existing) {
                $_SESSION['error_message'] = 'Email already belongs to another user.';
                $this->redirect('?page=admin-edit-doctor&id=' . $id);
                return;
            }
            
            // Update query
            $query = "UPDATE users SET 
                     name = ?, email = ?, phone = ?, 
                     specialization = ?, department_id = ?, status = ? 
                     WHERE id = ? AND role = 'doctor'";
            
            $result = $db->execute($query, [
                $data['name'], $data['email'], $data['phone'],
                $data['specialization'], $data['department_id'], $data['status'], $id
            ]);
            
            // Update password if provided
            if (!empty($_POST['password'])) {
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $db->execute("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $id]);
            }
            
            if ($result) {
                $_SESSION['success_message'] = 'Doctor updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update doctor.';
            }
            
            $this->redirect('?page=admin-doctors');
        } else {
            // Show edit form
            $doctor = $db->query("SELECT * FROM users WHERE id = ? AND role = 'doctor'", [$id])[0] ?? null;
            
            if (!$doctor) {
                $_SESSION['error_message'] = 'Doctor not found.';
                $this->redirect('?page=admin-doctors');
                return;
            }
            
            $departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
            
            $this->view('admin/edit-doctor', [
                'doctor' => $doctor,
                'departments' => $departments,
                'currentPage' => 'doctors'
            ]);
        }
    }
    
    public function deleteDoctor() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        // Check if doctor has appointments
        $appointments = $db->query("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ?", [$id])[0]['count'] ?? 0;
        
        if ($appointments > 0) {
            $_SESSION['error_message'] = 'Cannot delete doctor with existing appointments.';
        } else {
            $result = $db->execute("DELETE FROM users WHERE id = ? AND role = 'doctor'", [$id]);
            
            if ($result) {
                $_SESSION['success_message'] = 'Doctor deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete doctor.';
            }
        }
        
        $this->redirect('?page=admin-doctors');
    }
    
    // ============================
    // USERS CRUD (All Users)
    // ============================
    public function users() {
        $db = \Core\Database::getInstance();
        
        // Get filter parameters
        $role = $_GET['role'] ?? '';
        $status = $_GET['status'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $query = "SELECT * FROM users WHERE 1=1";
        $params = [];
        
        if (!empty($role)) {
            $query .= " AND role = ?";
            $params[] = $role;
        }
        
        if (!empty($status)) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        if (!empty($search)) {
            $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $query .= " ORDER BY role, name";
        
        $users = $db->query($query, $params) ?? [];
        
        $this->view('admin/users', [
            'users' => $users,
            'currentPage' => 'users',
            'filters' => [
                'role' => $role,
                'status' => $status,
                'search' => $search
            ]
        ]);
    }
    
    public function addUser() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = \Core\Database::getInstance();
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'password' => password_hash($_POST['password'] ?? 'password123', PASSWORD_DEFAULT),
                'phone' => trim($_POST['phone'] ?? ''),
                'role' => $_POST['role'] ?? 'patient',
                'status' => $_POST['status'] ?? 'active',
                'specialization' => trim($_POST['specialization'] ?? ''),
                'department_id' => $_POST['department_id'] ?? null
            ];
            
            // Validate required fields
            if (empty($data['name']) || empty($data['email'])) {
                $_SESSION['error_message'] = 'Name and email are required.';
                $this->redirect('?page=admin-add-user');
                return;
            }
            
            // Check if email exists
            $existing = $db->query("SELECT id FROM users WHERE email = ?", [$data['email']]);
            if ($existing) {
                $_SESSION['error_message'] = 'Email already exists.';
                $this->redirect('?page=admin-add-user');
                return;
            }
            
            // Prepare query based on role
            if ($data['role'] === 'doctor') {
                $query = "INSERT INTO users (name, email, password, phone, role, status, specialization, department_id) 
                         VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [
                    $data['name'], $data['email'], $data['password'], $data['phone'],
                    $data['role'], $data['status'], $data['specialization'], $data['department_id']
                ];
            } else {
                $query = "INSERT INTO users (name, email, password, phone, role, status) 
                         VALUES (?, ?, ?, ?, ?, ?)";
                $params = [
                    $data['name'], $data['email'], $data['password'], $data['phone'],
                    $data['role'], $data['status']
                ];
            }
            
            $result = $db->execute($query, $params);
            
            if ($result) {
                $_SESSION['success_message'] = ucfirst($data['role']) . ' added successfully!';
                $this->redirect('?page=admin-users');
            } else {
                $_SESSION['error_message'] = 'Failed to add user.';
                $this->redirect('?page=admin-add-user');
            }
        } else {
            // Show add form
            $db = \Core\Database::getInstance();
            $departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
            
            $this->view('admin/add-user', [
                'departments' => $departments,
                'currentPage' => 'users'
            ]);
        }
    }
    
    public function editUser() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $id;
            
            $data = [
                'name' => trim($_POST['name'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'phone' => trim($_POST['phone'] ?? ''),
                'status' => $_POST['status'] ?? 'active',
                'specialization' => trim($_POST['specialization'] ?? ''),
                'department_id' => $_POST['department_id'] ?? null
            ];
            
            // Check email
            $existing = $db->query("SELECT id FROM users WHERE email = ? AND id != ?", [$data['email'], $id]);
            if ($existing) {
                $_SESSION['error_message'] = 'Email already belongs to another user.';
                $this->redirect('?page=admin-edit-user&id=' . $id);
                return;
            }
            
            // Get user role to determine fields
            $user = $db->query("SELECT role FROM users WHERE id = ?", [$id])[0] ?? [];
            
            if ($user['role'] === 'doctor') {
                $query = "UPDATE users SET 
                         name = ?, email = ?, phone = ?, 
                         status = ?, specialization = ?, department_id = ? 
                         WHERE id = ?";
                $params = [
                    $data['name'], $data['email'], $data['phone'],
                    $data['status'], $data['specialization'], $data['department_id'], $id
                ];
            } else {
                $query = "UPDATE users SET 
                         name = ?, email = ?, phone = ?, status = ? 
                         WHERE id = ?";
                $params = [
                    $data['name'], $data['email'], $data['phone'], $data['status'], $id
                ];
            }
            
            $result = $db->execute($query, $params);
            
            // Update password if provided
            if (!empty($_POST['password'])) {
                $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
                $db->execute("UPDATE users SET password = ? WHERE id = ?", [$hashedPassword, $id]);
            }
            
            if ($result) {
                $_SESSION['success_message'] = 'User updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update user.';
            }
            
            $this->redirect('?page=admin-users');
        } else {
            // Show edit form
            $user = $db->query("SELECT * FROM users WHERE id = ?", [$id])[0] ?? null;
            
            if (!$user) {
                $_SESSION['error_message'] = 'User not found.';
                $this->redirect('?page=admin-users');
                return;
            }
            
            $departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
            
            $this->view('admin/edit-user', [
                'user' => $user,
                'departments' => $departments,
                'currentPage' => 'users'
            ]);
        }
    }
    
    public function deleteUser() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        // Prevent deleting yourself
        if ($id == $_SESSION['user_id']) {
            $_SESSION['error_message'] = 'You cannot delete your own account.';
            $this->redirect('?page=admin-users');
            return;
        }
        
        // Get user role for validation
        $user = $db->query("SELECT role FROM users WHERE id = ?", [$id])[0] ?? [];
        
        if (!$user) {
            $_SESSION['error_message'] = 'User not found.';
            $this->redirect('?page=admin-users');
            return;
        }
        
        // Check appointments based on role
        if ($user['role'] === 'doctor') {
            $appointments = $db->query("SELECT COUNT(*) as count FROM appointments WHERE doctor_id = ?", [$id])[0]['count'] ?? 0;
            if ($appointments > 0) {
                $_SESSION['error_message'] = 'Cannot delete doctor with existing appointments.';
                $this->redirect('?page=admin-users');
                return;
            }
        } elseif ($user['role'] === 'patient') {
            $appointments = $db->query("SELECT COUNT(*) as count FROM appointments WHERE patient_id = ?", [$id])[0]['count'] ?? 0;
            if ($appointments > 0) {
                $_SESSION['error_message'] = 'Cannot delete patient with existing appointments.';
                $this->redirect('?page=admin-users');
                return;
            }
        }
        
        $result = $db->execute("DELETE FROM users WHERE id = ?", [$id]);
        
        if ($result) {
            $_SESSION['success_message'] = 'User deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete user.';
        }
        
        $this->redirect('?page=admin-users');
    }
    
    // ============================
    // DEPARTMENTS CRUD
    // ============================
    public function departments() {
        $db = \Core\Database::getInstance();
        
        $departments = $db->query("
            SELECT d.*, 
                   COUNT(u.id) as doctor_count
            FROM departments d
            LEFT JOIN users u ON d.id = u.department_id AND u.role = 'doctor'
            GROUP BY d.id
            ORDER BY d.name
        ") ?? [];
        
        $this->view('admin/departments', [
            'departments' => $departments,
            'currentPage' => 'departments'
        ]);
    }
    
    public function addDepartment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $db = \Core\Database::getInstance();
            
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                $_SESSION['error_message'] = 'Department name is required.';
                $this->redirect('?page=admin-add-department');
                return;
            }
            
            $result = $db->execute(
                "INSERT INTO departments (name, description) VALUES (?, ?)",
                [$name, $description]
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Department added successfully!';
                $this->redirect('?page=admin-departments');
            } else {
                $_SESSION['error_message'] = 'Failed to add department.';
                $this->redirect('?page=admin-add-department');
            }
        } else {
            $this->view('admin/add-department', [
                'currentPage' => 'departments'
            ]);
        }
    }
    
    public function editDepartment() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $id;
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                $_SESSION['error_message'] = 'Department name is required.';
                $this->redirect('?page=admin-edit-department&id=' . $id);
                return;
            }
            
            $result = $db->execute(
                "UPDATE departments SET name = ?, description = ? WHERE id = ?",
                [$name, $description, $id]
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Department updated successfully!';
                $this->redirect('?page=admin-departments');
            } else {
                $_SESSION['error_message'] = 'Failed to update department.';
                $this->redirect('?page=admin-edit-department&id=' . $id);
            }
        } else {
            $department = $db->query("SELECT * FROM departments WHERE id = ?", [$id])[0] ?? null;
            
            if (!$department) {
                $_SESSION['error_message'] = 'Department not found.';
                $this->redirect('?page=admin-departments');
                return;
            }
            
            $this->view('admin/edit-department', [
                'department' => $department,
                'currentPage' => 'departments'
            ]);
        }
    }
    
    public function deleteDepartment() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        // Check if department has doctors
        $doctors = $db->query("SELECT COUNT(*) as count FROM users WHERE department_id = ? AND role = 'doctor'", [$id])[0]['count'] ?? 0;
        
        if ($doctors > 0) {
            $_SESSION['error_message'] = 'Cannot delete department with assigned doctors.';
        } else {
            $result = $db->execute("DELETE FROM departments WHERE id = ?", [$id]);
            
            if ($result) {
                $_SESSION['success_message'] = 'Department deleted successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to delete department.';
            }
        }
        
        $this->redirect('?page=admin-departments');
    }
    
    // ============================
    // APPOINTMENTS CRUD
    // ============================
    public function appointments() {
        $db = \Core\Database::getInstance();
        
        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $date = $_GET['date'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $query = "
            SELECT a.*, 
                p.name as patient_name,
                p.email as patient_email,
                p.phone as patient_phone,
                d.name as doctor_name,
                d.specialization as doctor_specialization
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            JOIN users d ON a.doctor_id = d.id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($status)) {
            $query .= " AND a.status = ?";
            $params[] = $status;
        }
        
        if (!empty($date)) {
            $query .= " AND a.date = ?";
            $params[] = $date;
        }
        
        if (!empty($search)) {
            $query .= " AND (p.name LIKE ? OR d.name LIKE ? OR a.reason LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $query .= " ORDER BY a.date DESC, a.time DESC";
        
        $appointments = $db->query($query, $params) ?? [];
        
        // 🔥 FIXED: Get appointment statistics correctly 🔥
        $statsQuery = "SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
            SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
            SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
        FROM appointments";
        
        // Apply same filters to stats
        $statsParams = [];
        $statsWhere = '';
        
        if (!empty($status)) {
            $statsWhere .= " WHERE status = ?";
            $statsParams[] = $status;
        }
        
        if (!empty($date)) {
            $statsWhere .= (!empty($statsWhere) ? " AND" : " WHERE") . " date = ?";
            $statsParams[] = $date;
        }
        
        if (!empty($search)) {
            // For search, we need to join with users tables
            $statsQuery = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN a.status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN a.status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN a.status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN a.status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            JOIN users d ON a.doctor_id = d.id
            WHERE (p.name LIKE ? OR d.name LIKE ? OR a.reason LIKE ?)";
            
            $searchTerm = "%$search%";
            $statsParams = [$searchTerm, $searchTerm, $searchTerm];
            
            if (!empty($status)) {
                $statsQuery .= " AND a.status = ?";
                $statsParams[] = $status;
            }
            
            if (!empty($date)) {
                $statsQuery .= " AND a.date = ?";
                $statsParams[] = $date;
            }
        } else {
            // No search, use simple query with where clause
            $statsQuery .= $statsWhere;
        }
        
        $statsResult = $db->query($statsQuery, $statsParams);
        
        // Ensure we always have stats, even if query returns nothing
        $stats = [
            'total' => 0,
            'pending' => 0,
            'confirmed' => 0,
            'completed' => 0,
            'cancelled' => 0
        ];
        
        if ($statsResult && count($statsResult) > 0) {
            $stats = [
                'total' => $statsResult[0]['total'] ?? 0,
                'pending' => $statsResult[0]['pending'] ?? 0,
                'confirmed' => $statsResult[0]['confirmed'] ?? 0,
                'completed' => $statsResult[0]['completed'] ?? 0,
                'cancelled' => $statsResult[0]['cancelled'] ?? 0
            ];
        }
        
        $this->view('admin/appointments', [
            'appointments' => $appointments,
            'stats' => $stats,
            'currentPage' => 'appointments',
            'filters' => [
                'status' => $status,
                'date' => $date,
                'search' => $search
            ]
        ]);
    }
    
    public function addAppointment() {
        $db = \Core\Database::getInstance();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'patient_id' => $_POST['patient_id'] ?? 0,
                'doctor_id' => $_POST['doctor_id'] ?? 0,
                'date' => $_POST['date'] ?? '',
                'time' => $_POST['time'] ?? '',
                'reason' => trim($_POST['reason'] ?? ''),
                'status' => $_POST['status'] ?? 'pending'
            ];
            
            // Validate
            if (empty($data['patient_id']) || empty($data['doctor_id']) || empty($data['date']) || empty($data['time'])) {
                $_SESSION['error_message'] = 'Please fill all required fields.';
                $this->redirect('?page=admin-add-appointment');
                return;
            }
            
            $result = $db->execute(
                "INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status) 
                 VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $data['patient_id'], $data['doctor_id'], $data['date'], $data['time'],
                    $data['reason'], $data['status']
                ]
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Appointment added successfully!';
                $this->redirect('?page=admin-appointments');
            } else {
                $_SESSION['error_message'] = 'Failed to add appointment.';
                $this->redirect('?page=admin-add-appointment');
            }
        } else {
            // Get patients and doctors for dropdowns
            $patients = $db->query("SELECT id, name FROM users WHERE role = 'patient' AND status = 'active' ORDER BY name") ?? [];
            $doctors = $db->query("SELECT id, name, specialization FROM users WHERE role = 'doctor' AND status = 'active' ORDER BY name") ?? [];
            
            $this->view('admin/add-appointment', [
                'patients' => $patients,
                'doctors' => $doctors,
                'currentPage' => 'appointments'
            ]);
        }
    }
    
    public function editAppointment() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $id;
            
            $data = [
                'patient_id' => $_POST['patient_id'] ?? 0,
                'doctor_id' => $_POST['doctor_id'] ?? 0,
                'date' => $_POST['date'] ?? '',
                'time' => $_POST['time'] ?? '',
                'reason' => trim($_POST['reason'] ?? ''),
                'status' => $_POST['status'] ?? 'pending'
            ];
            
            $result = $db->execute(
                "UPDATE appointments SET 
                 patient_id = ?, doctor_id = ?, date = ?, time = ?, reason = ?, status = ? 
                 WHERE id = ?",
                [
                    $data['patient_id'], $data['doctor_id'], $data['date'], $data['time'],
                    $data['reason'], $data['status'], $id
                ]
            );
            
            if ($result) {
                $_SESSION['success_message'] = 'Appointment updated successfully!';
                $this->redirect('?page=admin-appointments');
            } else {
                $_SESSION['error_message'] = 'Failed to update appointment.';
                $this->redirect('?page=admin-edit-appointment&id=' . $id);
            }
        } else {
            $appointment = $db->query("SELECT * FROM appointments WHERE id = ?", [$id])[0] ?? null;
            
            if (!$appointment) {
                $_SESSION['error_message'] = 'Appointment not found.';
                $this->redirect('?page=admin-appointments');
                return;
            }
            
            $patients = $db->query("SELECT id, name FROM users WHERE role = 'patient' AND status = 'active' ORDER BY name") ?? [];
            $doctors = $db->query("SELECT id, name, specialization FROM users WHERE role = 'doctor' AND status = 'active' ORDER BY name") ?? [];
            
            $this->view('admin/edit-appointment', [
                'appointment' => $appointment,
                'patients' => $patients,
                'doctors' => $doctors,
                'currentPage' => 'appointments'
            ]);
        }
    }
    
    public function deleteAppointment() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        $result = $db->execute("DELETE FROM appointments WHERE id = ?", [$id]);
        
        if ($result) {
            $_SESSION['success_message'] = 'Appointment deleted successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to delete appointment.';
        }
        
        $this->redirect('?page=admin-appointments');
    }
}
?>