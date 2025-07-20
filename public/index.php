<?php
// filepath: c:\xampp\htdocs\register-learning\public\index.php

// เริ่ม session
session_start();

// การกำหนดค่าเริ่มต้น
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../src/helpers/functions.php';

// Load Composer autoloader for vendor libraries
require_once '../vendor/autoload.php';

// QR Code classes for download handling
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

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

// Handle QR code download requests BEFORE any HTML output
if ($page === 'qrcode' && isset($_GET['download']) && !empty($_GET['download'])) {
    $url = $_GET['url'] ?? '';
    $size = (int)($_GET['size'] ?? 300);
    
    if ($size < 100 || $size > 1000) {
        $size = 300;
    }
    
    if (!empty($url)) {
        try {
            // Clean and validate URL
            $url = trim($url);
            if (!filter_var($url, FILTER_VALIDATE_URL) && !preg_match('/^https?:\/\//', $url)) {
                $url = 'http://' . $url;
            }
            
            // Create filename based on URL hash
            $urlHash = md5($url);
            $filename = "qrcode_{$urlHash}_{$size}.png";
            $filePath = BASE_PATH . "/public/qrcodes/{$filename}";
            
            // Check if file exists
            if (file_exists($filePath)) {
                // Clean any output buffers
                while (ob_get_level()) {
                    ob_end_clean();
                }
                
                $downloadFilename = 'qrcode_' . date('Y-m-d_H-i-s') . '.png';
                
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="' . $downloadFilename . '"');
                header('Content-Length: ' . filesize($filePath));
                header('Cache-Control: no-cache, must-revalidate');
                header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
                
                readfile($filePath);
                exit;
            }
        } catch (Exception $e) {
            error_log("QR download error: " . $e->getMessage());
        }
    }
}

// ฟังก์ชันสำหรับจัดการไฟล์อัพโหลด
function handleFileUpload($file) {
    if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'ບໍ່ມີໄຟລ໌ຫຼືເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດ'];
    }
    
    // ตรวจสอบขนาดไฟล์ (5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'error' => 'ຂະໜາດໄຟລ໌ໃຫຍ່ເກີນໄປ'];
    }
    
    // ตรวจสอบประเภทไฟล์
    $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'error' => 'ປະເພດໄຟລ໌ບໍ່ຖືກຕ້ອງ'];
    }
    
    // สร้างโฟลเดอร์ถ้าไม่มี
    $uploadDir = BASE_PATH . '/public/uploads/photos/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // สร้างชื่อไฟล์ใหม่
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'student_' . time() . '_' . mt_rand(1000, 9999) . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    // ย้ายไฟล์
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'filepath' => $filepath];
    } else {
        return ['success' => false, 'error' => 'ບໍ່ສາມາດບັນທຶກໄຟລ໌ໄດ້'];
    }
}

// ตรวจสอบว่ามีการส่งฟอร์ม POST และประมวลผลการลงทะเบียน
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'register' && $action === 'process') {
    try {
        // รับข้อมูลจากฟอร์ม
        $studentData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'dob' => $_POST['dob'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'village' => $_POST['village'] ?? '',
            'district' => $_POST['district'] ?? '',
            'province' => $_POST['province'] ?? '',
            'accommodation_type' => $_POST['accommodation_type'] ?? '',
            'major_id' => $_POST['major_id'] ?? '',
            'academic_year_id' => $_POST['academic_year_id'] ?? '',
            'previous_school' => $_POST['previous_school'] ?? ''
        ];
        
        // จัดการไฟล์รูปภาพ
        $photoResult = handleFileUpload($_FILES['photo'] ?? null);
        if ($photoResult['success']) {
            $studentData['photo'] = $photoResult['filename'];
        } else {
            throw new Exception($photoResult['error']);
        }
        
        // สร้าง student object
        require_once BASE_PATH . '/src/classes/Student.php';
        $student = new Student($db);
        
        // ตั้งค่าข้อมูลให้ student object
        foreach ($studentData as $key => $value) {
            if (property_exists($student, $key)) {
                $student->$key = $value;
            }
        }
        
        // บันทึกข้อมูล
        if ($student->create()) {
            // ดึงข้อมูลที่เพิ่งสร้างจาก database
            $newStudentId = $student->id;
            
            // Query ข้อมูลเต็มรูปแบบ
            $query = "SELECT s.*, 
                             m.name as major_name,
                             ay.year as academic_year_name
                      FROM students s
                      LEFT JOIN majors m ON s.major_id = m.id
                      LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                      WHERE s.id = ?";
            
            $stmt = $db->prepare($query);
            $stmt->execute([$newStudentId]);
            $studentFullData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($studentFullData) {
                // สร้าง student_id ถ้ายังไม่มี
                if (empty($studentFullData['student_id'])) {
                    $studentId = 'STU' . str_pad($newStudentId, 6, '0', STR_PAD_LEFT);
                    $updateQuery = "UPDATE students SET student_id = ? WHERE id = ?";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->execute([$studentId, $newStudentId]);
                    $studentFullData['student_id'] = $studentId;
                }
                
                // สร้าง QR Code
                require_once BASE_PATH . '/src/classes/QrCodeGenerator.php';
                $qrCodeData = QrCodeGenerator::generateStudentQrCode($studentFullData);
                
                // เก็บข้อมูลใน session
                $_SESSION['student_data'] = $studentFullData;
                $_SESSION['qr_code_data'] = $qrCodeData;
                $_SESSION['registration_success'] = true;
                $_SESSION['show_success_alert'] = true;
                
                // Debug log
                error_log("Registration successful - Student ID: " . $newStudentId);
                
                // Redirect
                header("Location: " . BASE_URL . "index.php?page=registration-success");
                exit;
                
            } else {
                throw new Exception('ບໍ່ສາມາດດຶງຂໍ້ມູນນັກສຶກສາທີ່ຫາກໍ່ສ້າງໄດ້');
            }
            
        } else {
            throw new Exception('ບໍ່ສາມາດບັນທຶກຂໍ້ມູນລົງຖານຂໍ້ມູນໄດ້');
        }
        
    } catch (Exception $e) {
        error_log("Registration error: " . $e->getMessage());
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header("Location: " . BASE_URL . "index.php?page=register");
        exit;
    }
}

