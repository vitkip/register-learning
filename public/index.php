<?php
// filepath: c:\xampp\htdocs\register-learning\public\index.php

// เริ่ม session
session_start();

// การกำหนดค่าเริ่มต้น
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../src/helpers/functions.php';

// ตัดสินใจว่าหน้าใดที่จะแสดง
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// เชื่อมต่อฐานข้อมูล
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    $error = "ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໄດ້. ກະລຸນາລອງໃໝ່ພາຍຫຼັງ.";
}

// ตรวจสอบว่ามีการส่งฟอร์ม POST และประมวลผลการลงทะเบียน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'register' && $action === 'process') {
    try {
        require_once '../src/classes/Student.php';
        $student = new Student($db);
        
        // ดึงข้อมูลจากฟอร์ม
        $student->first_name = $_POST['first_name'];
        $student->last_name = $_POST['last_name'];
        $student->gender = $_POST['gender'];
        $student->dob = $_POST['dob'];
        $student->email = !empty($_POST['email']) ? $_POST['email'] : null;
        $student->phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
        $student->village = !empty($_POST['village']) ? $_POST['village'] : null;
        $student->district = !empty($_POST['district']) ? $_POST['district'] : null;
        $student->province = !empty($_POST['province']) ? $_POST['province'] : null;
        $student->accommodation_type = $_POST['accommodation_type'];
        $student->previous_school = !empty($_POST['previous_school']) ? $_POST['previous_school'] : null;
        $student->major_id = (int)$_POST['major_id'];
        $student->academic_year_id = (int)$_POST['academic_year_id'];
        
        // จัดการอัปโหลดรูปภาพ
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = '../public/uploads/photos/';
            
            // สร้างไดเรกทอรีถ้าไม่มี
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // กำหนดชื่อไฟล์ใหม่
            $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            // ย้ายไฟล์ที่อัปโหลดมา
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                $student->photo = $file_name;
            }
        }
        
        // บันทึกข้อมูล
        if ($student->create()) {
            // ดึงข้อมูลนักศึกษาที่เพิ่งลงทะเบียน
            $studentData = $student->readOne($student->id);
            
            // เก็บข้อมูลใน session สำหรับหน้า registration-success
            $_SESSION['studentData'] = $studentData;
            $_SESSION['registration_success'] = true;
            $_SESSION['message'] = "ລົງທະບຽນສຳເລັດແລ້ວ!";
            $_SESSION['message_type'] = "success";
            
            // Redirect ไปหน้า registration-success
            header("Location: index.php?page=registration-success");
            exit;
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການລົງທະບຽນ");
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=register");
        exit;
    }
}

// จัดการการอัพเดทข้อมูลนักศึกษา
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'student-edit' && $action === 'update') {
    require_once '../src/classes/Student.php';
    
    try {
        // สร้างออบเจ็กต์ Student
        $student = new Student($db);
        
        // ดึงข้อมูล ID
        $student->id = (int)$_POST['id'];
        
        // ตรวจสอบว่ามีนักศึกษาคนนี้จริงหรือไม่
        $existingStudent = $student->readOne($student->id);
        if (!$existingStudent) {
            throw new Exception("ບໍ່ພົບຂໍ້ມູນນັກສຶກສາທີ່ຕ້ອງການແກ້ໄຂ");
        }
        
        // ดึงข้อมูลจากฟอร์ม
        $student->first_name = $_POST['first_name'];
        $student->last_name = $_POST['last_name'];
        $student->gender = $_POST['gender'];
        $student->dob = $_POST['dob'];
        $student->email = !empty($_POST['email']) ? $_POST['email'] : null;
        $student->phone = !empty($_POST['phone']) ? $_POST['phone'] : null;
        $student->village = !empty($_POST['village']) ? $_POST['village'] : null;
        $student->district = !empty($_POST['district']) ? $_POST['district'] : null;
        $student->province = !empty($_POST['province']) ? $_POST['province'] : null;
        $student->accommodation_type = $_POST['accommodation_type'];
        $student->previous_school = !empty($_POST['previous_school']) ? $_POST['previous_school'] : null;
        $student->major_id = (int)$_POST['major_id'];
        $student->academic_year_id = (int)$_POST['academic_year_id'];
        
        // จัดการอัปโหลดรูปภาพ
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = '../public/uploads/photos/';
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // ลบรูปเก่าถ้ามี
            if (!empty($existingStudent['photo']) && file_exists($upload_dir . $existingStudent['photo'])) {
                unlink($upload_dir . $existingStudent['photo']);
            }
            
            // ตั้งชื่อไฟล์ใหม่
            $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            // ย้ายไฟล์ที่อัปโหลดมา
            if (move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                $student->photo = $file_name;
            }
        } else {
            // ใช้รูปเดิม
            $student->photo = $_POST['current_photo'];
        }
        
        // บันทึกข้อมูล
        if ($student->update()) {
            $_SESSION['message'] = "ອັບເດດຂໍ້ມູນສຳເລັດແລ້ວ!";
            $_SESSION['message_type'] = "success";
            header("Location: index.php?page=student-detail&id=" . $student->id);
            exit;
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການອັບເດດຂໍ້ມູນ");
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=student-edit&id=" . (int)$_POST['id']);
        exit;
    }
}

