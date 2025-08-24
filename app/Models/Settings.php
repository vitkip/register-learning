<?php

require_once 'app/Core/Model.php';

/**
 * Settings Model
 */
class SettingsModel extends Model {
    protected $table = 'settings';
    protected $fillable = ['key_name', 'key_value', 'description'];

    private $cache = [];

    /**
     * Get setting value by key
     */
    public function get($key, $default = null) {
        if (isset($this->cache[$key])) {
            return $this->cache[$key];
        }

        $result = $this->first('key_name = ?', [$key]);
        $value = $result ? $result['key_value'] : $default;
        
        $this->cache[$key] = $value;
        return $value;
    }

    /**
     * Set setting value
     */
    public function set($key, $value, $description = null) {
        $existing = $this->first('key_name = ?', [$key]);

        if ($existing) {
            $updateData = ['key_value' => $value];
            if ($description !== null) {
                $updateData['description'] = $description;
            }
            $result = $this->update($existing['id'], $updateData);
        } else {
            $result = $this->create([
                'key_name' => $key,
                'key_value' => $value,
                'description' => $description
            ]);
        }

        // Update cache
        if ($result) {
            $this->cache[$key] = $value;
        }

        return $result;
    }

    /**
     * Get all settings as key-value pairs
     */
    public function getAll() {
        if (empty($this->cache)) {
            $settings = $this->findAll('key_name');
            foreach ($settings as $setting) {
                $this->cache[$setting['key_name']] = $setting['key_value'];
            }
        }
        return $this->cache;
    }

    /**
     * Get settings for display (with descriptions)
     */
    public function getAllWithDescriptions() {
        return $this->findAll('key_name');
    }

    /**
     * Delete setting
     */
    public function deleteSetting($key) {
        $setting = $this->first('key_name = ?', [$key]);
        if ($setting) {
            unset($this->cache[$key]);
            return $this->delete($setting['id']);
        }
        return false;
    }
}