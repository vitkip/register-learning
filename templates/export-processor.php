<?php
// ປະມວນຜົນການສົ່ງອອກລາຍງານ
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../src/classes/ExportManager.php';

// ເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ
$database = new Database();
$db = $database->getConnection();

// ສ້າງອັອບເຈັກການສົ່ງອອກ
$exportManager = new ExportManager($db);

try {
    // ຕັດສິນໃຈວ່າຄວນສົ່ງອອກປະເພດໃດ
    $type = $_GET['type'] ?? 'all';
    
    switch ($type) {
        case 'all':
            $exportManager->exportAllStudents();
            break;
            
        case 'by_major':
            if (!isset($_GET['major_id']) || empty($_GET['major_id'])) {
                throw new Exception("ກະລຸນາເລືອກສາຂາ");
            }
            $exportManager->exportStudentsByMajor((int)$_GET['major_id']);
            break;
            
        case 'by_year':
            if (!isset($_GET['year_id']) || empty($_GET['year_id'])) {
                throw new Exception("ກະລຸນາເລືອກປີການສຶກສາ");
            }
            $exportManager->exportStudentsByYear((int)$_GET['year_id']);
            break;
            
        case 'by_accommodation':
            if (!isset($_GET['accommodation_type']) || empty($_GET['accommodation_type'])) {
                throw new Exception("ກະລຸນາເລືອກປະເພດທີ່ພັກ");
            }
            $exportManager->exportStudentsByAccommodation($_GET['accommodation_type']);
            break;
            
        default:
            throw new Exception("ບໍ່ຮູ້ຈັກປະເພດລາຍງານ");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header("Location: index.php?page=export-reports");
    exit;
}