// เพิ่มในส่วนของการจัดการ POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'verify-qrcode') {
    try {
        require_once '../src/classes/Student.php';
        require_once '../src/classes/Major.php';
        require_once '../src/classes/AcademicYear.php';
        
        $student = new Student($db);
        $result = ['valid' => false, 'message' => ''];
        
        if ($action === 'id') {
            // ตรวจสอบโดยใช้รหัสนักศึกษา
            $student_id = (int)$_POST['student_id'];
            $studentData = $student->readOne($student_id);
            
            if ($studentData) {
                // ดึงข้อมูลเพิ่มเติม
                $majorObj = new Major($db);
                $major = $majorObj->readOne($studentData['major_id']);
                
                $yearObj = new AcademicYear($db);
                $academicYear = $yearObj->readOne($studentData['academic_year_id']);
                
                $studentData['major_name'] = $major['name'];
                $studentData['academic_year'] = $academicYear['year'];
                
                $result = [
                    'valid' => true,
                    'student' => $studentData
                ];
            } else {
                $result['message'] = 'ບໍ່ພົບຂໍ້ມູນນັກສຶກສາລະຫັດ ' . $student_id;
            }
        } 
        
        $_SESSION['verification_result'] = $result;
        header("Location: index.php?page=verify-qrcode");
        exit;
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=verify-qrcode");
        exit;
    }
}

// ตรวจสอบหน้า registration-success และดึงข้อมูล
if ($page === 'registration-success') {
    if (isset($_SESSION['studentData'])) {
        $studentData = $_SESSION['studentData'];
        $showSuccessAlert = isset($_SESSION['registration_success']) && $_SESSION['registration_success'];
        unset($_SESSION['studentData']); // ลบออกจาก session หลังจากใช้แล้ว
        unset($_SESSION['registration_success']);
    } else {
        // หากไม่มีข้อมูล redirect กลับไปหน้า register
        $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນການລົງທະບຽນ";
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=register");
        exit;
    }
}
// จัดการหน้าพิมพ์บัตรนักศึกษา
if ($page === 'student-card' && isset($_GET['id'])) {
    require_once '../src/classes/Student.php';
    
    $student = new Student($db);
    $student_id = (int)$_GET['id'];
    
    // เพิ่มการ debug
    error_log("Requesting student card for ID: " . $student_id);
    
    // ตรวจสอบ ID ที่ส่งมา
    if ($student_id <= 0) {
        error_log("Invalid student ID provided: " . $student_id);
        $_SESSION['message'] = "ID นักศึกษาไม่ถูกต้อง";
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=students");
        exit;
    }
    
    // ตรวจสอบว่านักศึกษามีอยู่จริงก่อน
    $basic_student = $student->readOne($student_id);
    if (!$basic_student) {
        error_log("No basic student found for ID: " . $student_id);
        $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ ID: " . $student_id . " ໃນລະບົບ";
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=students");
        exit;
    }
    
    error_log("Basic student found: " . print_r($basic_student, true));
    
    // ดึงข้อมูลสำหรับพิมพ์บัตร
    $student_data = $student->getStudentForCard($student_id);
    
    if (!$student_data) {
        error_log("Failed to get student card data for ID: " . $student_id);
        $_SESSION['message'] = "ເກີດຂໍ້ຜິດພາດໃນການດຶງຂໍ້ມູນສຳລັບພິມບັດ - ID: " . $student_id;
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=students");
        exit;
    }
    
    error_log("Student card data retrieved: " . print_r($student_data, true));
    
    // ถ้าสำเร็จ ให้ไปที่ template
    include '../templates/student-card.php';
    exit;
}

