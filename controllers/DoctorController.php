<?php
// controllers/DoctorController.php - COMPLETE VERSION
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Models\Appointment;
use Models\UserFactory;

class DoctorController extends Controller {
    
    public function __construct() {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            $this->redirect('?page=login');
        }
        
        $user = $auth->user();
        if ($user && $user->getRole() !== 'doctor') {
            $_SESSION['error_message'] = 'Access denied. Doctor privileges required.';
            $this->redirect('?page=login');
        }
    }
    
    public function dashboard() {
        $auth = Auth::getInstance();
        $doctor = $auth->user();
        
        $db = \Core\Database::getInstance();
        
        // Get statistics
        $stats = [
            'today_appointments' => $db->query("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND date = CURDATE() AND status != 'cancelled'
            ", [$doctor->getId()])[0]['count'] ?? 0,
            
            'total_appointments' => $db->query("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND status != 'cancelled'
            ", [$doctor->getId()])[0]['count'] ?? 0,
            
            'pending_appointments' => $db->query("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND status = 'pending'
            ", [$doctor->getId()])[0]['count'] ?? 0,
            
            'completed_appointments' => $db->query("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE doctor_id = ? AND status = 'completed'
            ", [$doctor->getId()])[0]['count'] ?? 0
        ];
        
        // Get today's appointments
        $todayAppointments = $db->query("
            SELECT a.*, 
                   p.name as patient_name, 
                   p.phone as patient_phone,
                   p.email as patient_email
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            WHERE a.doctor_id = ? 
            AND a.date = CURDATE()
            AND a.status != 'cancelled'
            ORDER BY a.time
        ", [$doctor->getId()]) ?? [];
        
        // Get upcoming appointments (next 7 days)
        $upcomingAppointments = $db->query("
            SELECT a.*, 
                   p.name as patient_name,
                   DATE_FORMAT(a.date, '%W, %M %d') as formatted_date
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            WHERE a.doctor_id = ? 
            AND a.date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            AND a.status = 'confirmed'
            ORDER BY a.date, a.time
            LIMIT 10
        ", [$doctor->getId()]) ?? [];
        
        // Get doctor's profile info
        $doctorInfo = $db->query("
            SELECT u.*, d.name as department_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE u.id = ?
        ", [$doctor->getId()])[0] ?? [];
        
        $this->view('doctor/dashboard', [
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
            'upcomingAppointments' => $upcomingAppointments,
            'doctorInfo' => $doctorInfo,
            'currentPage' => 'dashboard'
        ]);
    }
    
    public function appointments() {
        $auth = Auth::getInstance();
        $doctor = $auth->user();
        
        $db = \Core\Database::getInstance();
        
        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $date = $_GET['date'] ?? '';
        $search = $_GET['search'] ?? '';
        
        $query = "
            SELECT a.*, 
                   p.name as patient_name,
                   p.phone as patient_phone,
                   p.email as patient_email,
                   DATE_FORMAT(a.date, '%M %d, %Y') as formatted_date,
                   DATE_FORMAT(a.time, '%h:%i %p') as formatted_time
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            WHERE a.doctor_id = ?
        ";
        
        $params = [$doctor->getId()];
        
        if (!empty($status)) {
            $query .= " AND a.status = ?";
            $params[] = $status;
        }
        
        if (!empty($date)) {
            $query .= " AND a.date = ?";
            $params[] = $date;
        }
        
        if (!empty($search)) {
            $query .= " AND (p.name LIKE ? OR a.reason LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $query .= " ORDER BY a.date DESC, a.time DESC";
        
        $appointments = $db->query($query, $params) ?? [];
        
        // Appointment statistics
        $statsQuery = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled
            FROM appointments 
            WHERE doctor_id = ?
        ";
        
        $statsResult = $db->query($statsQuery, [$doctor->getId()])[0] ?? [];
        
        $stats = [
            'total' => $statsResult['total'] ?? 0,
            'pending' => $statsResult['pending'] ?? 0,
            'confirmed' => $statsResult['confirmed'] ?? 0,
            'completed' => $statsResult['completed'] ?? 0,
            'cancelled' => $statsResult['cancelled'] ?? 0
        ];
        
        $this->view('doctor/appointments', [
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
    
    public function updateAppointmentStatus() {
        $auth = Auth::getInstance();
        $doctor = $auth->user();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appointmentId = $_POST['appointment_id'] ?? 0;
            $newStatus = $_POST['status'] ?? '';
            $notes = $_POST['notes'] ?? '';
            
            $db = \Core\Database::getInstance();
            
            // Verify appointment belongs to this doctor
            $appointment = $db->query("
                SELECT * FROM appointments 
                WHERE id = ? AND doctor_id = ?
            ", [$appointmentId, $doctor->getId()])[0] ?? null;
            
            if (!$appointment) {
                $_SESSION['error_message'] = 'Appointment not found or access denied.';
                $this->redirect('?page=doctor-appointments');
                return;
            }
            
            // Update status
            $result = $db->execute("
                UPDATE appointments 
                SET status = ?, doctor_notes = ?, updated_at = NOW()
                WHERE id = ?
            ", [$newStatus, $notes, $appointmentId]);
            
            if ($result) {
                // Create notification for patient
                $db->execute("
                    INSERT INTO notifications (user_id, type, message) 
                    VALUES (?, 'appointment_update', ?)
                ", [
                    $appointment['patient_id'],
                    "Your appointment status has been updated to: " . ucfirst($newStatus)
                ]);
                
                $_SESSION['success_message'] = 'Appointment status updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update appointment.';
            }
            
            $this->redirect('?page=doctor-appointments');
        }
    }
    
    public function schedule() {
        $auth = Auth::getInstance();
        $doctor = $auth->user();
        
        $db = \Core\Database::getInstance();
        
        // Get doctor's schedule
        $schedule = $db->query("
            SELECT * FROM doctor_schedules 
            WHERE doctor_id = ?
            ORDER BY day_of_week, start_time
        ", [$doctor->getId()]) ?? [];
        
        // Get available days
        $days = [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 
            'Friday', 'Saturday', 'Sunday'
        ];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Update schedule
            $dayOfWeek = $_POST['day_of_week'] ?? '';
            $startTime = $_POST['start_time'] ?? '';
            $endTime = $_POST['end_time'] ?? '';
            $maxPatients = $_POST['max_patients'] ?? 20;
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            // Check if schedule exists for this day
            $existing = $db->query("
                SELECT id FROM doctor_schedules 
                WHERE doctor_id = ? AND day_of_week = ?
            ", [$doctor->getId(), $dayOfWeek])[0] ?? null;
            
            if ($existing) {
                // Update existing
                $result = $db->execute("
                    UPDATE doctor_schedules 
                    SET start_time = ?, end_time = ?, max_patients = ?, is_active = ?
                    WHERE id = ?
                ", [$startTime, $endTime, $maxPatients, $isActive, $existing['id']]);
            } else {
                // Create new
                $result = $db->execute("
                    INSERT INTO doctor_schedules (doctor_id, day_of_week, start_time, end_time, max_patients, is_active)
                    VALUES (?, ?, ?, ?, ?, ?)
                ", [$doctor->getId(), $dayOfWeek, $startTime, $endTime, $maxPatients, $isActive]);
            }
            
            if ($result) {
                $_SESSION['success_message'] = 'Schedule updated successfully!';
            } else {
                $_SESSION['error_message'] = 'Failed to update schedule.';
            }
            
            $this->redirect('?page=doctor-schedule');
        }
        
        $this->view('doctor/schedule', [
            'schedule' => $schedule,
            'days' => $days,
            'currentPage' => 'schedule'
        ]);
    }
    
    public function profile() {
        $auth = Auth::getInstance();
        $doctor = $auth->user();
        
        $db = \Core\Database::getInstance();
        
        // Get doctor details
        $doctorDetails = $db->query("
            SELECT u.*, d.name as department_name
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE u.id = ?
        ", [$doctor->getId()])[0] ?? [];
        
        // Get all departments for dropdown
        $departments = $db->query("SELECT * FROM departments ORDER BY name") ?? [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $specialization = trim($_POST['specialization'] ?? '');
            $departmentId = $_POST['department_id'] ?? null;
            $bio = trim($_POST['bio'] ?? '');
            
            // Update doctor info
            $result = $db->execute("
                UPDATE users 
                SET name = ?, phone = ?, specialization = ?, department_id = ?, bio = ?, updated_at = NOW()
                WHERE id = ?
            ", [$name, $phone, $specialization, $departmentId, $bio, $doctor->getId()]);
            
            if ($result) {
                $_SESSION['success_message'] = 'Profile updated successfully!';
                $_SESSION['user_name'] = $name; // Update session name
                $this->redirect('?page=doctor-profile');
            } else {
                $_SESSION['error_message'] = 'Failed to update profile.';
            }
        }
        
        $this->view('doctor/profile', [
            'doctor' => $doctorDetails,
            'departments' => $departments,
            'currentPage' => 'profile'
        ]);
    }
    
    public function patients() {
        $auth = Auth::getInstance();
        $doctor = $auth->user();
        
        $db = \Core\Database::getInstance();
        
        // Get patients who have appointments with this doctor
        $patients = $db->query("
            SELECT DISTINCT 
                   p.id,
                   p.name,
                   p.email,
                   p.phone,
                   p.status,
                   COUNT(a.id) as appointment_count,
                   MAX(a.date) as last_appointment
            FROM users p
            JOIN appointments a ON p.id = a.patient_id
            WHERE a.doctor_id = ?
            AND p.role = 'patient'
            GROUP BY p.id
            ORDER BY p.name
        ", [$doctor->getId()]) ?? [];
        
        $this->view('doctor/patients', [
            'patients' => $patients,
            'currentPage' => 'patients'
        ]);
    }
}
?>