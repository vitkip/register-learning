<?php

require_once 'app/Core/Model.php';

/**
 * Student Model
 */
class StudentModel extends Model {
    protected $table = 'students';
    protected $fillable = [
        'student_id', 'first_name', 'last_name', 'gender', 'dob',
        'email', 'phone', 'village', 'district', 'province',
        'accommodation_type', 'photo', 'major_id', 'academic_year_id',
        'previous_school', 'status'
    ];

    /**
     * Get student with related data
     */
    public function getWithRelations($id) {
        $query = "SELECT s.*, 
                         m.name as major_name, m.code as major_code,
                         ay.year as academic_year_name
                  FROM {$this->table} s
                  LEFT JOIN majors m ON s.major_id = m.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  WHERE s.id = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get students with pagination and filters
     */
    public function getWithPagination($page = 1, $perPage = 10, $filters = []) {
        $conditions = ['s.status != ?'];
        $params = ['deleted'];

        // Build where conditions
        if (!empty($filters['search'])) {
            $conditions[] = "(s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_id LIKE ? OR s.email LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params = array_merge($params, [$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        }

        if (!empty($filters['major_id'])) {
            $conditions[] = "s.major_id = ?";
            $params[] = $filters['major_id'];
        }

        if (!empty($filters['academic_year_id'])) {
            $conditions[] = "s.academic_year_id = ?";
            $params[] = $filters['academic_year_id'];
        }

        if (!empty($filters['gender'])) {
            $conditions[] = "s.gender = ?";
            $params[] = $filters['gender'];
        }

        $whereClause = implode(' AND ', $conditions);
        
        // Count total
        $countQuery = "SELECT COUNT(*) as total FROM {$this->table} s WHERE {$whereClause}";
        $countStmt = $this->db->prepare($countQuery);
        $countStmt->execute($params);
        $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];

        // Get data
        $offset = ($page - 1) * $perPage;
        $query = "SELECT s.*, 
                         m.name as major_name,
                         ay.year as academic_year_name
                  FROM {$this->table} s
                  LEFT JOIN majors m ON s.major_id = m.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  WHERE {$whereClause}
                  ORDER BY s.created_at DESC
                  LIMIT {$perPage} OFFSET {$offset}";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'data' => $data,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];
    }

    /**
     * Generate next student ID
     */
    public function generateStudentId() {
        $query = "SELECT student_id FROM {$this->table} WHERE student_id IS NOT NULL ORDER BY student_id DESC LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result && $result['student_id']) {
            $lastId = $result['student_id'];
            $number = (int)substr($lastId, 3); // Remove 'STU' prefix
            $newNumber = $number + 1;
        } else {
            $newNumber = 1;
        }

        return 'STU' . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Check if email is unique
     */
    public function isEmailUnique($email, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Create student with auto-generated student_id
     */
    public function createStudent($data) {
        // Generate student ID if not provided
        if (empty($data['student_id'])) {
            $data['student_id'] = $this->generateStudentId();
        }

        return $this->create($data);
    }
}