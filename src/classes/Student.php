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
    public $registered_at;
    public $major_id;
    public $academic_year_id;
    public $previous_school;
    
    /**
     * Constructor - เชื่อมต่อฐานข้อมูล
     * @param PDO $db การเชื่อมต่อฐานข้อมูล
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * สร้างนักศึกษาใหม่
     * @return boolean สถานะการสร้าง
     */
    public function create() {
        try {
            // เตรียมคำสั่ง SQL
            $query = "INSERT INTO " . $this->table_name . "
                    (first_name, last_name, gender, dob, email, phone, village, district, province, 
                     accommodation_type, photo, major_id, academic_year_id, previous_school)
                    VALUES 
                    (:first_name, :last_name, :gender, :dob, :email, :phone, :village, :district, :province,
                     :accommodation_type, :photo, :major_id, :academic_year_id, :previous_school)";
            
            // เตรียมการ query
            $stmt = $this->conn->prepare($query);
            
            // ล้างข้อมูลและกำหนดค่า
            $this->sanitizeValues();
            
            // ผูกพารามิเตอร์
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
            
            // ประมวลผลคำสั่ง
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Student::create(): " . $e->getMessage());
            return false;
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
     * @return array รายการนักศึกษา
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
     * ค้นหานักศึกษาตามเงื่อนไข
     * @param array $params พารามิเตอร์การค้นหา
     * @return array รายการนักศึกษาที่พบ
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
     * ດຶງຂໍ້ມູນນັກສຶກສາຕາມ ID
     * 
     * @param int $id ID ຂອງນັກສຶກສາ
     * @return array|false ຂໍ້ມູນນັກສຶກສາ ຫຼື false ຖ້າບໍ່ພົບ
     */
    public function readOne($id) {
        try {
            $query = "SELECT * FROM students WHERE id = :id LIMIT 0,1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                return $row;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Student::readOne(): " . $e->getMessage());
            return false;
        }
    }
    /**
     * ອັບເດດຂໍ້ມູນນັກສຶກສາ
     * 
     * @return boolean ຜົນການອັບເດດ
     */
    public function update() {
        try {
            $query = "UPDATE students SET 
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
                    previous_school = :previous_school,
                    major_id = :major_id,
                    academic_year_id = :academic_year_id";
            
            // ຖ້າມີການອັບເດຕຮູບພາບ
            if (!empty($this->photo)) {
                $query .= ", photo = :photo";
            }
            
            $query .= " WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // ເຊື່ອມຕໍ່ຄ່າພາຣາມິເຕີ
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
            $stmt->bindParam(':previous_school', $this->previous_school);
            $stmt->bindParam(':major_id', $this->major_id);
            $stmt->bindParam(':academic_year_id', $this->academic_year_id);
            $stmt->bindParam(':id', $this->id);
            
            // ຖ້າມີການອັບເດຕຮູບພາບ
            if (!empty($this->photo)) {
                $stmt->bindParam(':photo', $this->photo);
            }
            
            // ດຳເນີນການຄຳສັ່ງ
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in Student::update(): " . $e->getMessage());
            return false;
        }
    }

    public function countStudents($search_term = '', $major_id = 0, $academic_year_id = 0) {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " s
                  LEFT JOIN majors m ON s.major_id = m.id
                  LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                  WHERE 1=1";
        
        $params = [];
        
        if (!empty($search_term)) {
            $query .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_id LIKE :search)";
            $params[':search'] = '%' . $search_term . '%';
        }
        
        if ($major_id > 0) {
            $query .= " AND s.major_id = :major_id";
            $params[':major_id'] = $major_id;
        }
        
        if ($academic_year_id > 0) {
            $query .= " AND s.academic_year_id = :academic_year_id";
            $params[':academic_year_id'] = $academic_year_id;
        }
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return (int)$result['total'];
    }

    public function getStudentsWithPagination($limit = 10, $offset = 0, $search_term = '', $major_id = 0, $academic_year_id = 0) {
        try {
            $query = "SELECT s.*, m.name as major_name, ay.year as academic_year_name 
                      FROM " . $this->table_name . " s
                      LEFT JOIN majors m ON s.major_id = m.id
                      LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                      WHERE 1=1";
        
            $params = [];
        
            if (!empty($search_term)) {
                $query .= " AND (s.first_name LIKE :search OR s.last_name LIKE :search OR s.student_id LIKE :search)";
                $params[':search'] = '%' . $search_term . '%';
            }
        
            if ($major_id > 0) {
                $query .= " AND s.major_id = :major_id";
                $params[':major_id'] = $major_id;
            }
        
            if ($academic_year_id > 0) {
                $query .= " AND s.academic_year_id = :academic_year_id";
                $params[':academic_year_id'] = $academic_year_id;
            }
        
            // แก้ไข ORDER BY ให้ใช้ registered_at แทน created_at
            $query .= " ORDER BY s.registered_at DESC LIMIT :limit OFFSET :offset";
        
            $stmt = $this->conn->prepare($query);
        
            // Bind parameters
            foreach ($params as $key => $value) {
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

    public function getAllMajors() {
        $query = "SELECT id, name FROM majors ORDER BY name";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllAcademicYears() {
        $query = "SELECT id, year FROM academic_years ORDER BY year DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * ลบข้อมูลนักศึกษา
     * 
     * @param int $id ID ของนักศึกษาที่ต้องการลบ
     * @return boolean ผลการลบ
     */
    public function delete($id) {
        try {
            // ดึงข้อมูลนักศึกษาเพื่อลบรูปภาพ
            $student = $this->readOne($id);
            
            if ($student && !empty($student['photo'])) {
                $photo_path = '../public/uploads/photos/' . $student['photo'];
                if (file_exists($photo_path)) {
                    unlink($photo_path); // ลบไฟล์รูปภาพ
                }
            }
            
            // ลบข้อมูลจากฐานข้อมูล
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in Student::delete(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลนักศึกษาพร้อมข้อมูลสาขาและปีการศึกษาสำหรับพิมพ์บัตร
     * 
     * @param int $id ID ของนักศึกษา
     * @return array|false ข้อมูลนักศึกษาพร้อมรายละเอียด
     */
    public function getStudentForCard($id) {
        try {
            // ตรวจสอบว่า ID เป็นตัวเลขและมากกว่า 0
            if (!is_numeric($id) || $id <= 0) {
                error_log("Invalid student ID: " . $id);
                return false;
            }
            
            // ดึงข้อมูลนักศึกษาพื้นฐานก่อน
            $basic_query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $basic_stmt = $this->conn->prepare($basic_query);
            $basic_stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $basic_stmt->execute();
            
            $student = $basic_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$student) {
                error_log("No student found with ID: " . $id);
                return false;
            }
            
            // เริ่มต้นค่าเริ่มต้น
            $student['major_name'] = 'ບໍ່ລະບຸສາຂາ';
            $student['major_code'] = 'ST';
            $student['academic_year_name'] = 'ບໍ່ລະບຸ';
            
            // ดึงข้อมูลสาขา
            if (!empty($student['major_id'])) {
                try {
                    $major_query = "SELECT name, code FROM majors WHERE id = :id LIMIT 1";
                    $major_stmt = $this->conn->prepare($major_query);
                    $major_stmt->bindParam(':id', $student['major_id'], PDO::PARAM_INT);
                    $major_stmt->execute();
                    $major_data = $major_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($major_data) {
                        $student['major_name'] = $major_data['name'];
                        $student['major_code'] = $major_data['code'] ?? 'ST';
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching major data: " . $e->getMessage());
                    // ใช้ค่าเริ่มต้น
                }
            }
            
            // ดึงข้อมูลปีการศึกษา
            if (!empty($student['academic_year_id'])) {
                try {
                    $year_query = "SELECT year FROM academic_years WHERE id = :id LIMIT 1";
                    $year_stmt = $this->conn->prepare($year_query);
                    $year_stmt->bindParam(':id', $student['academic_year_id'], PDO::PARAM_INT);
                    $year_stmt->execute();
                    $year_data = $year_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($year_data) {
                        $student['academic_year_name'] = $year_data['year'];
                    }
                } catch (PDOException $e) {
                    error_log("Error fetching academic year data: " . $e->getMessage());
                    // ใช้ค่าเริ่มต้น
                }
            }
            
            // ตรวจสอบและกำหนดค่าเริ่มต้นถ้าข้อมูลไม่ครบ
            if (empty($student['major_id']) || $student['major_name'] === 'ບໍ່ລະບຸສາຂາ') {
                // หาสาขาเริ่มต้น
                try {
                    $default_major_query = "SELECT id, name, code FROM majors ORDER BY id LIMIT 1";
                    $default_major_stmt = $this->conn->prepare($default_major_query);
                    $default_major_stmt->execute();
                    $default_major = $default_major_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($default_major) {
                        $student['major_id'] = $default_major['id'];
                        $student['major_name'] = $default_major['name'];
                        $student['major_code'] = $default_major['code'] ?? 'ST';
                        
                        // อัพเดทในฐานข้อมูล
                        $update_query = "UPDATE " . $this->table_name . " SET major_id = :major_id WHERE id = :id";
                        $update_stmt = $this->conn->prepare($update_query);
                        $update_stmt->bindParam(':major_id', $default_major['id'], PDO::PARAM_INT);
                        $update_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $update_stmt->execute();
                        
                        error_log("Set default major for student ID: " . $id);
                    }
                } catch (PDOException $e) {
                    error_log("Error setting default major: " . $e->getMessage());
                }
            }
            
            if (empty($student['academic_year_id']) || $student['academic_year_name'] === 'ບໍ່ລະບຸ') {
                // หาปีการศึกษาเริ่มต้น
                try {
                    $default_year_query = "SELECT id, year FROM academic_years ORDER BY year DESC LIMIT 1";
                    $default_year_stmt = $this->conn->prepare($default_year_query);
                    $default_year_stmt->execute();
                    $default_year = $default_year_stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($default_year) {
                        $student['academic_year_id'] = $default_year['id'];
                        $student['academic_year_name'] = $default_year['year'];
                        
                        // อัพเดทในฐานข้อมูล
                        $update_query = "UPDATE " . $this->table_name . " SET academic_year_id = :year_id WHERE id = :id";
                        $update_stmt = $this->conn->prepare($update_query);
                        $update_stmt->bindParam(':year_id', $default_year['id'], PDO::PARAM_INT);
                        $update_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $update_stmt->execute();
                        
                        error_log("Set default academic year for student ID: " . $id);
                    }
                } catch (PDOException $e) {
                    error_log("Error setting default academic year: " . $e->getMessage());
                }
            }
            
            // สร้างรหัสนักศึกษาถ้ายังไม่มี
            if (empty($student['student_id'])) {
                $student['student_id'] = $this->generateStudentId($student['academic_year_id'], $student['major_id']);
                
                try {
                    // อัพเดทรหัสนักศึกษาในฐานข้อมูล
                    $update_query = "UPDATE " . $this->table_name . " SET student_id = :student_id WHERE id = :id";
                    $update_stmt = $this->conn->prepare($update_query);
                    $update_stmt->bindParam(':student_id', $student['student_id']);
                    $update_stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $update_stmt->execute();
                    
                    error_log("Generated student ID: " . $student['student_id'] . " for student ID: " . $id);
                } catch (PDOException $e) {
                    error_log("Error updating student ID: " . $e->getMessage());
                }
            }
            
            error_log("Student card data retrieved successfully for ID: " . $id);
            return $student;
            
        } catch (PDOException $e) {
            error_log("Database error in Student::getStudentForCard(): " . $e->getMessage());
            return false;
        }
    }

    /**
     * สร้างรหัสนักศึกษาอัตโนมัติ
     * 
     * @param int $academic_year_id ID ปีการศึกษา
     * @param int $major_id ID สาขา
     * @return string รหัสนักศึกษา
     */
    private function generateStudentId($academic_year_id, $major_id) {
        try {
            // ค่าเริ่มต้น
            $year_short = date('y'); // ปีปัจจุบัน
            $major_code = 'ST'; // รหัสเริ่มต้น
            
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
                           WHERE (academic_year_id = :year_id OR :year_id IS NULL) 
                           AND (major_id = :major_id OR :major_id IS NULL)";
                $count_stmt = $this->conn->prepare($count_query);
                $count_stmt->bindParam(':year_id', $academic_year_id, PDO::PARAM_INT);
                $count_stmt->bindParam(':major_id', $major_id, PDO::PARAM_INT);
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
}
?>