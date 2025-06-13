<?php
// filepath: c:\xampp\htdocs\register-learning\src\classes\QrCodeGenerator.php

require_once __DIR__ . '/../../vendor/autoload.php';

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;

class QrCodeGenerator {
    /**
     * ສ້າງ QR Code ສຳລັບນັກສຶກສາ
     *
     * @param array $studentData ຂໍ້ມູນນັກສຶກສາ
     * @return array ຂໍ້ມູນ QR code (data_url, mime_type)
     */
    public static function generateStudentQrCode(array $studentData): array {
        // สร้าง URL ที่ชี้ไปที่หน้ารายละเอียดนักศึกษา
        $baseUrl = self::getBaseUrl();
        $studentId = $studentData['id'];
        
        // สร้าง URL แบบสมบูรณ์
        $url = "{$baseUrl}index.php?page=student-detail&id={$studentId}";
        
        // ສ້າງ QR Code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($url) // ใช้ URL แทนที่ JSON
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();
        
        // ຮັບ data URL ແລະ mime type
        return [
            'data_url' => $result->getDataUri(),
            'mime_type' => $result->getMimeType(),
            'url' => $url // เก็บ URL ไว้ใช้แสดงหรืออ้างอิงภายหลัง
        ];
    }
    
    /**
     * ບັນທຶກໄຟລ QR Code
     *
     * @param array $studentData ຂໍ້ມູນນັກສຶກສາ
     * @return string ເສັ້ນທາງໄຟລ
     */
    public static function saveStudentQrCode(array $studentData): string {
        // ກຳນົດໂຟລເດີເກັບໄຟລ
        $targetDir = __DIR__ . '/../../public/assets/uploads/qrcodes/';
        
        // ສ້າງໂຟລເດີຖ້າຍັງບໍ່ມີ
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        
        // ກຳນົດຊື່ໄຟລ
        $fileName = 'student_' . $studentData['id'] . '_' . time() . '.png';
        $filePath = $targetDir . $fileName;
        
        // สร้าง URL ที่ชี้ไปที่หน้ารายละเอียดนักศึกษา
        $baseUrl = self::getBaseUrl();
        $studentId = $studentData['id'];
        $url = "{$baseUrl}index.php?page=student-detail&id={$studentId}";
        
        // ສ້າງ QR Code
        $result = Builder::create()
            ->writer(new PngWriter())
            ->writerOptions([])
            ->data($url) // ใช้ URL แทนที่ JSON
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(300)
            ->margin(10)
            ->roundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->build();
        
        // ບັນທຶກ QR Code ລົງໄຟລ
        $result->saveToFile($filePath);
        
        return $fileName;
    }
    
    /**
     * ດຶງ Base URL ຂອງເວັບໄຊ
     * @return string Base URL
     */
    private static function getBaseUrl(): string {
        // ถ้ามีค่าคงที่ BASE_URL กำหนดไว้
        if (defined('BASE_URL')) {
            return BASE_URL;
        }
        
        // หาก็ไม่มีค่าคงที่ BASE_URL จะสร้างขึ้นมาจาก $_SERVER
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        $path = $path !== '/' ? rtrim($path, '/') . '/' : '/';
        
        return "$protocol://$host$path";
    }
}