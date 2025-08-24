<?php

/**
 * View class for handling views and templates
 */
class View {
    private $data = [];
    private $layout = 'main';

    /**
     * Set data for the view
     */
    public function set($key, $value = null) {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Set the layout
     */
    public function setLayout($layout) {
        $this->layout = $layout;
    }

    /**
     * Render a view
     */
    public function render($view, $data = []) {
        // Merge data
        $this->data = array_merge($this->data, $data);
        
        // Extract data to variables
        extract($this->data);
        
        // Start output buffering
        ob_start();
        
        // Include the view file
        $viewPath = "app/Views/{$view}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new Exception("View file not found: {$viewPath}");
        }
        
        // Get the content
        $content = ob_get_clean();
        
        // If layout is disabled, return content directly
        if ($this->layout === false) {
            return $content;
        }
        
        // Load the layout
        $layoutPath = "app/Views/layouts/{$this->layout}.php";
        if (file_exists($layoutPath)) {
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    /**
     * Render view without layout
     */
    public function renderPartial($view, $data = []) {
        // Merge data
        $this->data = array_merge($this->data, $data);
        
        // Extract data to variables
        extract($this->data);
        
        // Include the view file
        $viewPath = "app/Views/{$view}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        } else {
            throw new Exception("View file not found: {$viewPath}");
        }
    }

    /**
     * Include a partial view
     */
    public function partial($view, $data = []) {
        $originalData = $this->data;
        $this->data = array_merge($this->data, $data);
        
        extract($this->data);
        
        $viewPath = "app/Views/{$view}.php";
        if (file_exists($viewPath)) {
            require $viewPath;
        }
        
        $this->data = $originalData;
    }

    /**
     * Escape HTML output
     */
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Generate URL
     */
    public function url($path = '') {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }

    /**
     * Generate asset URL
     */
    public function asset($path) {
        return $this->url('public/assets/' . ltrim($path, '/'));
    }
}