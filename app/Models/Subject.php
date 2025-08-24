<?php

require_once 'app/Core/Model.php';

/**
 * Subject Model
 */
class SubjectModel extends Model {
    protected $table = 'subjects';
    protected $fillable = [
        'subject_code', 'subject_name', 'credits', 'major_id', 
        'semester', 'year_level', 'prerequisite', 'description', 'status'
    ];

    /**
     * Get subjects with major info
     */
    public function getWithMajor() {
        $query = "SELECT s.*, m.name as major_name, m.code as major_code
                  FROM {$this->table} s
                  LEFT JOIN majors m ON s.major_id = m.id
                  ORDER BY m.name, s.year_level, s.semester, s.subject_name";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get subjects by major and year level
     */
    public function getByMajorAndYear($majorId, $yearLevel, $semester = null) {
        $query = "SELECT * FROM {$this->table} WHERE major_id = ? AND year_level = ? AND status = 'active'";
        $params = [$majorId, $yearLevel];

        if ($semester) {
            $query .= " AND semester = ?";
            $params[] = $semester;
        }

        $query .= " ORDER BY semester, subject_name";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if subject code is unique
     */
    public function isCodeUnique($code, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE subject_code = ?";
        $params = [$code];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }
}