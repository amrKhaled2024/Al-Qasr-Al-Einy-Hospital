<?php
namespace Models;

use Core\Database;

abstract class User {
    protected $id;
    protected $name;
    protected $email;
    protected $password;
    protected $phone;
    protected $role;
    protected $status;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->role = $data['role'] ?? '';
        $this->status = $data['status'] ?? 'active';
    }
    
    // Abstract methods
    abstract public function getDashboardUrl();
    abstract public function canPerform($action);
    
    // Getters
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getEmail() { return $this->email; }
    public function getRole() { return $this->role; }
    public function getStatus() { return $this->status; }
    public function getPhone() { return $this->phone; }
    
    // Setters
    public function setName($name) { $this->name = $name; }
    public function setEmail($email) { $this->email = $email; }
    public function setPhone($phone) { $this->phone = $phone; }
    
    public function validatePassword($password) {
        return password_verify($password, $this->password);
    }
    
    public function save() {
        $db = Database::getInstance();
        
        if ($this->id) {
            // Update
            $query = "UPDATE users SET name = ?, email = ?, phone = ?, status = ? WHERE id = ?";
            $result = $db->execute($query, [
                $this->name, $this->email, $this->phone, $this->status, $this->id
            ]);
            return $result !== false;
        } else {
            // Insert
            $query = "INSERT INTO users (name, email, password, phone, role, status) VALUES (?, ?, ?, ?, ?, ?)";
            $newId = $db->insert($query, [
                $this->name, 
                $this->email, 
                $this->password, 
                $this->phone, 
                $this->role, 
                $this->status
            ]);
            
            if ($newId) {
                $this->id = $newId;
                return true;
            }
            return false;
        }
    }
    
    public function toArray() {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'role' => $this->role,
            'status' => $this->status
        ];
    }
}
?>