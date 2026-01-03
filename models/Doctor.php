<?php
namespace Models;

class Doctor extends User {
    private $specialization;
    private $departmentId;
    private $permissions = [
        'view_appointments' => true,
        'update_appointment_status' => true,
        'add_medical_notes' => true,
        'view_patient_history' => true
    ];
    
    public static function getBySpecialization($specialization) {
        $db = \Core\Database::getInstance();
        $results = $db->query("
            SELECT * FROM users 
            WHERE role = 'doctor' 
            AND specialization = ?
            AND status = 'active'
            ORDER BY name
        ", [$specialization]);
        
        return $results;
    }
    public function __construct($data = []) {
        parent::__construct($data);
        $this->role = 'doctor';
        $this->specialization = $data['specialization'] ?? '';
        $this->departmentId = $data['department_id'] ?? null;
    }
    
    public function getDashboardUrl() {
        return '/doctor/dashboard';
    }
    
    public function canPerform($action) {
        return $this->permissions[$action] ?? false;
    }
    
    public function getSpecialization() {
        return $this->specialization;
    }
    
    public function getDepartmentId() {
        return $this->departmentId;
    }
}
?>