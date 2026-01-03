<?php
namespace Models;

class UserFactory {
    public static function createUser($role, $data = []) {
        // Ensure all required fields have default values
        $defaultData = array_merge([
            'id' => null,
            'name' => '',
            'email' => '',
            'password' => '',
            'phone' => '',
            'status' => 'active'
        ], $data);
        
        switch ($role) {
            case 'admin':
                return new Admin($defaultData);
            case 'receptionist':
                return new Receptionist($defaultData);
            case 'doctor':
                return new Doctor($defaultData);
            case 'patient':
                return new Patient($defaultData);
            default:
                throw new \Exception("Invalid user role: $role");
        }
    }
    
    public static function createFromDatabase($row) {
        if (!isset($row['role']) || empty($row['role'])) {
            throw new \Exception("User role not specified in database row");
        }
        
        $role = $row['role'];
        return self::createUser($role, $row);
    }
    
    public static function getAllRoles() {
        return ['admin', 'receptionist', 'doctor', 'patient'];
    }
}
?>