// ตรวจสอบว่ามีการส่งฟอร์ม POST สำหรับการอัปเดตข้อมูลนักศึกษา
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $page === 'student-edit' && $action === 'update') {
    try {
        // ตรวจสอบว่ามี ID ของนักศึกษา
        if (!isset($_POST['id']) || empty($_POST['id'])) {
            throw new Exception('ບໍ່ພົບ ID ນັກສຶກສາ');
        }
        
        $studentId = (int)$_POST['id'];
        
        // รับข้อมูลจากฟอร์ม
        $studentData = [
            'first_name' => $_POST['first_name'] ?? '',
            'last_name' => $_POST['last_name'] ?? '',
            'gender' => $_POST['gender'] ?? '',
            'dob' => $_POST['dob'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'village' => $_POST['village'] ?? '',
            'district' => $_POST['district'] ?? '',
            'province' => $_POST['province'] ?? '',
            'accommodation_type' => $_POST['accommodation_type'] ?? '',
            'major_id' => $_POST['major_id'] ?? '',
            'academic_year_id' => $_POST['academic_year_id'] ?? '',
            'previous_school' => $_POST['previous_school'] ?? ''
        ];
        
        // สร้าง student object และดึงข้อมูลเดิม
        require_once BASE_PATH . '/src/classes/Student.php';
        $student = new Student($db);
        $existingData = $student->readOne($studentId);
        
        if (!$existingData) {
            throw new Exception('ບໍ່ພົບຂໍ້ມູນນັກສຶກສາທີ່ຕ້ອງການແກ້ໄຂ');
        }
        
        // จัดการไฟล์รูปภาพ
        if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
            $photoResult = handleFileUpload($_FILES['photo']);
            if ($photoResult['success']) {
                $studentData['photo'] = $photoResult['filename'];
                
                // ลบรูปภาพเดิมถ้ามี
                if (!empty($existingData['photo'])) {
                    $oldPhotoPath = BASE_PATH . '/public/uploads/photos/' . $existingData['photo'];
                    if (file_exists($oldPhotoPath)) {
                        unlink($oldPhotoPath);
                    }
                }
            } else {
                throw new Exception($photoResult['error']);
            }
        } else {
            // ใช้รูปภาพเดิม
            $studentData['photo'] = $existingData['photo'];
        }
        
        // ตั้งค่าข้อมูลให้ student object
        $student->id = $studentId;
        foreach ($studentData as $key => $value) {
            if (property_exists($student, $key)) {
                $student->$key = $value;
            }
        }
        
        // บันทึกข้อมูล
        if ($student->update()) {
            $_SESSION['message'] = 'ແກ້ໄຂຂໍ້ມູນນັກສຶກສາສຳເລັດແລ້ວ';
            $_SESSION['message_type'] = 'success';
            header("Location: " . BASE_URL . "index.php?page=student-detail&id=" . $studentId);
            exit;
        } else {
            throw new Exception('ບໍ່ສາມາດບັນທຶກການປ່ຽນແປງໄດ້');
        }
        
    } catch (Exception $e) {
        error_log("Student update error: " . $e->getMessage());
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = 'error';
        header("Location: " . BASE_URL . "index.php?page=student-edit&id=" . ($studentId ?? $_POST['id'] ?? ''));
        exit;
    }
}

// ตรวจสอบหน้า registration-success
if ($page === 'registration-success') {
    if (isset($_SESSION['student_data'])) {
        $studentData = $_SESSION['student_data'];
        $qrCodeData = $_SESSION['qr_code_data'] ?? ['success' => false, 'error' => 'QR Code not generated'];
        $showSuccessAlert = $_SESSION['show_success_alert'] ?? false;
        
        include BASE_PATH . '/templates/registration-success.php';
        exit;
    } else {
        $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນການລົງທະບຽນ ກະລຸນາລອງໃໝ່";
        $_SESSION['message_type'] = "error";
        header("Location: " . BASE_URL . "index.php?page=register");
        exit;
    }
}

