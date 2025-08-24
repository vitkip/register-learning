<?php

require_once 'app/Core/Model.php';

/**
 * Enrollment Model
 */
class EnrollmentModel extends Model {
    protected $table = 'enrollments';
    protected $fillable = [
        'student_id', 'subject_id', 'academic_year_id', 
        'semester', 'enrollment_date', 'status'
    ];

    /**
     * Get enrollments with student and subject info
     */
    public function getWithDetails($filters = []) {
        $conditions = ['1=1'];
        $params = [];

        if (!empty($filters['student_id'])) {
            $conditions[] = "e.student_id = ?";
            $params[] = $filters['student_id'];
        }

        if (!empty($filters['subject_id'])) {
            $conditions[] = "e.subject_id = ?";
            $params[] = $filters['subject_id'];
        }

        if (!empty($filters['academic_year_id'])) {
            $conditions[] = "e.academic_year_id = ?";
            $params[] = $filters['academic_year_id'];
        }

        if (!empty($filters['semester'])) {
            $conditions[] = "e.semester = ?";
            $params[] = $filters['semester'];
        }

        $whereClause = implode(' AND ', $conditions);

        $query = "SELECT e.*, 
                         s.student_id, s.first_name, s.last_name,
                         sub.subject_code, sub.subject_name, sub.credits,
                         ay.year as academic_year
                  FROM {$this->table} e
                  JOIN students s ON e.student_id = s.id
                  JOIN subjects sub ON e.subject_id = sub.id
                  JOIN academic_years ay ON e.academic_year_id = ay.id
                  WHERE {$whereClause}
                  ORDER BY ay.year DESC, e.semester, sub.subject_name";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Check if enrollment already exists
     */
    public function exists($studentId, $subjectId, $academicYearId, $semester) {
        $query = "SELECT COUNT(*) FROM {$this->table} 
                  WHERE student_id = ? AND subject_id = ? AND academic_year_id = ? AND semester = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$studentId, $subjectId, $academicYearId, $semester]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Get student's enrolled subjects for a semester
     */
    public function getStudentEnrollments($studentId, $academicYearId, $semester) {
        $query = "SELECT e.*, sub.subject_code, sub.subject_name, sub.credits
                  FROM {$this->table} e
                  JOIN subjects sub ON e.subject_id = sub.id
                  WHERE e.student_id = ? AND e.academic_year_id = ? AND e.semester = ?
                  ORDER BY sub.subject_name";

        $stmt = $this->db->prepare($query);
        $stmt->execute([$studentId, $academicYearId, $semester]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}