<?php
namespace Models;

use Core\Observer;

class Patient extends User implements Observer {
    public function __construct($data = []) {
        parent::__construct($data);
        $this->role = 'patient';
    }
    
    public function getDashboardUrl() {
        return '/patient/dashboard';
    }
    
    public function canPerform($action) {
        $patientPermissions = [
            'view_appointments' => true,
            'book_appointment' => true,
            'update_profile' => true
        ];
        return $patientPermissions[$action] ?? false;
    }
    
    // Observer pattern implementation
    public function update($subject, $event) {
        // In a real app, you would send email/SMS notification
        error_log("Patient {$this->id} notified: $event");
        
        // Store notification in database
        $db = \Core\Database::getInstance();
        $db->execute(
            "INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)",
            [$this->id, 'appointment_update', $event]
        );
    }
}
?>