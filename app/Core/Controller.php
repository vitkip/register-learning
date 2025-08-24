<?php

/**
 * Base Controller class
 * All controllers should extend this class
 */
class Controller {
    protected $db;
    protected $auth;

    public function __construct() {
        // Initialize database connection
        $database = new Database();
        $this->db = $database->getConnection();
        
        // Initialize authentication
        $this->auth = new Auth($this->db);
    }

    /**
     * Load a view file
     */
    protected function view($view, $data = []) {
        // Extract data array to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        require_once "app/Views/{$view}.php";
        
        // Get the content
        $content = ob_get_clean();
        
        // Load the layout with content
        require_once 'app/Views/layouts/main.php';
    }

    /**
     * Load a view without layout
     */
    protected function viewOnly($view, $data = []) {
        extract($data);
        require_once "app/Views/{$view}.php";
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url, $message = null, $type = 'info') {
        if ($message) {
            $_SESSION['message'] = $message;
            $_SESSION['message_type'] = $type;
        }
        
        header("Location: " . BASE_URL . $url);
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCSRF() {
        $token = $_POST['csrf_token'] ?? '';
        if (!Session::validateCSRF($token)) {
            $this->json(['error' => 'Invalid CSRF token'], 403);
        }
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth() {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('auth/login', 'ກະລຸນາເຂົ້າສູ່ລະບົບກ່ອນ', 'error');
        }
    }

    /**
     * Check if user has admin role
     */
    protected function requireAdmin() {
        $this->requireAuth();
        
        if (!$this->auth->isAdmin()) {
            $this->redirect('dashboard', 'ທ່ານບໍ່ມີສິດເຂົ້າເຖິງໜ້ານີ້', 'error');
        }
    }
}