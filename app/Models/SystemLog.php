<?php

require_once 'app/Core/Model.php';

/**
 * System Log Model
 */
class SystemLogModel extends Model {
    protected $table = 'system_logs';
    protected $fillable = [
        'user_id', 'action', 'table_name', 'record_id', 
        'message', 'level', 'context', 'ip_address', 'user_agent'
    ];

    /**
     * Log an event
     */
    public function logEvent($userId, $action, $tableName, $recordId, $message, $level = 'info', $context = null) {
        $data = [
            'user_id' => $userId,
            'action' => $action,
            'table_name' => $tableName,
            'record_id' => $recordId,
            'message' => $message,
            'level' => $level,
            'context' => $context ? json_encode($context) : null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];

        return $this->create($data);
    }

    /**
     * Get logs with user information
     */
    public function getWithUser($limit = 100, $level = null) {
        $conditions = ['1=1'];
        $params = [];

        if ($level) {
            $conditions[] = 'l.level = ?';
            $params[] = $level;
        }

        $whereClause = implode(' AND ', $conditions);

        $query = "SELECT l.*, 
                         u.username, u.first_name, u.last_name
                  FROM {$this->table} l
                  LEFT JOIN users u ON l.user_id = u.id
                  WHERE {$whereClause}
                  ORDER BY l.created_at DESC
                  LIMIT {$limit}";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get logs by user
     */
    public function getByUser($userId, $limit = 50) {
        $query = "SELECT * FROM {$this->table} 
                  WHERE user_id = ?
                  ORDER BY created_at DESC
                  LIMIT {$limit}";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get logs by table and record
     */
    public function getByRecord($tableName, $recordId, $limit = 20) {
        $query = "SELECT l.*, 
                         u.username, u.first_name, u.last_name
                  FROM {$this->table} l
                  LEFT JOIN users u ON l.user_id = u.id
                  WHERE l.table_name = ? AND l.record_id = ?
                  ORDER BY l.created_at DESC
                  LIMIT {$limit}";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$tableName, $recordId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Clean old logs
     */
    public function cleanOldLogs($daysToKeep = 90) {
        $query = "DELETE FROM {$this->table} 
                  WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY)";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([$daysToKeep]);
    }

    /**
     * Get statistics
     */
    public function getStats() {
        $query = "SELECT 
                    level,
                    COUNT(*) as count,
                    DATE(created_at) as date
                  FROM {$this->table}
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                  GROUP BY level, DATE(created_at)
                  ORDER BY date DESC, level";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}