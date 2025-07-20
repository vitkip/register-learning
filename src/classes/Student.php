<?php
// filepath: c:\xampp\htdocs\register-learning\src\classes\Student.php

/**
 * Student class - จัดการข้อมูลนักศึกษา
 */
class Student {
    private $conn;
    private $table_name = "students";
    
    // คุณสมบัติของวัตถุ
    public $id;
    public $student_id;
    public $first_name;
    public $last_name;
    public $gender;
    public $dob;
    public $email;
    public $phone;
    public $village;
    public $district;
    public $province;
    public $accommodation_type;
    public $photo;
    public $major_id;
    public $academic_year_id;
    public $previous_school;
    public $registered_at;
    
    /**
     * Constructor - เชื่อมต่อฐานข้อมูล
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * สร้างนักศึกษาใหม่
     */
    public function create() {
        try {
            // ตรวจสอบข้อมูล
            $this->sanitizeValues();
            
            // Query สำหรับเพิ่มข้อมูล
            $query = "INSERT INTO " . $this->table_name . " SET
                        first_name = :first_name,
                        last_name = :last_name,
                        gender = :gender,
                        dob = :dob,
                        email = :email,
                        phone = :phone,
                        village = :village,
                        district = :district,
                        province = :province,
                        accommodation_type = :accommodation_type,
                        photo = :photo,
                        major_id = :major_id,
                        academic_year_id = :academic_year_id,
                        previous_school = :previous_school,
                        registered_at = NOW()";
            
            // เตรียม statement
            $stmt = $this->conn->prepare($query);
            
            // bind parameters
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':dob', $this->dob);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':village', $this->village);
            $stmt->bindParam(':district', $this->district);
            $stmt->bindParam(':province', $this->province);
            $stmt->bindParam(':accommodation_type', $this->accommodation_type);
            $stmt->bindParam(':photo', $this->photo);
            $stmt->bindParam(':major_id', $this->major_id);
            $stmt->bindParam(':academic_year_id', $this->academic_year_id);
            $stmt->bindParam(':previous_school', $this->previous_school);
            
