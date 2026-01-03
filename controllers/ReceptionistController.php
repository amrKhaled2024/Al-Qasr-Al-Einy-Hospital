<?php
// controllers/ReceptionistController.php - COMPLETE VERSION
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Models\UserFactory;
use Models\Appointment;

class ReceptionistController extends Controller {
    
    public function __construct() {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            $this->redirect('?page=login');
        }
        
        $user = $auth->user();
        if ($user && $user->getRole() !== 'receptionist') {
            $_SESSION['error_message'] = 'Access denied. Receptionist privileges required.';
            $this->redirect('?page=login');
        }
    }
    
    public function dashboard() {
        $db = \Core\Database::getInstance();
        
        // Get statistics
        $stats = [
            'today_appointments' => $db->query("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE date = CURDATE()
            ")[0]['count'] ?? 0,
            
            'total_patients' => $db->query("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE role = 'patient' AND status = 'active'
            ")[0]['count'] ?? 0,
            
            'pending_appointments' => $db->query("
                SELECT COUNT(*) as count 
                FROM appointments 
                WHERE status = 'pending'
            ")[0]['count'] ?? 0,
            
            'available_doctors' => $db->query("
                SELECT COUNT(*) as count 
                FROM users 
                WHERE role = 'doctor' AND status = 'active'
            ")[0]['count'] ?? 0
        ];
        
        // Get today's appointments
        $todayAppointments = $db->query("
            SELECT a.*, 
                   p.name as patient_name,
                   p.phone as patient_phone,
                   d.name as doctor_name,
                   d.specialization as doctor_specialization,
                   TIME_FORMAT(a.time, '%h:%i %p') as formatted_time
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            JOIN users d ON a.doctor_id = d.id
            WHERE a.date = CURDATE()
            ORDER BY a.time
        ") ?? [];
        
        // Get recent patients
        $recentPatients = $db->query("
            SELECT * FROM users 
            WHERE role = 'patient' 
            ORDER BY created_at DESC 
            LIMIT 5
        ") ?? [];
        
        $this->view('receptionist/dashboard', [
            'stats' => $stats,
            'todayAppointments' => $todayAppointments,
            'recentPatients' => $recentPatients,
            'currentPage' => 'dashboard'
        ]);
    }
    
    public function patients() {
        $db = \Core\Database::getInstance();
        
        // Get filter parameters
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $query = "SELECT * FROM users WHERE role = 'patient'";
        $params = [];
        
        if (!empty($search)) {
            $query .= " AND (name LIKE ? OR email LIKE ? OR phone LIKE ?)";
            $searchTerm = "%$search%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        if (!empty($status)) {
            $query .= " AND status = ?";
            $params[] = $status;
        }
        
        $query .= " ORDER BY created_at DESC";
        
        $patients = $db->query($query, $params) ?? [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Add new patient
            $name = trim($_POST['name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $address = trim($_POST['address'] ?? '');
            $gender = $_POST['gender'] ?? '';
            $date_of_birth = $_POST['date_of_birth'] ?? '';
            
            // Check if email exists
            $existing = $db->query("SELECT id FROM users WHERE email = ?", [$email]);
            if ($existing) {
                $_SESSION['error_message'] = 'Email already exists.';
                $this->redirect('?page=receptionist-patients');
                return;
            }
            
            // Default password
            $password = 'patient123';
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert patient
            $result = $db->execute("
                INSERT INTO users (name, email, password, phone, role, status) 
                VALUES (?, ?, ?, ?, 'patient', 'active')
            ", [$name, $email, $hashedPassword, $phone]);
            
            if ($result) {
                $patientId = $db->getConnection()->lastInsertId();
                
                // Insert patient details
                $db->execute("
                    INSERT INTO patient_details (user_id, address, gender, date_of_birth) 
                    VALUES (?, ?, ?, ?)
                ", [$patientId, $address, $gender, $date_of_birth]);
                
                $_SESSION['success_message'] = 'Patient registered successfully!';
                $this->redirect('?page=receptionist-patients');
            } else {
                $_SESSION['error_message'] = 'Failed to register patient.';
            }
        }
        
        $this->view('receptionist/patients', [
            'patients' => $patients,
            'currentPage' => 'patients',
            'filters' => [
                'search' => $search,
                'status' => $status
            ]
        ]);
    }
    
    public function appointments() {
        $db = \Core\Database::getInstance();
        
        // Get filter parameters
        $status = $_GET['status'] ?? '';
        $date = $_GET['date'] ?? '';
        $doctor_id = $_GET['doctor_id'] ?? '';
        $patient_id = $_GET['patient_id'] ?? '';
        
        $query = "
            SELECT a.*, 
                   p.name as patient_name,
                   p.phone as patient_phone,
                   d.name as doctor_name,
                   d.specialization as doctor_specialization,
                   DATE_FORMAT(a.date, '%M %d, %Y') as formatted_date,
                   TIME_FORMAT(a.time, '%h:%i %p') as formatted_time
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
        
        if (!empty($doctor_id)) {
            $query .= " AND a.doctor_id = ?";
            $params[] = $doctor_id;
        }
        
        if (!empty($patient_id)) {
            $query .= " AND a.patient_id = ?";
            $params[] = $patient_id;
        }
        
        $query .= " ORDER BY a.date DESC, a.time DESC";
        
        $appointments = $db->query($query, $params) ?? [];
        
        // Get doctors and patients for filters and forms
        $doctors = $db->query("
            SELECT id, name, specialization 
            FROM users 
            WHERE role = 'doctor' AND status = 'active'
            ORDER BY name
        ") ?? [];
        
        $patients = $db->query("
            SELECT id, name, phone 
            FROM users 
            WHERE role = 'patient' AND status = 'active'
            ORDER BY name
        ") ?? [];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['add_appointment'])) {
                // Book new appointment
                $patientId = $_POST['patient_id'] ?? 0;
                $doctorId = $_POST['doctor_id'] ?? 0;
                $date = $_POST['date'] ?? '';
                $time = $_POST['time'] ?? '';
                $reason = trim($_POST['reason'] ?? '');
                $status = $_POST['status'] ?? 'pending';
                
                // Validate
                if (empty($patientId) || empty($doctorId) || empty($date) || empty($time)) {
                    $_SESSION['error_message'] = 'Please fill all required fields.';
                    $this->redirect('?page=receptionist-appointments');
                    return;
                }
                
                // Check if appointment time is available
                $existing = $db->query("
                    SELECT id FROM appointments 
                    WHERE doctor_id = ? AND date = ? AND time = ?
                ", [$doctorId, $date, $time]);
                
                if ($existing) {
                    $_SESSION['error_message'] = 'This time slot is already booked.';
                    $this->redirect('?page=receptionist-appointments');
                    return;
                }
                
                $result = $db->execute("
                    INSERT INTO appointments (patient_id, doctor_id, date, time, reason, status, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ", [
                    $patientId, $doctorId, $date, $time, $reason, $status, $_SESSION['user_id']
                ]);
                
                if ($result) {
                    // Create notification for patient
                    $db->execute("
                        INSERT INTO notifications (user_id, type, message) 
                        VALUES (?, 'appointment_created', ?)
                    ", [
                        $patientId,
                        "New appointment booked for " . date('M d, Y', strtotime($date)) . " at " . date('h:i A', strtotime($time))
                    ]);
                    
                    $_SESSION['success_message'] = 'Appointment booked successfully!';
                } else {
                    $_SESSION['error_message'] = 'Failed to book appointment.';
                }
                
                $this->redirect('?page=receptionist-appointments');
                
            } elseif (isset($_POST['update_appointment'])) {
                // Update appointment status
                $appointmentId = $_POST['appointment_id'] ?? 0;
                $newStatus = $_POST['status'] ?? '';
                
                $result = $db->execute("
                    UPDATE appointments 
                    SET status = ?, updated_at = NOW()
                    WHERE id = ?
                ", [$newStatus, $appointmentId]);
                
                if ($result) {
                    // Get appointment details for notification
                    $appointment = $db->query("
                        SELECT patient_id, date, time 
                        FROM appointments 
                        WHERE id = ?
                    ", [$appointmentId])[0] ?? [];
                    
                    if ($appointment) {
                        $db->execute("
                            INSERT INTO notifications (user_id, type, message) 
                            VALUES (?, 'appointment_update', ?)
                        ", [
                            $appointment['patient_id'],
                            "Your appointment status has been updated to: " . ucfirst($newStatus)
                        ]);
                    }
                    
                    $_SESSION['success_message'] = 'Appointment status updated!';
                } else {
                    $_SESSION['error_message'] = 'Failed to update appointment.';
                }
                
                $this->redirect('?page=receptionist-appointments');
            }
        }
        
        $this->view('receptionist/appointments', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'patients' => $patients,
            'currentPage' => 'appointments',
            'filters' => [
                'status' => $status,
                'date' => $date,
                'doctor_id' => $doctor_id,
                'patient_id' => $patient_id
            ]
        ]);
    }
    
    public function doctors() {
        $db = \Core\Database::getInstance();
        
        $doctors = $db->query("
            SELECT u.*, d.name as department_name,
                   (SELECT COUNT(*) FROM appointments WHERE doctor_id = u.id AND date >= CURDATE()) as upcoming_appointments
            FROM users u
            LEFT JOIN departments d ON u.department_id = d.id
            WHERE u.role = 'doctor'
            ORDER BY u.name
        ") ?? [];
        
        // Get doctor schedules
        foreach ($doctors as &$doctor) {
            $doctor['schedule'] = $db->query("
                SELECT * FROM doctor_schedules 
                WHERE doctor_id = ? AND is_active = 1
                ORDER BY day_of_week
            ", [$doctor['id']]) ?? [];
        }
        
        $this->view('receptionist/doctors', [
            'doctors' => $doctors,
            'currentPage' => 'doctors'
        ]);
    }
    
    public function editAppointment() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? $id;
            $patientId = $_POST['patient_id'] ?? 0;
            $doctorId = $_POST['doctor_id'] ?? 0;
            $date = $_POST['date'] ?? '';
            $time = $_POST['time'] ?? '';
            $reason = trim($_POST['reason'] ?? '');
            $status = $_POST['status'] ?? 'pending';
            
            // Check for time conflict (excluding current appointment)
            $existing = $db->query("
                SELECT id FROM appointments 
                WHERE doctor_id = ? AND date = ? AND time = ? AND id != ?
            ", [$doctorId, $date, $time, $id]);
            
            if ($existing) {
                $_SESSION['error_message'] = 'This time slot is already booked for the selected doctor.';
                $this->redirect('?page=receptionist-edit-appointment&id=' . $id);
                return;
            }
            
            $result = $db->execute("
                UPDATE appointments 
                SET patient_id = ?, doctor_id = ?, date = ?, time = ?, reason = ?, status = ?, updated_at = NOW()
                WHERE id = ?
            ", [$patientId, $doctorId, $date, $time, $reason, $status, $id]);
            
            if ($result) {
                $_SESSION['success_message'] = 'Appointment updated successfully!';
                $this->redirect('?page=receptionist-appointments');
            } else {
                $_SESSION['error_message'] = 'Failed to update appointment.';
                $this->redirect('?page=receptionist-edit-appointment&id=' . $id);
            }
        } else {
            $appointment = $db->query("
                SELECT a.*, 
                       p.name as patient_name,
                       d.name as doctor_name
                FROM appointments a
                JOIN users p ON a.patient_id = p.id
                JOIN users d ON a.doctor_id = d.id
                WHERE a.id = ?
            ", [$id])[0] ?? null;
            
            if (!$appointment) {
                $_SESSION['error_message'] = 'Appointment not found.';
                $this->redirect('?page=receptionist-appointments');
                return;
            }
            
            $doctors = $db->query("
                SELECT id, name, specialization 
                FROM users 
                WHERE role = 'doctor' AND status = 'active'
                ORDER BY name
            ") ?? [];
            
            $patients = $db->query("
                SELECT id, name, phone 
                FROM users 
                WHERE role = 'patient' AND status = 'active'
                ORDER BY name
            ") ?? [];
            
            $this->view('receptionist/edit-appointment', [
                'appointment' => $appointment,
                'doctors' => $doctors,
                'patients' => $patients,
                'currentPage' => 'appointments'
            ]);
        }
    }
    
    public function cancelAppointment() {
        $db = \Core\Database::getInstance();
        $id = $_GET['id'] ?? 0;
        
        $appointment = $db->query("SELECT * FROM appointments WHERE id = ?", [$id])[0] ?? null;
        
        if (!$appointment) {
            $_SESSION['error_message'] = 'Appointment not found.';
            $this->redirect('?page=receptionist-appointments');
            return;
        }
        
        $result = $db->execute("
            UPDATE appointments 
            SET status = 'cancelled', updated_at = NOW()
            WHERE id = ?
        ", [$id]);
        
        if ($result) {
            // Create notification for patient
            $db->execute("
                INSERT INTO notifications (user_id, type, message) 
                VALUES (?, 'appointment_cancelled', ?)
            ", [
                $appointment['patient_id'],
                "Your appointment has been cancelled."
            ]);
            
            $_SESSION['success_message'] = 'Appointment cancelled successfully!';
        } else {
            $_SESSION['error_message'] = 'Failed to cancel appointment.';
        }
        
        $this->redirect('?page=receptionist-appointments');
    }
}
?>