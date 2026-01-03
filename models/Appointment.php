<?php
namespace Models;

use Core\Database;
use Core\Subject;

class Appointment implements Subject {
    private $id;
    private $patientId;
    private $doctorId;
    private $date;
    private $time;
    private $reason;
    private $status;
    private $observers = [];
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->patientId = $data['patient_id'] ?? null;
        $this->doctorId = $data['doctor_id'] ?? null;
        $this->date = $data['date'] ?? null;
        $this->time = $data['time'] ?? null;
        $this->reason = $data['reason'] ?? '';
        $this->status = $data['status'] ?? 'pending';
    }
    
    // Observer pattern methods
    public function attach($observer) {
        $this->observers[] = $observer;
    }
    
    public function detach($observer) {
        $key = array_search($observer, $this->observers, true);
        if ($key !== false) {
            unset($this->observers[$key]);
        }
    }
    
    public function notify($event = '') {
        foreach ($this->observers as $observer) {
            $observer->update($this, $event);
        }
    }
    
    // Getters
    public function getId() { return $this->id; }
    public function getPatientId() { return $this->patientId; }
    public function getDoctorId() { return $this->doctorId; }
    public function getDate() { return $this->date; }
    public function getTime() { return $this->time; }
    public function getReason() { return $this->reason; }
    public function getStatus() { return $this->status; }
    
    // Business methods
    public function updateStatus($newStatus) {
        $oldStatus = $this->status;
        $this->status = $newStatus;
        
        // Save to database
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        $query = "UPDATE appointments SET status = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $result = $stmt->execute([$newStatus, $this->id]);
        
        if ($result) {
            // Notify observers about status change
            $this->notify("Appointment status changed from $oldStatus to $newStatus");
            return true;
        }
        
        return false;
    }
    
// Update the save() method in Appointment.php
    public function save() {
        $db = Database::getInstance();
        $conn = $db->getConnection();
        
        if ($this->id) {
            // Update existing appointment
            $query = "UPDATE appointments SET 
                    patient_id = ?, doctor_id = ?, date = ?, time = ?, 
                    reason = ?, status = ? WHERE id = ?";
            
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([
                $this->patientId, $this->doctorId, $this->date, $this->time,
                $this->reason, $this->status, $this->id
            ]);
            
            return $result;
        } else {
            // Insert new appointment
            $query = "INSERT INTO appointments 
                    (patient_id, doctor_id, date, time, reason, status) 
                    VALUES (?, ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($query);
            $result = $stmt->execute([
                $this->patientId, $this->doctorId, $this->date, $this->time,
                $this->reason, $this->status
            ]);
            
            if ($result) {
                $this->id = $conn->lastInsertId();
                $this->notify("New appointment created");
                return $this->id; // Return the new ID
            }
            
            return false;
        }
    }
    
    public static function find($id) {
        $db = Database::getInstance();
        $result = $db->query("SELECT * FROM appointments WHERE id = ?", [$id]);
        
        if ($result && count($result) > 0) {
            return new self($result[0]);
        }
        return null;
    }
    
    public static function getAll() {
        $db = Database::getInstance();
        $results = $db->query("
            SELECT a.*, 
                   p.name as patient_name,
                   d.name as doctor_name
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            JOIN users d ON a.doctor_id = d.id
            ORDER BY a.date DESC, a.time DESC
        ");
        
        $appointments = [];
        foreach ($results as $row) {
            $appointments[] = $row;
        }
        return $appointments;
    }
    
    public static function getByDoctor($doctorId) {
        $db = Database::getInstance();
        $results = $db->query("
            SELECT a.*, p.name as patient_name
            FROM appointments a
            JOIN users p ON a.patient_id = p.id
            WHERE a.doctor_id = ?
            ORDER BY a.date, a.time
        ", [$doctorId]);
        
        return $results;
    }
    
    public static function getByPatient($patientId) {
        $db = Database::getInstance();
        $results = $db->query("
            SELECT a.*, d.name as doctor_name
            FROM appointments a
            JOIN users d ON a.doctor_id = d.id
            WHERE a.patient_id = ?
            ORDER BY a.date DESC, a.time DESC
        ", [$patientId]);
        
        return $results;
    }
}
?>