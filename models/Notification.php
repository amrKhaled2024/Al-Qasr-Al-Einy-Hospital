<?php
namespace Models;

use Core\Database;

class Notification {
    private $id;
    private $userId;
    private $type;
    private $message;
    private $isRead;
    private $createdAt;
    
    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['user_id'] ?? null;
        $this->type = $data['type'] ?? '';
        $this->message = $data['message'] ?? '';
        $this->isRead = $data['is_read'] ?? false;
        $this->createdAt = $data['created_at'] ?? null;
    }
    
    public function save() {
        $db = Database::getInstance();
        
        if ($this->id) {
            $query = "UPDATE notifications SET is_read = ? WHERE id = ?";
            return $db->execute($query, [$this->isRead, $this->id]);
        } else {
            $query = "INSERT INTO notifications (user_id, type, message) VALUES (?, ?, ?)";
            return $db->execute($query, [$this->userId, $this->type, $this->message]);
        }
    }
    
    public static function getByUser($userId, $unreadOnly = false) {
        $db = Database::getInstance();
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        return $db->query($sql, [$userId]);
    }
    
    public static function markAsRead($notificationId) {
        $db = Database::getInstance();
        return $db->execute("UPDATE notifications SET is_read = 1 WHERE id = ?", [$notificationId]);
    }
}
?>