<?php
namespace Controllers;

use Core\Controller;
use Core\Auth;
use Models\Appointment;

class AppointmentController extends Controller {
    
    public function __construct() {
        $auth = Auth::getInstance();
        if (!$auth->check()) {
            $this->redirect('?page=login');
        }
    }
    
    public function cancel() {
        // Check if it's a POST request and has ID
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            
            // Get current user
            $auth = Auth::getInstance();
            $user = $auth->user();
            
            // Find the appointment
            $appointment = Appointment::find($id);
            
            if ($appointment) {
                // Check if user has permission to cancel
                $canCancel = false;
                
                switch ($user->getRole()) {
                    case 'admin':
                        $canCancel = true; // Admin can cancel any appointment
                        break;
                    case 'doctor':
                        // Doctor can only cancel their own appointments
                        $canCancel = ($appointment->getDoctorId() == $user->getId());
                        break;
                    case 'patient':
                        // Patient can only cancel their own appointments
                        $canCancel = ($appointment->getPatientId() == $user->getId());
                        break;
                    case 'receptionist':
                        $canCancel = true; // Receptionist can cancel any appointment
                        break;
                }
                
                if ($canCancel) {
                    // Update status to cancelled
                    $result = $appointment->updateStatus('cancelled');
                    
                    if ($result) {
                        $_SESSION['success_message'] = 'Appointment cancelled successfully!';
                    } else {
                        $_SESSION['error_message'] = 'Failed to cancel appointment.';
                    }
                } else {
                    $_SESSION['error_message'] = 'You do not have permission to cancel this appointment.';
                }
            } else {
                $_SESSION['error_message'] = 'Appointment not found.';
            }
            
            // Redirect back based on user role
            $this->redirectToAppointmentsPage($user->getRole());
            
        } else {
            // Invalid request, redirect to home
            $this->redirect('?page=home');
        }
    }
    
    public function updateStatus() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $status = $_POST['status'] ?? '';
            
            // Validate status
            $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            if (!in_array($status, $validStatuses)) {
                $_SESSION['error_message'] = 'Invalid status.';
                $this->redirect('?page=home');
                return;
            }
            
            $auth = Auth::getInstance();
            $user = $auth->user();
            
            $appointment = Appointment::find($id);
            
            if ($appointment) {
                // Check permissions based on role
                $canUpdate = false;
                
                switch ($user->getRole()) {
                    case 'admin':
                    case 'receptionist':
                        $canUpdate = true;
                        break;
                    case 'doctor':
                        // Doctor can only update their own appointments
                        $canUpdate = ($appointment->getDoctorId() == $user->getId());
                        break;
                }
                
                if ($canUpdate) {
                    $result = $appointment->updateStatus($status);
                    
                    if ($result) {
                        $_SESSION['success_message'] = "Appointment status updated to {$status}.";
                    } else {
                        $_SESSION['error_message'] = 'Failed to update appointment status.';
                    }
                } else {
                    $_SESSION['error_message'] = 'You do not have permission to update this appointment.';
                }
            } else {
                $_SESSION['error_message'] = 'Appointment not found.';
            }
            
            $this->redirectToAppointmentsPage($user->getRole());
        }
    }
    
    private function redirectToAppointmentsPage($role) {
        switch ($role) {
            case 'admin':
                $this->redirect('?page=admin-appointments');
                break;
            case 'doctor':
                $this->redirect('?page=doctor-appointments');
                break;
            case 'receptionist':
                $this->redirect('?page=receptionist-appointments');
                break;
            case 'patient':
                $this->redirect('?page=patient-appointments');
                break;
            default:
                $this->redirect('?page=home');
        }
    }
}
?>