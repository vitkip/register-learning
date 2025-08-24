<?php

/**
 * Authentication class
 */
class Auth {
    private $db;
    private $user = null;

    public function __construct($db) {
        $this->db = $db;
        $this->loadUser();
    }

    /**
     * Load user from session
     */
    private function loadUser() {
        $userId = Session::get('user_id');
        if ($userId) {
            $this->user = $this->getUserById($userId);
        }
    }

    /**
     * Get user by ID
     */
    private function getUserById($id) {
        $query = "SELECT * FROM users WHERE id = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Attempt to log in a user
     */
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = ? AND is_active = 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Set session
            Session::set('user_id', $user['id']);
            Session::set('username', $user['username']);
            Session::set('user_role', $user['role']);
            
            // Load user data
            $this->user = $user;
            
            // Log login event
            $this->logEvent($user['id'], 'login', 'users', $user['id'], 'User logged in');
            
            return true;
        }

        return false;
    }

    /**
     * Log out the user
     */
    public function logout() {
        if ($this->user) {
            // Log logout event
            $this->logEvent($this->user['id'], 'logout', 'users', $this->user['id'], 'User logged out');
        }
        
        Session::remove('user_id');
        Session::remove('username');
        Session::remove('user_role');
        $this->user = null;
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return $this->user !== null;
    }

    /**
     * Check if user has admin role
     */
    public function isAdmin() {
        return $this->user && $this->user['role'] === 'admin';
    }

    /**
     * Get current user
     */
    public function user() {
        return $this->user;
    }

    /**
     * Get user ID
     */
    public function id() {
        return $this->user ? $this->user['id'] : null;
    }

    /**
     * Get user role
     */
    public function role() {
        return $this->user ? $this->user['role'] : null;
    }

    /**
     * Check if user has permission
     */
    public function hasPermission($permission) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        // Admin has all permissions
        if ($this->isAdmin()) {
            return true;
        }

        // Add permission logic here based on role
        return false;
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $query = "UPDATE users SET last_login = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$userId]);
    }

    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Generate random password
     */
    public static function generatePassword($length = 8) {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        return substr(str_shuffle($chars), 0, $length);
    }

    /**
     * Log system event
     */
    private function logEvent($userId, $action, $tableName, $recordId, $message, $level = 'info') {
        try {
            $query = "INSERT INTO system_logs (user_id, action, table_name, record_id, message, level, ip_address, user_agent) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                $userId,
                $action,
                $tableName,
                $recordId,
                $message,
                $level,
                $_SERVER['REMOTE_ADDR'] ?? null,
                $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);
        } catch (Exception $e) {
            error_log("Failed to log event: " . $e->getMessage());
        }
    }
}