            // execute
            if ($stmt->execute()) {
                // บันทึก ID ที่เพิ่งสร้าง
                $this->id = $this->conn->lastInsertId();
                
                // สร้าง student_id และอัปเดต
                $this->updateStudentId();
                
                error_log("Student created with ID: " . $this->id . " and Student ID: " . $this->student_id);
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::create(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * อัปเดต student_id ในฐานข้อมูล
     */
    private function updateStudentId() {
        try {
            // สร้าง student_id
            $this->student_id = $this->generateStudentId($this->academic_year_id, $this->major_id);
            
            // อัปเดตในฐานข้อมูล
            $updateQuery = "UPDATE " . $this->table_name . " SET student_id = :student_id WHERE id = :id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':student_id', $this->student_id);
            $updateStmt->bindParam(':id', $this->id);
            
            return $updateStmt->execute();
            
        } catch (PDOException $e) {
            error_log("Error updating student ID: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * สร้างรหัสนักศึกษาอัตโนมัติ
     */
    private function generateStudentId($academic_year_id = null, $major_id = null) {
        try {
            // ใช้ค่าจาก object หากไม่มี parameter
            $academic_year_id = $academic_year_id ?? $this->academic_year_id;
            $major_id = $major_id ?? $this->major_id;
            
            // ค่าเริ่มต้น
            $year_short = date('y');
            $major_code = 'ST';
            
            // ดึงปีการศึกษา
            if ($academic_year_id && $academic_year_id > 0) {
                try {
                    $year_query = "SELECT year FROM academic_years WHERE id = :id LIMIT 1";
                    $year_stmt = $this->conn->prepare($year_query);
                    $year_stmt->bindParam(':id', $academic_year_id, PDO::PARAM_INT);
                    $year_stmt->execute();
                    $year_data = $year_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($year_data && !empty($year_data['year'])) {
                        $year_string = $year_data['year'];
                        // แยกปีออกมา เช่น 2025-2026 -> 25
                        if (preg_match('/(\d{4})/', $year_string, $matches)) {
                            $year_short = substr($matches[1], -2);
                        }
                    }
                } catch (PDOException $e) {
                    error_log("Error getting year data: " . $e->getMessage());
                }
            }
            
            // ดึงรหัสสาขา  
            if ($major_id && $major_id > 0) {
                try {
                    $major_query = "SELECT code FROM majors WHERE id = :id LIMIT 1";
                    $major_stmt = $this->conn->prepare($major_query);
                    $major_stmt->bindParam(':id', $major_id, PDO::PARAM_INT);
                    $major_stmt->execute();
                    $major_data = $major_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($major_data && !empty($major_data['code'])) {
                        $major_code = $major_data['code'];
                    }
                } catch (PDOException $e) {
                    error_log("Error getting major code: " . $e->getMessage());
                }
            }
            
            // หาลำดับถัดไป
            try {
                $count_query = "SELECT COUNT(*) as total FROM " . $this->table_name . " 
                           WHERE student_id IS NOT NULL AND student_id != ''";
                $count_stmt = $this->conn->prepare($count_query);
                $count_stmt->execute();
                $count_data = $count_stmt->fetch(PDO::FETCH_ASSOC);
                
                $sequence = str_pad(($count_data['total'] ?? 0) + 1, 3, '0', STR_PAD_LEFT);
            } catch (PDOException $e) {
                error_log("Error counting students: " . $e->getMessage());
                $sequence = str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
            }
            
            return $year_short . $major_code . $sequence;
            
        } catch (Exception $e) {
            error_log("Error in generateStudentId: " . $e->getMessage());
            return date('y') . 'ST' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        }
    }
    
    /**
     * ล้างและตรวจสอบค่าข้อมูลก่อนบันทึกลงฐานข้อมูล
     */
    private function sanitizeValues() {
        $this->first_name = htmlspecialchars(strip_tags($this->first_name));
        $this->last_name = htmlspecialchars(strip_tags($this->last_name));
        $this->gender = htmlspecialchars(strip_tags($this->gender));
        $this->email = $this->email ? htmlspecialchars(strip_tags($this->email)) : null;
        $this->phone = $this->phone ? htmlspecialchars(strip_tags($this->phone)) : null;
        $this->village = $this->village ? htmlspecialchars(strip_tags($this->village)) : null;
        $this->district = $this->district ? htmlspecialchars(strip_tags($this->district)) : null;
        $this->province = $this->province ? htmlspecialchars(strip_tags($this->province)) : null;
        $this->accommodation_type = htmlspecialchars(strip_tags($this->accommodation_type));
        $this->previous_school = $this->previous_school ? htmlspecialchars(strip_tags($this->previous_school)) : null;
    }
    
    /**
     * ดึงรายชื่อนักศึกษาทั้งหมด
     */
    public function readAll() {
        try {
            $query = "SELECT s.*, m.name as major_name, a.year as academic_year
                      FROM " . $this->table_name . " s
                      LEFT JOIN majors m ON s.major_id = m.id
                      LEFT JOIN academic_years a ON s.academic_year_id = a.id
                      ORDER BY s.registered_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Student::readAll(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ดึงข้อมูลนักศึกษาตาม ID
     */
    public function readOne($id) {
        try {
            $query = "SELECT s.*, 
                             m.name as major_name,
                             ay.year as academic_year_name
                      FROM " . $this->table_name . " s
                      LEFT JOIN majors m ON s.major_id = m.id
                      LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                      WHERE s.id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                // กำหนดค่าให้กับ properties
                $this->id = $row['id'];
                $this->student_id = $row['student_id'] ?? null;
                $this->first_name = $row['first_name'];
                $this->last_name = $row['last_name'];
                $this->gender = $row['gender'];
                $this->dob = $row['dob'];
                $this->email = $row['email'] ?? null;
                $this->phone = $row['phone'] ?? null;
                $this->village = $row['village'] ?? null;
                $this->district = $row['district'] ?? null;
                $this->province = $row['province'] ?? null;
                $this->accommodation_type = $row['accommodation_type'] ?? null;
                $this->photo = $row['photo'] ?? null;
                $this->major_id = $row['major_id'] ?? null;
                $this->academic_year_id = $row['academic_year_id'] ?? null;
                $this->previous_school = $row['previous_school'] ?? null;
                $this->registered_at = $row['registered_at'];
                
                return $row;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::readOne(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ค้นหานักศึกษาตามเงื่อนไข
     */
    public function search($params) {
        try {
            $conditions = [];
            $values = [];
            
            // สร้างเงื่อนไขการค้นหา
            if (!empty($params['name'])) {
                $conditions[] = "(s.first_name LIKE :name OR s.last_name LIKE :name)";
                $values[':name'] = "%" . $params['name'] . "%";
            }
            
            if (!empty($params['major_id'])) {
                $conditions[] = "s.major_id = :major_id";
                $values[':major_id'] = $params['major_id'];
            }
            
            if (!empty($params['academic_year_id'])) {
                $conditions[] = "s.academic_year_id = :academic_year_id";
                $values[':academic_year_id'] = $params['academic_year_id'];
            }
            
            // สร้าง WHERE clause
            $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT s.*, m.name as major_name, a.year as academic_year
                      FROM " . $this->table_name . " s
                      LEFT JOIN majors m ON s.major_id = m.id
                      LEFT JOIN academic_years a ON s.academic_year_id = a.id
                      {$where}
                      ORDER BY s.registered_at DESC";
            
            $stmt = $this->conn->prepare($query);
            
            // ผูกพารามิเตอร์
            foreach ($values as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Student::search(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * อัปเดตข้อมูลนักศึกษา
     */
    public function update() {
        try {
            $this->sanitizeValues();
            
            $query = "UPDATE " . $this->table_name . " SET
                        first_name = :first_name,
                        last_name = :last_name,
                        gender = :gender,
                        dob = :dob,
                        email = :email,
                        phone = :phone,
                        village = :village,
                        district = :district,
                        province = :province,
                        accommodation_type = :accommodation_type,
                        major_id = :major_id,
                        academic_year_id = :academic_year_id,
                        previous_school = :previous_school";
            
            // เพิ่มการอัปเดตรูปภาพหากมี
            if (!empty($this->photo)) {
                $query .= ", photo = :photo";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // bind parameters
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':dob', $this->dob);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':village', $this->village);
            $stmt->bindParam(':district', $this->district);
            $stmt->bindParam(':province', $this->province);
            $stmt->bindParam(':accommodation_type', $this->accommodation_type);
            $stmt->bindParam(':major_id', $this->major_id);
            $stmt->bindParam(':academic_year_id', $this->academic_year_id);
            $stmt->bindParam(':previous_school', $this->previous_school);
            $stmt->bindParam(':id', $this->id);
            
            if (!empty($this->photo)) {
                $stmt->bindParam(':photo', $this->photo);
            }
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Database error in Student::update(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ลบข้อมูลนักศึกษา
     */
    public function delete() {
        try {
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $this->id);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Database error in Student::delete(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ตรวจสอบว่าอีเมลมีอยู่ในระบบแล้วหรือไม่
     */
    public function emailExists($email, $excludeId = null) {
        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            
            if ($excludeId) {
                $query .= " AND id != :exclude_id";
            }
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            
            if ($excludeId) {
                $stmt->bindParam(':exclude_id', $excludeId);
            }
            
            $stmt->execute();
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::emailExists(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ลบรูปภาพนักศึกษา
     */
    public function deletePhoto($id) {
        try {
            // ดึงข้อมูลนักศึกษาเพื่อลบรูปภาพ
            $student = $this->readOne($id);
            
            if ($student && !empty($student['photo'])) {
                $photo_path = '../public/uploads/photos/' . $student['photo'];
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                }
            }
            
            // อัปเดตฐานข้อมูลเพื่อลบข้อมูลรูปภาพ
            $query = "UPDATE " . $this->table_name . " SET photo = NULL WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in Student::deletePhoto(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ดึงข้อมูลนักศึกษาพร้อมข้อมูลสาขาและปีการศึกษาสำหรับพิมพ์บัตร
     */
    public function getStudentForCard($id) {
        try {
            // ตรวจสอบว่า ID เป็นตัวเลขและมากกว่า 0
            if (!is_numeric($id) || $id <= 0) {
                error_log("Invalid student ID: " . $id);
                return false;
            }
            
            // ดึงข้อมูลนักศึกษาแบบเต็ม
            $student = $this->readOne($id);
            
            if (!$student) {
                error_log("No student found with ID: " . $id);
                return false;
            }
            
            // สร้างรหัสนักศึกษาใหม่ถ้าไม่มี
            if (empty($student['student_id'])) {
                $student['student_id'] = $this->generateStudentId($student['academic_year_id'], $student['major_id']);
                
                // อัปเดตในฐานข้อมูล
                $update_query = "UPDATE " . $this->table_name . " SET student_id = :student_id WHERE id = :id";
                $update_stmt = $this->conn->prepare($update_query);
                $update_stmt->bindParam(':student_id', $student['student_id']);
                $update_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $update_stmt->execute();
                
                error_log("Generated student ID: " . $student['student_id'] . " for student ID: " . $id);
            }
            
            return $student;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::getStudentForCard(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * นับจำนวนนักศึกษาทั้งหมด
     */
    public function getCount() {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::getCount(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนนักศึกษาทั้งหมด
     */
    public function countStudents() {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::countStudents(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * นับจำนวนนักศึกษาตามเงื่อนไขการค้นหา
     */
    public function countStudentsWithFilter($params = []) {
        try {
            $conditions = [];
            $values = [];
            
            // สร้างเงื่อนไขการค้นหา
            if (!empty($params['search'])) {
                $conditions[] = "(s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_id LIKE :search OR s.email LIKE :search OR s.phone LIKE :search)";
                $values[':search'] = "%" . $params['search'] . "%";
            }
            
            if (!empty($params['major']) && $params['major'] != '0') {
                $conditions[] = "s.major_id = :major_id";
                $values[':major_id'] = $params['major'];
            }
            
            if (!empty($params['year']) && $params['year'] != '0') {
                $conditions[] = "s.academic_year_id = :academic_year_id";
                $values[':academic_year_id'] = $params['year'];
            }
            
            // สร้าง WHERE clause
            $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            $query = "SELECT COUNT(*) as count 
                      FROM " . $this->table_name . " s 
                      LEFT JOIN majors m ON s.major_id = m.id 
                      LEFT JOIN academic_years a ON s.academic_year_id = a.id 
                      {$where}";
            
            $stmt = $this->conn->prepare($query);
            
            // ผูกพารามิเตอร์
            foreach ($values as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row['count'] ?? 0;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::countStudentsWithFilter(): " . $e->getMessage());
            return 0;
        }
    }

    /**
     * ดึงข้อมูลนักศึกษาพร้อม pagination และ filter
     */
    public function getStudentsWithPagination($params = []) {
        try {
            $conditions = [];
            $values = [];
            
            // สร้างเงื่อนไขการค้นหา
            if (!empty($params['search'])) {
                $conditions[] = "(s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_id LIKE :search OR s.email LIKE :search OR s.phone LIKE :search)";
                $values[':search'] = "%" . $params['search'] . "%";
            }
            
            if (!empty($params['major']) && $params['major'] != '0') {
                $conditions[] = "s.major_id = :major_id";
                $values[':major_id'] = $params['major'];
            }
            
            if (!empty($params['year']) && $params['year'] != '0') {
                $conditions[] = "s.academic_year_id = :academic_year_id";
                $values[':academic_year_id'] = $params['year'];
            }
            
            // สร้าง WHERE clause
            $where = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";
            
            // กำหนดค่า pagination
            $limit = isset($params['limit']) ? intval($params['limit']) : 10;
            $offset = isset($params['offset']) ? intval($params['offset']) : 0;
            
            $query = "SELECT s.*, 
                         m.name as major_name,
                         a.year as academic_year_name
                  FROM " . $this->table_name . " s
                  LEFT JOIN majors m ON s.major_id = m.id
                  LEFT JOIN academic_years a ON s.academic_year_id = a.id
                  {$where}
                  ORDER BY s.registered_at DESC
                  LIMIT :limit OFFSET :offset";
            
            $stmt = $this->conn->prepare($query);
            
            // ผูกพารามิเตอร์
            foreach ($values as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Database error in Student::getStudentsWithPagination(): " . $e->getMessage());
            return [];
        }
    }

    /**
     * ลบนักศึกษาตาม ID
     */
    public function deleteById($id) {
        try {
            // ดึงข้อมูลนักศึกษาก่อนลบ
            $student = $this->readOne($id);
            
            if ($student) {
                // ลบรูปภาพถ้ามี
                if (!empty($student['photo'])) {
                    $photo_path = BASE_PATH . '/public/uploads/photos/' . $student['photo'];
                    if (file_exists($photo_path)) {
                        unlink($photo_path);
                    }
                }
                
                // ลบข้อมูลจากฐานข้อมูล
                $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                
                return $stmt->execute();
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::deleteById(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่านักศึกษามีอยู่หรือไม่
     */
    public function exists($id) {
        try {
            $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return ($row['count'] ?? 0) > 0;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::exists(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลสถิติเบื้องต้น
     */
    public function getStats() {
        try {
            $stats = [
                'total_students' => 0,
                'by_gender' => [],
                'by_major' => [],
                'by_year' => [],
                'by_accommodation' => []
            ];
            
            // จำนวนนักศึกษาทั้งหมด
            $stats['total_students'] = $this->countStudents();
            
            // จำนวนแยกตามเพศ
            $query = "SELECT gender, COUNT(*) as count FROM " . $this->table_name . " GROUP BY gender";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['by_gender'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // จำนวนแยกตามสาขา
            $query = "SELECT m.name as major_name, COUNT(*) as count 
                      FROM " . $this->table_name . " s 
                      LEFT JOIN majors m ON s.major_id = m.id 
                      GROUP BY s.major_id, m.name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['by_major'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // จำนวนแยกตามปีการศึกษา
            $query = "SELECT a.year as academic_year, COUNT(*) as count 
                      FROM " . $this->table_name . " s 
                      LEFT JOIN academic_years a ON s.academic_year_id = a.id 
                      GROUP BY s.academic_year_id, a.year";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['by_year'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // จำนวนแยกตามที่พัก
            $query = "SELECT accommodation_type, COUNT(*) as count 
                      FROM " . $this->table_name . " 
                      GROUP BY accommodation_type";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $stats['by_accommodation'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::getStats(): " . $e->getMessage());
            return [
                'total_students' => 0,
                'by_gender' => [],
                'by_major' => [],
                'by_year' => [],
                'by_accommodation' => []
            ];
        }
    }
    
    /**
     * ดึงจำนวนนักศึกษาทั้งหมด
     */
    public function getTotalCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Database error in Student::getTotalCount(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * ดึงจำนวนนักศึกษาตามเพศ
     */
    public function getCountByGender($gender) {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE gender = :gender";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':gender', $gender);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Database error in Student::getCountByGender(): " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * ดึงนักศึกษาที่ลงทะบียนล่าสุด
     */
    public function getRecentStudents($limit = 5) {
        try {
            $query = "SELECT s.*, m.name as major_name 
                     FROM " . $this->table_name . " s 
                     LEFT JOIN majors m ON s.major_id = m.id 
                     ORDER BY s.registered_at DESC, s.id DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Student::getRecentStudents(): " . $e->getMessage());
            return [];
        }
    }
}
?>