<?php
namespace Models;

class Receptionist extends User {
    private $permissions = [
        'add_patient' => true,
        'book_appointment' => true,
        'update_appointment' => true,
        'cancel_appointment' => true,
        'view_appointments' => true
    ];
    
    public function __construct($data = []) {
        parent::__construct($data);
        $this->role = 'receptionist';
    }
    
    public function getDashboardUrl() {
        return '/receptionist/dashboard';
    }
    
    public function canPerform($action) {
        return $this->permissions[$action] ?? false;
    }
    
    // Receptionist-specific methods
    public function addPatient($patientData) {
        $patientData['role'] = 'patient';
        $patient = new Patient($patientData);
        return $patient->save();
    }
    
    public function bookAppointment($appointmentData) {
        $appointment = new Appointment($appointmentData);
        return $appointment->save();
    }
}
?>