// ตรวจสอบหน้า students และเพิ่ม pagination
if ($page === 'students') {
    require_once '../src/classes/Student.php';
    require_once '../src/classes/Major.php';
    require_once '../src/classes/AcademicYear.php';
    
    $student = new Student($db);
    
    // เพิ่มการดึงข้อมูลสาขาและปีการศึกษา
    $majorObj = new Major($db);
    $majors = $majorObj->readAll();
    
    $yearObj = new AcademicYear($db);
    $academicYears = $yearObj->readAll();
    
    // การตั้งค่า pagination
    $students_per_page = isset($_GET['students_per_page']) ? (int)$_GET['students_per_page'] : 10;
    $students_per_page = max(5, min(100, $students_per_page));
    
    $current_page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
    $current_page = max(1, $current_page);
    
    // การค้นหา - กำหนดค่าเริ่มต้น
    $current_search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $current_major = isset($_GET['major']) ? (int)$_GET['major'] : 0;
    $current_year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
    
    // ใช้ตัวแปรเดียวกันสำหรับ methods
    $search_term = $current_search;
    $filter_major = $current_major;
    $filter_year = $current_year;
    
    // การลบนักศึกษา (ก่อนดึงข้อมูล)
    if ($action === 'delete' && isset($_GET['id'])) {
        $student_id = (int)$_GET['id'];
        
        try {
            if ($student->delete($student_id)) {
                $_SESSION['message'] = "ລຶບຂໍ້ມູນນັກສຶກສາສຳເລັດແລ້ວ";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ";
                $_SESSION['message_type'] = "error";
            }
        } catch (Exception $e) {
            $_SESSION['message'] = "ເກີດຂໍ້ຜິດພາດ: " . $e->getMessage();
            $_SESSION['message_type'] = "error";
        }
        
        // redirect กลับไปหน้าเดิม
        $redirect_url = "index.php?page=students";
        if ($current_page > 1) {
            $redirect_url .= "&p=" . $current_page;
        }
        if (!empty($current_search)) {
            $redirect_url .= "&search=" . urlencode($current_search);
        }
        if ($current_major > 0) {
            $redirect_url .= "&major=" . $current_major;
        }
        if ($current_year > 0) {
            $redirect_url .= "&year=" . $current_year;
        }
        if ($students_per_page != 10) {
            $redirect_url .= "&students_per_page=" . $students_per_page;
        }
        
        header("Location: " . $redirect_url);
        exit;
    }
    
    // นับจำนวนนักศึกษาทั้งหมด
    $total_students = $student->countStudents($search_term, $filter_major, $filter_year);
    $total_pages = ceil($total_students / $students_per_page);
    
    // คำนวณ offset
    $offset = ($current_page - 1) * $students_per_page;
    
    // ดึงข้อมูลนักศึกษาตาม pagination
    $students = $student->getStudentsWithPagination($students_per_page, $offset, $search_term, $filter_major, $filter_year);
}

// รวมส่วนหัว (Header) - เฉพาะหน้าที่ต้องการ layout ปกติ
include_once '../templates/components/header.php';

