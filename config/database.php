<?php
// filepath: c:\xampp\htdocs\register-learning\config\database.php

/**
 * ຄລາສເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ
 */
class Database {
    // Database credentials
    private $host = "localhost";
    private $db_name = "register-learning";
    private $username = "root";
    private $password = "";
    public $conn;

    /**
     * ຟັງຊັນເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ
     * @return PDO connection object
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch(PDOException $e) {
            throw $e;
        }

        return $this->conn;
    }
}
?>