// ตรวจสอบหน้า students
if ($page === 'students') {
    require_once '../src/classes/Student.php';
    require_once '../src/classes/Major.php';
    require_once '../src/classes/AcademicYear.php';
    
    $student = new Student($db);
    $majorObj = new Major($db);
    $yearObj = new AcademicYear($db);
    
    $majors = $majorObj->readAll();
    $academicYears = $yearObj->readAll();
    
    // รับค่า parameters
    $current_search = $_GET['search'] ?? '';
    $current_major = intval($_GET['major'] ?? 0);
    $current_year = intval($_GET['year'] ?? 0);
    $current_page = intval($_GET['p'] ?? 1);
    $students_per_page = intval($_GET['students_per_page'] ?? 10);
    
    // จัดการการลบ
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $delete_id = intval($_GET['id']);
        
        if ($student->deleteById($delete_id)) {
            $_SESSION['message'] = "ລຶບຂໍ້ມູນນັກສຶກສາສຳເລັດແລ້ວ";
            $_SESSION['message_type'] = "success";
        } else {
            $_SESSION['message'] = "ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ";
            $_SESSION['message_type'] = "error";
        }
        
        header("Location: index.php?page=students");
        exit;
    }
    
    // สร้าง parameters สำหรับ search
    $search_params = [
        'search' => $current_search,
        'major' => $current_major,
        'year' => $current_year
    ];
    
    // นับจำนวนนักศึกษาทั้งหมด
    $total_students = $student->countStudentsWithFilter($search_params);
    
    // คำนวณ pagination
    $total_pages = ceil($total_students / $students_per_page);
    $current_page = max(1, min($current_page, $total_pages));
    $offset = ($current_page - 1) * $students_per_page;
    
    // ดึงข้อมูลนักศึกษา
    $pagination_params = array_merge($search_params, [
        'limit' => $students_per_page,
        'offset' => $offset
    ]);
    
    $students = $student->getStudentsWithPagination($pagination_params);
    
    // แก้ไขตรงนี้: เรียก header ก่อนเนื้อหาหลัก
    include_once '../templates/components/header.php';
    include BASE_PATH . '/templates/students-list.php';
    // เรียก footer หลังเนื้อหาหลัก
    include_once '../templates/components/footer.php';
    exit;
}

// จัดการหน้าพิมพ์บัตรนักศึกษา
if ($page === 'student-card' && isset($_GET['id'])) {
    require_once '../src/classes/Student.php';
    
    $student = new Student($db);
    $student_id = (int)$_GET['id'];
    
    if ($student_id <= 0) {
        $_SESSION['message'] = "ID นักศึกษาไม่ถูกต้อง";
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=students");
        exit;
    }
    
    $student_data = $student->getStudentForCard($student_id);
    
    if (!$student_data) {
        $_SESSION['message'] = "ເກີດຂໍ້ຜິດພາດໃນການດຶງຂໍ້ມູນສຳລັບພິມບັດ";
        $_SESSION['message_type'] = "error";
        header("Location: index.php?page=students");
        exit;
    }
    
    include '../templates/student-card.php';
    exit;
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
            // ดึงข้อมูลสาขาและปีการศึกษา
            require_once '../src/classes/Major.php';
            require_once '../src/classes/AcademicYear.php';
            
            $majorObj = new Major($db);
            $yearObj = new AcademicYear($db);
            
            $majors = $majorObj->readAll();
            $academicYears = $yearObj->readAll();
            
            include '../templates/register.php';
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
                require_once '../src/classes/Major.php';
                require_once '../src/classes/AcademicYear.php';
                
                $student = new Student($db);
                $student_data = $student->readOne((int)$_GET['id']);
                
                if (!$student_data) {
                    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ";
                    $_SESSION['message_type'] = "error";
                    header("Location: index.php?page=students");
                    exit;
                }
                
                // ดึงข้อมูลสาขาและปีการศึกษา
                $majorObj = new Major($db);
                $yearObj = new AcademicYear($db);
                
                $majors = $majorObj->readAll();
                $academicYears = $yearObj->readAll();
                
                include '../templates/student-edit.php';
            } else {
                header("Location: index.php?page=students");
                exit;
            }
            break;
            
        case 'qrcode':
            // Check if QR code libraries are available
            if (!class_exists('Endroid\QrCode\QrCode')) {
                echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">';
                echo '<strong>Error:</strong> QR Code library not available. ';
                echo '<a href="qrcode.php" class="underline">Try direct QR generator</a>';
                echo '</div>';
                include '../templates/dashboard.php';
            } else {
                // Include QR Code generator template
                include '../templates/qrcode.php';
            }
            break;
            
        default:
            include '../templates/dashboard.php';
            break;
    }
}

// รวมส่วนท้าย (Footer)
include_once '../templates/components/footer.php';
?>