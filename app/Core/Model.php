<?php

/**
 * Base Model class
 * All models should extend this class
 */
class Model {
    protected $db;
    protected $table;
    protected $primaryKey = 'id';
    protected $fillable = [];
    
    public function __construct($db = null) {
        if ($db) {
            $this->db = $db;
        } else {
            $database = new Database();
            $this->db = $database->getConnection();
        }
    }

    /**
     * Find a record by ID
     */
    public function find($id) {
        $query = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Find all records
     */
    public function findAll($orderBy = null) {
        $query = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
        }
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find records with conditions
     */
    public function where($conditions, $params = []) {
        $query = "SELECT * FROM {$this->table} WHERE {$conditions}";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Find first record with conditions
     */
    public function first($conditions, $params = []) {
        $query = "SELECT * FROM {$this->table} WHERE {$conditions} LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create a new record
     */
    public function create($data) {
        // Filter data based on fillable fields
        $filteredData = $this->filterFillable($data);
        
        $fields = array_keys($filteredData);
        $placeholders = ':' . implode(', :', $fields);
        
        $query = "INSERT INTO {$this->table} (" . implode(', ', $fields) . ") VALUES ({$placeholders})";
        
        $stmt = $this->db->prepare($query);
        
        // Bind parameters
        foreach ($filteredData as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        
        if ($stmt->execute()) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }

    /**
     * Update a record
     */
    public function update($id, $data) {
        // Filter data based on fillable fields
        $filteredData = $this->filterFillable($data);
        
        $fields = array_keys($filteredData);
        $setClause = implode(' = ?, ', $fields) . ' = ?';
        
        $query = "UPDATE {$this->table} SET {$setClause} WHERE {$this->primaryKey} = ?";
        
        $values = array_values($filteredData);
        $values[] = $id;
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute($values);
    }

    /**
     * Delete a record
     */
    public function delete($id) {
        $query = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$id]);
    }

    /**
     * Count total records
     */
    public function count($conditions = null, $params = []) {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($conditions) {
            $query .= " WHERE {$conditions}";
        }
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['total'] ?? 0;
    }

    /**
     * Get paginated results
     */
    public function paginate($page = 1, $perPage = 10, $conditions = null, $params = []) {
        $offset = ($page - 1) * $perPage;
        
        $query = "SELECT * FROM {$this->table}";
        if ($conditions) {
            $query .= " WHERE {$conditions}";
        }
        $query .= " LIMIT {$perPage} OFFSET {$offset}";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        
        return [
            'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $this->count($conditions, $params),
            'page' => $page,
            'perPage' => $perPage
        ];
    }

    /**
     * Filter data based on fillable fields
     */
    protected function filterFillable($data) {
        if (empty($this->fillable)) {
            return $data;
        }
        
        return array_intersect_key($data, array_flip($this->fillable));
    }

    /**
     * Execute raw query
     */
    public function query($sql, $params = []) {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}