<?php
// filepath: /register-learning/register-learning/src/classes/Student.php

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
            
            // ຖ້າມີການອັບເດດຮູບພາບ
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
            
            // ຖ້າມີການອັບເດດຮູບພາບ
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
}
?>