// ตรวจสอบข้อผิดพลาดการเชื่อมต่อฐานข้อมูล
if (isset($error)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
    echo '<strong>ຂໍ້ຜິດພາດ!</strong> ' . $error;
    echo '</div>';
} else {
    // แสดงหน้าที่ถูกร้องขอ
    switch ($page) {
        case 'dashboard':
        case 'home':
            include '../templates/dashboard.php';
            break;
            
        case 'register':
            include '../templates/register.php';
            break;
            
        case 'students':
            // ตรวจสอบการลบข้อมูล
            if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
                require_once '../src/classes/Student.php';
                $student = new Student($db);
                $student_id = (int)$_GET['id'];
                
                if ($student->delete($student_id)) {
                    $_SESSION['message'] = "ລຶບຂໍ້ມູນນັກສຶກສາສຳເລັດແລ້ວ";
                    $_SESSION['message_type'] = "success";
                } else {
                    $_SESSION['message'] = "ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ";
                    $_SESSION['message_type'] = "error";
                }
                
                // Redirect กลับไปหน้า students โดยรักษา pagination
                $redirect_params = $_GET;
                unset($redirect_params['action'], $redirect_params['id']);
                $query_string = http_build_query($redirect_params);
                header("Location: index.php?" . $query_string);
                exit;
            }
            
            // โหลดข้อมูลสำหรับหน้า students
            require_once '../src/classes/Student.php';
            
            $student = new Student($db);
            
            // รับค่าจากการค้นหาและ filter
            $current_search = isset($_GET['search']) ? trim($_GET['search']) : '';
            $current_major = isset($_GET['major']) ? (int)$_GET['major'] : 0;
            $current_year = isset($_GET['year']) ? (int)$_GET['year'] : 0;
            $current_page = isset($_GET['p']) ? max(1, (int)$_GET['p']) : 1;
            $students_per_page = isset($_GET['students_per_page']) ? max(5, min(100, (int)$_GET['students_per_page'])) : 10;
            
            // คำนวณ offset
            $offset = ($current_page - 1) * $students_per_page;
            
            // ดึงข้อมูลนักศึกษา
            $students = $student->getStudentsWithPagination($students_per_page, $offset, $current_search, $current_major, $current_year);
            
            // นับจำนวนทั้งหมด
            $total_students = $student->countStudents($current_search, $current_major, $current_year);
            $total_pages = ceil($total_students / $students_per_page);
            
            // ดึงข้อมูลสาขาและปีการศึกษา
            $majors = $student->getAllMajors();
            $academicYears = $student->getAllAcademicYears();
            
            include '../templates/students-list.php';
            break;
            
        case 'student-detail':
            if (isset($_GET['id'])) {
                require_once '../src/classes/Student.php';
                $student = new Student($db);
                $student_data = $student->readOne((int)$_GET['id']);
                
                if (!$student_data) {
                    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ";
                    $_SESSION['message_type'] = "error";
                    header("Location: index.php?page=students");
                    exit;
                }
                
                include '../templates/student-detail.php';
            } else {
                header("Location: index.php?page=students");
                exit;
            }
            break;
            
        case 'student-edit':
            if (isset($_GET['id'])) {
                require_once '../src/classes/Student.php';
                $student = new Student($db);
                $student_data = $student->readOne((int)$_GET['id']);
                
                if (!$student_data) {
                    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ";
                    $_SESSION['message_type'] = "error";
                    header("Location: index.php?page=students");
                    exit;
                }
                
                // ดึงข้อมูลสาขาและปีการศึกษา
                $majors = $student->getAllMajors();
                $academicYears = $student->getAllAcademicYears();
                
                include '../templates/student-edit.php';
            } else {
                header("Location: index.php?page=students");
                exit;
            }
            break;
            
        // ไม่ต้องมี case 'student-card' ที่นี่ เพราะจัดการไปแล้วด้านบน
        
        default:
            include '../templates/dashboard.php';
            break;
    }
}

// รวมส่วนท้าย (Footer)
include_once '../templates/components/footer.php';
?>