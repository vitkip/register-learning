<?php
// filepath: c:\xampp\htdocs\register-learning\templates\student-card.php

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=students');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($db) || !$db) {
    die('Database connection not available');
}

// ตรวจสอบว่ามีข้อมูลนักศึกษาหรือไม่
if (!isset($student_data) || !$student_data) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-md mx-auto mt-10">';
    echo '<h3 class="font-bold text-lg mb-2">ຂໍ້ຜິດພາດ!</h3>';
    echo '<p>ບໍ່ສາມາດດຶງຂໍ້ມູນນັກສຶກສາໄດ້</p>';
    echo '<div class="mt-4">';
    echo '<a href="' . BASE_URL . 'index.php?page=students" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ກັບຄືນຫາລາຍຊື່</a>';
    echo '</div>';
    echo '</div>';
    exit;
}

// โหลด mPDF
require_once BASE_PATH . '/vendor/autoload.php';

try {
    // สร้างโฟลเดอร์ temp ถ้าไม่มี
    $tempDir = BASE_PATH . '/temp';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // สร้าง mPDF instance พร้อมตั้งค่าฟอนต์
    $mpdf = new \Mpdf\Mpdf([
        'mode' => 'utf-8',
        'format' => [85.6, 53.98], // ขนาดบัตรเครดิต (มม.)
        'orientation' => 'L', // แนวนอน
        'margin_left' => 0,
        'margin_right' => 0,
        'margin_top' => 0,
        'margin_bottom' => 0,
        'margin_header' => 0,
        'margin_footer' => 0,
        'tempDir' => $tempDir,
        'fontDir' => [
            BASE_PATH . '/public/fonts', // โฟลเดอร์ฟอนต์
        ],
        'fontdata' => [
            'phetsarath' => [
                'R' => 'PhetsarathOT.ttf', // Regular
                'useOTL' => 0xFF,
                'useKashida' => 75
            ]
        ],
        'default_font' => 'phetsarath'
    ]);

    // เพิ่มฟอนต์ Phetsarath OT
    $fontPath = BASE_PATH . '/public/fonts/PhetsarathOT.ttf';
    if (file_exists($fontPath)) {
        $mpdf->AddFont('phetsarath', 'R', $fontPath);
    }

    // กำหนด CSS สำหรับบัตร
    $css = '
    <style>
        body {
            font-family: "phetsarath", "DejaVu Sans", sans-serif;
            margin: 0;
            padding: 0;
            font-size: 8px;
            line-height: 1.2;
        }
        
        .card-container {
            width: 85.6mm;
            height: 53.98mm;
            position: relative;
            overflow: hidden;
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        }
        
        .card-content {
            position: relative;
            z-index: 2;
            height: 100%;
            padding: 3mm;
            color: white;
            display: table;
            width: 100%;
            box-sizing: border-box;
        }
        
        .header-section {
            display: table;
            width: 100%;
            margin-bottom: 2mm;
        }
        
        .logo-cell {
            display: table-cell;
            width: 15mm;
            vertical-align: top;
        }
        
        .school-logo {
            width: 10mm;
            height: 10mm;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 50%;
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            color: #d97706;
            font-size: 12px;
            font-weight: bold;
            font-family: "phetsarath", sans-serif;
        }
        
        .school-info {
            margin-top: 1mm;
            font-size: 6px;
            font-weight: bold;
            text-align: center;
            line-height: 1.1;
            font-family: "phetsarath", sans-serif;
        }
        
        .photo-cell {
            display: table-cell;
            width: 18mm;
            text-align: right;
            vertical-align: top;
        }
        
        .student-photo {
            width: 15mm;
            height: 15mm;
            border-radius: 2mm;
            border: 1px solid white;
            object-fit: cover;
            background: rgba(255, 255, 255, 0.9);
        }
        
        .no-photo {
            width: 15mm;
            height: 15mm;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 2mm;
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            color: #666;
            font-size: 10px;
            font-family: "phetsarath", sans-serif;
        }
        
        .student-info {
            margin: 3mm 0;
            text-align: center;
        }
        
        .student-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 1mm;
            line-height: 1.1;
            font-family: "phetsarath", sans-serif;
        }
        
        .student-details {
            font-size: 6px;
            opacity: 0.9;
            line-height: 1.2;
            font-family: "phetsarath", sans-serif;
        }
        
        .footer-section {
            display: table;
            width: 100%;
            margin-top: auto;
        }
        
        .student-id-cell {
            display: table-cell;
            width: 60%;
            vertical-align: bottom;
        }
        
        .student-id-label {
            font-size: 5px;
            opacity: 0.75;
            margin-bottom: 0.5mm;
            font-family: "phetsarath", sans-serif;
        }
        
        .student-id {
            font-size: 8px;
            font-weight: bold;
            font-family: "phetsarath", monospace;
        }
        
        .qr-cell {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: bottom;
        }
        
        .qr-code {
            width: 12mm;
            height: 12mm;
            background: white;
            border-radius: 1mm;
            display: table-cell;
            text-align: center;
            vertical-align: middle;
            color: #374151;
            font-size: 8px;
            font-family: "phetsarath", sans-serif;
        }
        
        /* สำหรับด้านหลัง */
        .card-back {
            background: linear-gradient(135deg, #1f2937 0%, #374151 100%);
        }
        
        .back-content {
            padding: 3mm;
            color: white;
            height: 100%;
            box-sizing: border-box;
        }
        
        .back-header {
            text-align: center;
            margin-bottom: 3mm;
            border-bottom: 0.5px solid rgba(255,255,255,0.3);
            padding-bottom: 2mm;
        }
        
        .back-title {
            font-size: 8px;
            font-weight: bold;
            font-family: "phetsarath", sans-serif;
        }
        
        .info-grid {
            margin-bottom: 3mm;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 1.5mm;
        }
        
        .info-cell {
            display: table-cell;
            width: 50%;
            font-size: 6px;
            vertical-align: top;
            padding-right: 1mm;
        }
        
        .info-label {
            opacity: 0.75;
            margin-bottom: 0.5mm;
            font-family: "phetsarath", sans-serif;
        }
        
        .info-value {
            font-weight: bold;
            font-family: "phetsarath", sans-serif;
        }
        
        .footer-info {
            margin-top: auto;
            border-top: 0.5px solid rgba(255,255,255,0.3);
            padding-top: 1mm;
            text-align: center;
        }
        
        .issue-date {
            font-size: 5px;
            opacity: 0.75;
            font-family: "phetsarath", sans-serif;
        }
        
        .contact-info {
            font-size: 4px;
            margin-top: 1mm;
            opacity: 0.6;
            font-family: "phetsarath", sans-serif;
        }
    </style>';

    // ฟังก์ชันสร้าง HTML สำหรับด้านหน้า
    function generateFrontCardHTML($student_data) {
        global $BASE_PATH;
        
        // จัดการรูปภาพ
        $photoHTML = '';
        $photo_path = BASE_PATH . '/public/uploads/photos/';
        
        if (!empty($student_data['photo']) && file_exists($photo_path . $student_data['photo'])) {
            // แปลงรูปเป็น base64
            $imageType = pathinfo($photo_path . $student_data['photo'], PATHINFO_EXTENSION);
            $imageData = base64_encode(file_get_contents($photo_path . $student_data['photo']));
            $base64 = 'data:image/' . $imageType . ';base64,' . $imageData;
            
            $photoHTML = '<img src="' . $base64 . '" class="student-photo" alt="ຮູບນັກສຶກສາ">';
        } else {
            $photoHTML = '<div class="no-photo">ບໍ່ມີຮູບ</div>';
        }

        // สร้าง student_id ถ้าไม่มี
        $displayStudentId = $student_data['student_id'] ?? ('STU' . str_pad($student_data['id'], 6, '0', STR_PAD_LEFT));

        $html = '
        <div class="card-container">
            <div class="card-content">
                <!-- ส่วนหัว -->
                <div class="header-section">
                    <div class="logo-cell">
                        <div class="school-logo">🎓</div>
                        <div class="school-info">
                            <div>ວິທະຍາໄລ</div>
                            <div>ການສຶກສາ</div>
                            <div>ອົງຕື້ສັງຄະ</div>
                        </div>
                    </div>
                    <div class="photo-cell">
                        ' . $photoHTML . '
                    </div>
                </div>
                
                <!-- ข้อมูลนักศึกษา -->
                <div class="student-info">
                    <div class="student-name">
                        ' . htmlspecialchars($student_data['first_name'] . ' ' . $student_data['last_name']) . '
                    </div>
                    <div class="student-details">
                        <div>' . htmlspecialchars($student_data['major_name'] ?? 'ບໍ່ລະບຸສາຂາ') . '</div>
                        <div>ປີການສຶກສາ: ' . htmlspecialchars($student_data['academic_year_name'] ?? 'ບໍ່ລະບຸ') . '</div>
                    </div>
                </div>
                
                <!-- ส่วนล่าง -->
                <div class="footer-section">
                    <div class="student-id-cell">
                        <div class="student-id-label">ລະຫັດນັກສຶກສາ</div>
                        <div class="student-id">' . htmlspecialchars($displayStudentId) . '</div>
                    </div>
                    <div class="qr-cell">
                        <div class="qr-code">QR</div>
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    // ฟังก์ชันสร้าง HTML สำหรับด้านหลัง
    function generateBackCardHTML($student_data) {
        // จัดการวันที่เกิด
        $dobFormatted = 'ບໍ່ລະບຸ';
        if (!empty($student_data['dob'])) {
            try {
                $date = new DateTime($student_data['dob']);
                $dobFormatted = $date->format('d/m/Y');
            } catch (Exception $e) {
                $dobFormatted = 'ບໍ່ລະບຸ';
            }
        }

        // แปลงเพศ
        $genderText = '';
        switch ($student_data['gender'] ?? '') {
            case 'male': $genderText = 'ຊາຍ'; break;
            case 'female': $genderText = 'ຍິງ'; break;
            case 'ຊາຍ': $genderText = 'ຊາຍ'; break;
            case 'ຍິງ': $genderText = 'ຍິງ'; break;
            case 'ພຣະ': $genderText = 'ພຣະ'; break;
            case 'ສາມະເນນ': $genderText = 'ສາມະເນນ'; break;
            default: $genderText = 'ບໍ່ລະບຸ'; break;
        }

        $html = '
        <div class="card-container card-back">
            <div class="back-content">
                <div class="back-header">
                    <div class="back-title">ຂໍ້ມູນນັກສຶກສາ</div>
                    <div class="back-title">ວິທະຍາໄລການສຶກສາ ອົງຕື້ສັງຄະ</div>
                </div>
                
                <div class="info-grid">
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">ເພດ:</div>
                            <div class="info-value">' . $genderText . '</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">ວັນເກີດ:</div>
                            <div class="info-value">' . $dobFormatted . '</div>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <div class="info-cell">
                            <div class="info-label">ເບີໂທ:</div>
                            <div class="info-value">' . htmlspecialchars($student_data['phone'] ?? 'ບໍ່ລະບຸ') . '</div>
                        </div>
                        <div class="info-cell">
                            <div class="info-label">ທີ່ພັກ:</div>
                            <div class="info-value">' . htmlspecialchars($student_data['accommodation_type'] ?? 'ບໍ່ລະບຸ') . '</div>
                        </div>
                    </div>
                </div>
                
                <div class="footer-info">
                    <div class="issue-date">ອອກໃຫ້ເມື່ອ: ' . date('d/m/Y') . '</div>
                    <div class="contact-info">
                        ວິທະຍາໄລການສຶກສາ ອົງຕື້ສັງຄະ<br>
                        📞 +856-20-xxxx-xxxx | 🌐 www.college.edu.la
                    </div>
                </div>
            </div>
        </div>';

        return $html;
    }

    // ตรวจสอบว่าต้องการด้านไหน
    $side = $_GET['side'] ?? 'front'; // front, back, both
    $output = $_GET['output'] ?? 'I'; // D=Download, I=Inline

    // เขียน CSS
    $mpdf->WriteHTML($css, \Mpdf\HTMLParserMode::HEADER_CSS);

    if ($side === 'both') {
        // สร้างทั้งสองด้าน
        $frontHTML = generateFrontCardHTML($student_data);
        $mpdf->WriteHTML($frontHTML, \Mpdf\HTMLParserMode::HTML_BODY);
        
        // เพิ่มหน้าใหม่สำหรับด้านหลัง
        $mpdf->AddPage();
        $backHTML = generateBackCardHTML($student_data);
        $mpdf->WriteHTML($backHTML, \Mpdf\HTMLParserMode::HTML_BODY);
        
    } elseif ($side === 'back') {
        // สร้างเฉพาะด้านหลัง
        $backHTML = generateBackCardHTML($student_data);
        $mpdf->WriteHTML($backHTML, \Mpdf\HTMLParserMode::HTML_BODY);
        
    } else {
        // สร้างเฉพาะด้านหน้า (default)
        $frontHTML = generateFrontCardHTML($student_data);
        $mpdf->WriteHTML($frontHTML, \Mpdf\HTMLParserMode::HTML_BODY);
    }

    // กำหนดชื่อไฟล์
    $studentName = preg_replace('/[^a-zA-Z0-9_]/', '', $student_data['first_name'] . '_' . $student_data['last_name']);
    $displayStudentId = $student_data['student_id'] ?? $student_data['id'];
    
    $filename = 'student_card_' . 
                $displayStudentId . '_' . 
                $studentName . '_' .
                $side . '_' . 
                date('Ymd_His') . '.pdf';

    // ส่งออก PDF
    if ($output === 'D') {
        // ดาวน์โหลด
        $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    } elseif ($output === 'F') {
        // บันทึกไฟล์
        $downloadDir = BASE_PATH . '/public/downloads/';
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }
        $mpdf->Output($downloadDir . $filename, \Mpdf\Output\Destination::FILE);
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'สร้าง PDF สำเร็จ',
            'filename' => $filename,
            'path' => $downloadDir . $filename
        ]);
    } else {
        // แสดงในเบราว์เซอร์ (default)
        $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
    }

} catch (Exception $e) {
    error_log("PDF Generation Error: " . $e->getMessage());
    
    if (isset($_GET['output']) && $_GET['output'] === 'F') {
        // ส่งคืน JSON error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    } else {
        // แสดง error page
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-md mx-auto mt-10">';
        echo '<h3 class="font-bold text-lg mb-2">ເກີດຂໍ້ຜິດພາດ!</h3>';
        echo '<p>ບໍ່ສາມາດສ້າງ PDF ໄດ້: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<div class="mt-4">';
        echo '<a href="' . BASE_URL . 'index.php?page=students" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ກັບຄືນຫາລາຍຊື່</a>';
        echo '</div>';
        echo '</div>';
    }
}
?>