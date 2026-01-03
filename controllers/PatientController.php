<?php
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Models\Appointment;

class PatientController extends Controller {
    
    public function __construct() {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            $this->redirect('?page=login');
        }
        // Optional: Check if user is patient
        // if ($auth->user()->getRole() !== 'patient') {
        //     $this->redirect('?page=login');
        // }
    }
    
    // ✅ ADD THIS MISSING DASHBOARD METHOD
    public function dashboard() {
        $auth = Auth::getInstance();
        $patient = $auth->user();
        
        $db = \Core\Database::getInstance();
        
        // Get upcoming appointments
        $appointments = $db->query("
            SELECT a.*, d.name as doctor_name, d.specialization
            FROM appointments a
            JOIN users d ON a.doctor_id = d.id
            WHERE a.patient_id = ? AND a.date >= CURDATE()
            ORDER BY a.date, a.time
            LIMIT 5
        ", [$patient->getId()]);
        
        // Get notifications
        $notifications = $db->query("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT 5
        ", [$patient->getId()]);
        
        $this->view('patient/dashboard', [
            'appointments' => $appointments,
            'notifications' => $notifications,
            'currentPage' => 'dashboard'
        ]);
    }
    
    public function appointments() {
        $auth = Auth::getInstance();
        $patient = $auth->user();
        
        $db = \Core\Database::getInstance();
        $appointments = $db->query("
            SELECT a.*, d.name as doctor_name
            FROM appointments a
            JOIN users d ON a.doctor_id = d.id
            WHERE a.patient_id = ?
            ORDER BY a.date DESC, a.time DESC
        ", [$patient->getId()]);
        
        // Get success message if exists
        $successMessage = $_SESSION['success_message'] ?? '';
        unset($_SESSION['success_message']);
        
        $this->view('patient/appointments', [
            'appointments' => $appointments,
            'successMessage' => $successMessage,
            'currentPage' => 'appointments'
        ]);
    }
    
    public function book() {
        $db = \Core\Database::getInstance();
        
        // Get all departments (instead of just specializations)
        $departments = $db->query("
            SELECT * FROM departments 
            WHERE id IN (
                SELECT DISTINCT department_id 
                FROM users 
                WHERE role = 'doctor' 
                AND department_id IS NOT NULL
                AND status = 'active'
            )
            ORDER BY name
        ");
        
        $doctors = [];
        $selectedDepartment = '';
        $selectedSpecialization = '';
        
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $auth = Auth::getInstance();
            $patient = $auth->user();
            
            // Check which step we're on
            if (isset($_POST['step']) && $_POST['step'] === 'select_department') {
                // Step 1: User selected department
                $selectedDepartment = $_POST['department_id'] ?? '';
                
                // Get doctors for this department
                $doctors = $db->query("
                    SELECT u.*, d.name as department_name
                    FROM users u
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE u.role = 'doctor' 
                    AND u.department_id = ?
                    AND u.status = 'active'
                    ORDER BY u.name
                ", [$selectedDepartment]);
                
            } elseif (isset($_POST['step']) && $_POST['step'] === 'select_specialization') {
                // Step 2: User selected specialization within department
                $selectedDepartment = $_POST['department_id'] ?? '';
                $selectedSpecialization = $_POST['specialization'] ?? '';
                
                // Get doctors for this department and specialization
                $doctors = $db->query("
                    SELECT u.*, d.name as department_name
                    FROM users u
                    LEFT JOIN departments d ON u.department_id = d.id
                    WHERE u.role = 'doctor' 
                    AND u.department_id = ?
                    AND u.specialization = ?
                    AND u.status = 'active'
                    ORDER BY u.name
                ", [$selectedDepartment, $selectedSpecialization]);
                
            } elseif (isset($_POST['step']) && $_POST['step'] === 'book_appointment') {
                // Step 3: User is booking the appointment
                $selectedDepartment = $_POST['department_id'] ?? '';
                $doctorId = $_POST['doctor_id'] ?? '';
                
                // Validate doctor exists and is active
                $doctor = $db->query("
                    SELECT * FROM users 
                    WHERE id = ? 
                    AND role = 'doctor' 
                    AND status = 'active'
                ", [$doctorId]);
                
                if (empty($doctor)) {
                    $error = "Invalid doctor selection. Please try again.";
                    $this->view('patient/book', [
                        'departments' => $departments,
                        'selectedDepartment' => $selectedDepartment,
                        'selectedSpecialization' => $selectedSpecialization,
                        'doctors' => $doctors,
                        'error' => $error,
                        'currentPage' => 'book'
                    ]);
                    return;
                }
                
                // Create and save appointment
                $appointment = new \Models\Appointment([
                    'patient_id' => $patient->getId(),
                    'doctor_id' => $doctorId,
                    'date' => $_POST['date'],
                    'time' => $_POST['time'],
                    'reason' => $_POST['reason'],
                    'status' => 'pending'
                ]);
                
                $result = $appointment->save();
                
                if ($result) {
                    // Success - show success message then redirect
                    $_SESSION['success_message'] = 'Appointment booked successfully!';
                    $this->redirect('?page=patient-appointments');
                } else {
                    $error = "Failed to book appointment. Please try again.";
                    $this->view('patient/book', [
                        'departments' => $departments,
                        'selectedDepartment' => $selectedDepartment,
                        'selectedSpecialization' => $selectedSpecialization,
                        'doctors' => $doctors,
                        'error' => $error,
                        'currentPage' => 'book'
                    ]);
                }
                return;
            }
        }
        
        // Get specializations for selected department (if any)
        $specializations = [];
        if ($selectedDepartment) {
            $specializations = $db->query("
                SELECT DISTINCT specialization 
                FROM users 
                WHERE role = 'doctor' 
                AND department_id = ?
                AND specialization IS NOT NULL 
                AND specialization != ''
                AND status = 'active'
                ORDER BY specialization
            ", [$selectedDepartment]);
        }
        
        $this->view('patient/book', [
            'departments' => $departments,
            'specializations' => $specializations,
            'doctors' => $doctors,
            'selectedDepartment' => $selectedDepartment,
            'selectedSpecialization' => $selectedSpecialization,
            'currentPage' => 'book'
        ]);
    }
    
    public function profile() {
        $auth = Auth::getInstance();
        $patient = $auth->user();
        
        $this->view('patient/profile', [
            'currentPage' => 'profile',
            'patient' => $patient
        ]);
    }
}
?>