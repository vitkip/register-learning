<?php

require_once 'app/Core/Model.php';

/**
 * Academic Year Model
 */
class AcademicYearModel extends Model {
    protected $table = 'academic_years';
    protected $fillable = ['year', 'status', 'start_date', 'end_date'];

    /**
     * Get active academic years
     */
    public function getActive() {
        return $this->where('status = ?', ['active']);
    }

    /**
     * Check if year is unique
     */
    public function isYearUnique($year, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE year = ?";
        $params = [$year];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Get current academic year
     */
    public function getCurrent() {
        return $this->first('status = ? AND start_date <= CURDATE() AND end_date >= CURDATE()', ['active']) 
               ?: $this->first('status = ?', ['active']);
    }
}