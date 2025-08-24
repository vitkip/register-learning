<?php
// Start session
session_start();

// Set timezone
date_default_timezone_set('Asia/Vientiane');

// Configuration
require_once '../config/config.php';
require_once '../config/database.php';

// Core classes
require_once '../app/Core/Session.php';
require_once '../app/Core/Auth.php';
require_once '../app/Core/Router.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Model.php';
require_once '../app/Core/View.php';
require_once '../app/Core/Validator.php';
require_once '../app/Core/App.php';

// Initialize Session
Session::start();

// Load Composer autoloader
require_once '../vendor/autoload.php';

// QR Code classes for download handling
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

// Initialize database connection
try {
    $database = new Database();
    $db = $database->getConnection();
} catch (PDOException $e) {
    error_log("Database connection error: " . $e->getMessage());
    die("ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໄດ້. ກະລຸນາລອງໃໝ່ພາຍຫຼັງ.");
}

// Initialize authentication
$auth = new Auth($db);

// Check for public routes that don't require authentication
$url = $_GET['url'] ?? 'auth/login';
$urlParts = explode('/', $url);
$controller = $urlParts[0] ?? 'auth';

// Public routes (no authentication required)
$publicRoutes = ['auth'];

// If not a public route and user is not logged in, redirect to login
if (!in_array($controller, $publicRoutes) && !$auth->isLoggedIn()) {
    header('Location: ' . BASE_URL . 'auth/login');
    exit;
}

// If user is logged in and trying to access auth, redirect to dashboard
if ($controller === 'auth' && $auth->isLoggedIn() && $url !== 'auth/logout') {
    header('Location: ' . BASE_URL . 'dashboard');
    exit;
}

// Handle legacy QR code downloads before starting MVC
if (isset($_GET['page']) && $_GET['page'] === 'qrcode' && isset($_GET['download'])) {
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

// Start the MVC application
try {
    $app = new App();
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage());
    
    // Show basic error page
    http_response_code(500);
    echo '<!DOCTYPE html>
    <html lang="lo">
    <head>
        <meta charset="UTF-8">
        <title>ເກີດຂໍ້ຜິດພາດ</title>
        <style>body{font-family:sans-serif;text-align:center;padding:50px;}</style>
    </head>
    <body>
        <h1>ເກີດຂໍ້ຜິດພາດໃນລະບົບ</h1>
        <p>ກະລຸນາລອງໃໝ່ພາຍຫຼັງ ຫຼື ຕິດຕໍ່ຜູ້ຄຸ້ມຄອງລະບົບ</p>
        <a href="' . BASE_URL . '">ກັບໜ້າຫຼັກ</a>
    </body>
    </html>';
}
?>