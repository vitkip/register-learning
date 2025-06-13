<?php
// filepath: /register-learning/register-learning/src/classes/Major.php

/**
 * ຄລາສຈັດການຂໍ້ມູນສາຂາວິຊາ
 */
class Major {
    private $conn;
    private $table_name = "majors";
    
    // ຄຸນສົມບັດຂອງວັດຖຸ
    public $id;
    public $name;
    public $description;
    
    /**
     * Constructor - ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
     * @param PDO $db ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * ດຶງຂໍ້ມູນສາຂາທັງໝົດ
     * @return array ລາຍການສາຂາ
     */
    public function readAll() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY name";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database error in Major::readAll(): " . $e->getMessage());
            return [];
        }
    }
    
    // Function to create a new major
    public function create($name, $description) {
        $query = "INSERT INTO " . $this->table_name . " (name, description) VALUES (:name, :description)";
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));

        // bind values
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Function to read all majors
    public function read() {
        $query = "SELECT * FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    // Function to read a single major by ID
    public function readOne($id) {
        $query = "SELECT * FROM majors WHERE id = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $row : [];
    }

    // Function to update a major
    public function update($id, $name, $description) {
        $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // sanitize
        $name = htmlspecialchars(strip_tags($name));
        $description = htmlspecialchars(strip_tags($description));
        $id = htmlspecialchars(strip_tags($id));

        // bind values
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':id', $id);

        // execute query
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Function to delete a major
    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    /**
     * ດຶງຂໍ້ມູນສາຂາຕາມ ID
     * @param int $id ລະຫັດສາຂາ
     * @return array ຂໍ້ມູນສາຂາ
     */
    public function readById($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Major::readById(): " . $e->getMessage());
            return false;
        }
    }
}
?>