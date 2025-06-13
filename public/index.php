<?php
// filepath: c:\xampp\htdocs\register-learning\public\index.php

// เริ่ม session
session_start();

// การกำหนดค่าเริ่มต้น
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../src/helpers/functions.php';

// ตัดสินใจว่าแม่นหน้าใดที่จะแสดง
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
            $upload_dir = '../public/assets/uploads/photos/';
            
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
            // แทนที่จะ redirect ไปยังหน้าอื่น
            // เราจะเก็บข้อมูลนักศึกษาและแสดงหน้า registration-success
            $studentData = $student->readOne($student->id); // ดึงข้อมูลนักศึกษาที่เพิ่งลงทะเบียน
            
            // ส่งข้อมูลไปยังหน้าแสดงผล QR Code
            include_once '../templates/registration-success.php';
            exit; // หยุดการทำงานเพื่อไม่ให้โหลดส่วนอื่นของเพจ
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການລົງທະບຽນ");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
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
            $upload_dir = '../public/assets/uploads/photos/';
            
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
            $_SESSION['success'] = "ອັບເດດຂໍ້ມູນສຳເລັດແລ້ວ!";
            header("Location: index.php?page=student-detail&id=" . $student->id);
            exit;
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການອັບເດດຂໍ້ມູນ");
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
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
        // สามารถเพิ่มการประมวลผลการตรวจสอบจาก QR Code ในอนาคต
        
        $_SESSION['verification_result'] = $result;
        header("Location: index.php?page=verify-qrcode");
        exit;
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        header("Location: index.php?page=verify-qrcode");
        exit;
    }
}

// รวมส่วนหัวเข้า
include_once '../templates/components/header.php';
include_once '../templates/components/navigation.php';

// ตรวจสอบข้อผิดพลาดการเชื่อมต่อฐานข้อมูล
if (isset($error)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
    echo '<strong>ຂໍ້ຜິດພາດ!</strong> ' . $error;
    echo '</div>';
} else {
    // แสดงหน้าที่ถูกร้องขอ
    switch ($page) {
        case 'register':
            include_once '../templates/register.php';
            break;
        case 'students':
            include_once '../templates/students-list.php';
            break;
        case 'search':
            include_once '../templates/search.php';
            break;
        case 'student-detail':
            include_once '../templates/student-detail.php';
            break;
        case 'export-reports':
            include_once '../templates/export-reports.php';
            break; 
        case 'export-processor':
            include_once '../templates/export-processor.php';
            break;
        case 'student-edit':
            include_once '../templates/student-edit.php';
            break;
        // สำหรับหน้าแสดงผลหลังลงทะเบียนสำเร็จ
        case 'registration-success':
            // ตรวจสอบข้อมูล session หรือพารามิเตอร์ที่จำเป็น
            
            // เริ่มต้นด้วยการรวม header
            $pageTitle = "ລົງທະບຽນສຳເລັດ";
            include_once '../templates/includes/header.php'; // แทนที่ด้วยไฟล์ header จริงของคุณ
            
            // รวมเนื้อหาหลัก
            include_once '../templates/registration-success.php';
            
            // ปิดท้ายด้วย footer
            include_once '../templates/includes/footer.php'; // แทนที่ด้วยไฟล์ footer จริงของคุณ
            break;
        default:
            // หน้าหลัก
            include_once '../templates/dashboard.php';
            break;
    }
}

// รวมส่วนท้ายเข้า
include_once '../templates/components/footer.php';
?>