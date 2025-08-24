<?php

/**
 * Input validation class
 */
class Validator {
    private $data = [];
    private $rules = [];
    private $errors = [];
    private $messages = [
        'required' => 'ຊ່ອງ :field ແມ່ນຈຳເປັນ',
        'email' => 'ຊ່ອງ :field ຕ້ອງເປັນອີເມລທີ່ຖືກຕ້ອງ',
        'min' => 'ຊ່ອງ :field ຕ້ອງມີຄວາມຍາວຢ່າງໜ້ອຍ :min ຕົວອັກສອນ',
        'max' => 'ຊ່ອງ :field ຕ້ອງມີຄວາມຍາວບໍ່ເກີນ :max ຕົວອັກສອນ',
        'numeric' => 'ຊ່ອງ :field ຕ້ອງເປັນຕົວເລກ',
        'integer' => 'ຊ່ອງ :field ຕ້ອງເປັນຈຳນວນເຕັມ',
        'date' => 'ຊ່ອງ :field ຕ້ອງເປັນວັນທີທີ່ຖືກຕ້ອງ',
        'unique' => 'ຊ່ອງ :field ໄດ້ຖືກນຳໃຊ້ແລ້ວ',
        'exists' => 'ຊ່ອງ :field ບໍ່ມີຢູ່ໃນລະບົບ',
        'image' => 'ຊ່ອງ :field ຕ້ອງເປັນໄຟລ໌ຮູບພາບ',
        'max_size' => 'ຊ່ອງ :field ມີຂະໜາດໃຫຍ່ເກີນກໍາໜົດ',
    ];

    public function __construct($data = []) {
        $this->data = $data;
    }

    /**
     * Set validation rules
     */
    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }

    /**
     * Set custom error messages
     */
    public function setMessages($messages) {
        $this->messages = array_merge($this->messages, $messages);
        return $this;
    }

    /**
     * Validate data
     */
    public function validate() {
        $this->errors = [];

        foreach ($this->rules as $field => $rules) {
            $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;
            
            foreach ($rulesArray as $rule) {
                $this->validateField($field, $rule);
            }
        }

        return empty($this->errors);
    }

    /**
     * Validate a single field
     */
    private function validateField($field, $rule) {
        $ruleParts = explode(':', $rule);
        $ruleName = $ruleParts[0];
        $ruleParam = $ruleParts[1] ?? null;

        $value = $this->data[$field] ?? null;

        switch ($ruleName) {
            case 'required':
                if (empty($value) && $value !== '0') {
                    $this->addError($field, 'required');
                }
                break;

            case 'email':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($field, 'email');
                }
                break;

            case 'min':
                if (!empty($value) && strlen($value) < (int)$ruleParam) {
                    $this->addError($field, 'min', ['min' => $ruleParam]);
                }
                break;

            case 'max':
                if (!empty($value) && strlen($value) > (int)$ruleParam) {
                    $this->addError($field, 'max', ['max' => $ruleParam]);
                }
                break;

            case 'numeric':
                if (!empty($value) && !is_numeric($value)) {
                    $this->addError($field, 'numeric');
                }
                break;

            case 'integer':
                if (!empty($value) && !filter_var($value, FILTER_VALIDATE_INT)) {
                    $this->addError($field, 'integer');
                }
                break;

            case 'date':
                if (!empty($value) && !$this->isValidDate($value)) {
                    $this->addError($field, 'date');
                }
                break;

            case 'unique':
                if (!empty($value) && !$this->isUnique($field, $value, $ruleParam)) {
                    $this->addError($field, 'unique');
                }
                break;

            case 'exists':
                if (!empty($value) && !$this->exists($field, $value, $ruleParam)) {
                    $this->addError($field, 'exists');
                }
                break;
        }
    }

    /**
     * Validate file upload
     */
    public function validateFile($field, $rules) {
        if (!isset($_FILES[$field])) {
            return true;
        }

        $file = $_FILES[$field];
        
        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
            return true; // No file uploaded
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->addError($field, 'file_error');
            return false;
        }

        $rulesArray = explode('|', $rules);

        foreach ($rulesArray as $rule) {
            $ruleParts = explode(':', $rule);
            $ruleName = $ruleParts[0];
            $ruleParam = $ruleParts[1] ?? null;

            switch ($ruleName) {
                case 'image':
                    if (!$this->isImageFile($file)) {
                        $this->addError($field, 'image');
                    }
                    break;

                case 'max_size':
                    $maxSize = $this->parseSize($ruleParam);
                    if ($file['size'] > $maxSize) {
                        $this->addError($field, 'max_size');
                    }
                    break;
            }
        }

        return !$this->hasErrors($field);
    }

    /**
     * Check if value is unique in database
     */
    private function isUnique($field, $value, $table) {
        global $db;
        if (!$db) return true;

        $query = "SELECT COUNT(*) FROM {$table} WHERE {$field} = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$value]);
        return $stmt->fetchColumn() == 0;
    }

    /**
     * Check if value exists in database
     */
    private function exists($field, $value, $table) {
        global $db;
        if (!$db) return true;

        $query = "SELECT COUNT(*) FROM {$table} WHERE {$field} = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$value]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Check if date is valid
     */
    private function isValidDate($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }

    /**
     * Check if file is an image
     */
    private function isImageFile($file) {
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        return in_array($file['type'], $allowedTypes);
    }

    /**
     * Parse size string to bytes
     */
    private function parseSize($size) {
        $units = ['B' => 1, 'KB' => 1024, 'MB' => 1048576, 'GB' => 1073741824];
        
        if (is_numeric($size)) {
            return (int)$size;
        }

        $matches = [];
        if (preg_match('/^(\d+)\s*([A-Z]{1,2})$/i', $size, $matches)) {
            $number = (int)$matches[1];
            $unit = strtoupper($matches[2]);
            return $number * ($units[$unit] ?? 1);
        }

        return 0;
    }

    /**
     * Add error to errors array
     */
    private function addError($field, $rule, $params = []) {
        $message = $this->messages[$rule] ?? $rule;
        $message = str_replace(':field', $field, $message);
        
        foreach ($params as $key => $value) {
            $message = str_replace(':' . $key, $value, $message);
        }

        $this->errors[$field][] = $message;
    }

    /**
     * Get all errors
     */
    public function getErrors() {
        return $this->errors;
    }

    /**
     * Get errors for specific field
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }

    /**
     * Check if there are any errors
     */
    public function hasErrors($field = null) {
        if ($field) {
            return isset($this->errors[$field]);
        }
        return !empty($this->errors);
    }

    /**
     * Get first error message
     */
    public function getFirstError() {
        foreach ($this->errors as $fieldErrors) {
            return $fieldErrors[0] ?? '';
        }
        return '';
    }

    /**
     * Sanitize input data
     */
    public static function sanitize($data) {
        if (is_array($data)) {
            return array_map([self::class, 'sanitize'], $data);
        }
        
        return trim(htmlspecialchars($data, ENT_QUOTES, 'UTF-8'));
    }
}