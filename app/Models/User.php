<?php

require_once 'app/Core/Model.php';

/**
 * User Model
 */
class UserModel extends Model {
    protected $table = 'users';
    protected $fillable = [
        'username', 'password', 'email', 'first_name', 
        'last_name', 'role', 'is_active'
    ];

    /**
     * Get active users
     */
    public function getActive() {
        return $this->where('is_active = ?', [1]);
    }

    /**
     * Get user by username
     */
    public function getByUsername($username) {
        return $this->first('username = ? AND is_active = 1', [$username]);
    }

    /**
     * Check if username is unique
     */
    public function isUsernameUnique($username, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE username = ?";
        $params = [$username];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Check if email is unique
     */
    public function isEmailUnique($email, $excludeId = null) {
        $query = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];

        if ($excludeId) {
            $query .= " AND id != ?";
            $params[] = $excludeId;
        }

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Create user with hashed password
     */
    public function createUser($data) {
        if (isset($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword($id, $newPassword) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        return $this->update($id, ['password' => $hashedPassword]);
    }

    /**
     * Update last login
     */
    public function updateLastLogin($id) {
        return $this->update($id, ['last_login' => date('Y-m-d H:i:s')]);
    }
}