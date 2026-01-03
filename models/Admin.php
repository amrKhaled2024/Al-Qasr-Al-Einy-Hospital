<?php
namespace Models;

class Admin extends User {
    private $permissions = [
        'manage_users' => true,
        'manage_doctors' => true,
        'view_reports' => true,
        'system_config' => true
    ];
    
    public function __construct($data = []) {
        parent::__construct($data);
        $this->role = 'admin';
    }
    
    public function getDashboardUrl() {
        return '/admin/dashboard';
    }
    
    public function canPerform($action) {
        return $this->permissions[$action] ?? false;
    }
    
    // Admin-specific methods
    public function addUser($userData) {
        // Implementation to add user to database
        $user = UserFactory::createUser($userData['role'], $userData);
        return $user->save();
    }
    
    public function updateUser($userId, $userData) {
        // Update user in database
    }
    
    public function deleteUser($userId) {
        // Delete user from database
    }
    
    public function addDoctor($doctorData) {
        $doctorData['role'] = 'doctor';
        return $this->addUser($doctorData);
    }
}
?>