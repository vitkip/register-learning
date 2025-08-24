<?php

require_once 'app/Core/Model.php';

/**
 * Major Model
 */
class MajorModel extends Model {
    protected $table = 'majors';
    protected $fillable = ['name', 'code', 'description', 'status'];

    /**
     * Get active majors
     */
    public function getActive() {
        return $this->where('status = ?', ['active']);
    }

    /**
     * Check if code is unique
     */
    public function isCodeUnique($code, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE code = ?";
        $params = [$code];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Get major with student count
     */
    public function getWithStudentCount() {
        $query = "SELECT m.*, COUNT(s.id) as student_count
                  FROM {$this->table} m
                  LEFT JOIN students s ON m.id = s.major_id
                  GROUP BY m.id
                  ORDER BY m.name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}