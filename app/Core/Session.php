<?php

/**
 * Session management class
 */
class Session {
    
    /**
     * Start session if not already started
     */
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set session data
     */
    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session data
     */
    public static function get($key, $default = null) {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has($key) {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session data
     */
    public static function remove($key) {
        self::start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Destroy all session data
     */
    public static function destroy() {
        self::start();
        session_destroy();
        $_SESSION = [];
    }

    /**
     * Generate CSRF token
     */
    public static function generateCSRF() {
        self::start();
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Validate CSRF token
     */
    public static function validateCSRF($token) {
        self::start();
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        return hash_equals($sessionToken, $token);
    }

    /**
     * Flash message - set and remove after one request
     */
    public static function flash($key, $value = null) {
        self::start();
        
        if ($value === null) {
            // Get flash message
            $flash = $_SESSION['flash'][$key] ?? null;
            if (isset($_SESSION['flash'][$key])) {
                unset($_SESSION['flash'][$key]);
            }
            return $flash;
        } else {
            // Set flash message
            $_SESSION['flash'][$key] = $value;
        }
    }

    /**
     * Check if flash message exists
     */
    public static function hasFlash($key) {
        self::start();
        return isset($_SESSION['flash'][$key]);
    }

    /**
     * Set flash message with type
     */
    public static function setMessage($message, $type = 'info') {
        self::flash('message', $message);
        self::flash('message_type', $type);
    }

    /**
     * Get flash message
     */
    public static function getMessage() {
        $message = self::flash('message');
        $type = self::flash('message_type') ?? 'info';
        
        return $message ? ['message' => $message, 'type' => $type] : null;
    }
}