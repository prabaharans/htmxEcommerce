<?php

class InMemoryStorage {
    private static $instance = null;
    private $data = [];
    private $filePath;
    
    private function __construct() {
        $this->filePath = sys_get_temp_dir() . '/dropship_pro_storage.json';
        $this->loadFromFile();
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Set a value in storage
     */
    public function set($key, $value) {
        $this->data[$key] = $value;
        $this->saveToFile();
    }
    
    /**
     * Get a value from storage
     */
    public function get($key, $default = null) {
        return $this->data[$key] ?? $default;
    }
    
    /**
     * Check if key exists in storage
     */
    public function has($key) {
        return isset($this->data[$key]);
    }
    
    /**
     * Delete a key from storage
     */
    public function delete($key) {
        unset($this->data[$key]);
        $this->saveToFile();
    }
    
    /**
     * Clear all data from storage
     */
    public function clear() {
        $this->data = [];
        $this->saveToFile();
    }
    
    /**
     * Get all data
     */
    public function all() {
        return $this->data;
    }
    
    /**
     * Get keys matching a pattern
     */
    public function keys($pattern = null) {
        $keys = array_keys($this->data);
        
        if ($pattern === null) {
            return $keys;
        }
        
        return array_filter($keys, function($key) use ($pattern) {
            return fnmatch($pattern, $key);
        });
    }
    
    /**
     * Increment a numeric value
     */
    public function increment($key, $value = 1) {
        $current = $this->get($key, 0);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }
    
    /**
     * Decrement a numeric value
     */
    public function decrement($key, $value = 1) {
        return $this->increment($key, -$value);
    }
    
    /**
     * Push value to array
     */
    public function push($key, $value) {
        $array = $this->get($key, []);
        if (!is_array($array)) {
            $array = [];
        }
        $array[] = $value;
        $this->set($key, $array);
        return count($array);
    }
    
    /**
     * Pop value from array
     */
    public function pop($key) {
        $array = $this->get($key, []);
        if (!is_array($array) || empty($array)) {
            return null;
        }
        $value = array_pop($array);
        $this->set($key, $array);
        return $value;
    }
    
    /**
     * Get size of array or string
     */
    public function size($key) {
        $value = $this->get($key);
        if (is_array($value)) {
            return count($value);
        }
        if (is_string($value)) {
            return strlen($value);
        }
        return 0;
    }
    
    /**
     * Set expiration for a key (basic TTL)
     */
    public function expire($key, $seconds) {
        $expireKey = $key . '_expires_at';
        $expireTime = time() + $seconds;
        $this->set($expireKey, $expireTime);
    }
    
    /**
     * Check if key has expired
     */
    public function isExpired($key) {
        $expireKey = $key . '_expires_at';
        $expireTime = $this->get($expireKey);
        
        if ($expireTime === null) {
            return false;
        }
        
        if (time() > $expireTime) {
            $this->delete($key);
            $this->delete($expireKey);
            return true;
        }
        
        return false;
    }
    
    /**
     * Get value only if not expired
     */
    public function getValid($key, $default = null) {
        if ($this->isExpired($key)) {
            return $default;
        }
        return $this->get($key, $default);
    }
    
    /**
     * Save data to file for persistence
     */
    private function saveToFile() {
        try {
            $jsonData = json_encode($this->data, JSON_PRETTY_PRINT);
            file_put_contents($this->filePath, $jsonData, LOCK_EX);
        } catch (Exception $e) {
            error_log('Failed to save storage data: ' . $e->getMessage());
        }
    }
    
    /**
     * Load data from file
     */
    private function loadFromFile() {
        try {
            if (file_exists($this->filePath)) {
                $jsonData = file_get_contents($this->filePath);
                $data = json_decode($jsonData, true);
                if (is_array($data)) {
                    $this->data = $data;
                }
            }
        } catch (Exception $e) {
            error_log('Failed to load storage data: ' . $e->getMessage());
            $this->data = [];
        }
    }
    
    /**
     * Get storage statistics
     */
    public function getStats() {
        return [
            'total_keys' => count($this->data),
            'memory_usage' => memory_get_usage(true),
            'file_size' => file_exists($this->filePath) ? filesize($this->filePath) : 0,
            'last_save' => file_exists($this->filePath) ? filemtime($this->filePath) : null
        ];
    }
    
    /**
     * Export data as JSON
     */
    public function export() {
        return json_encode($this->data, JSON_PRETTY_PRINT);
    }
    
    /**
     * Import data from JSON
     */
    public function import($jsonData) {
        try {
            $data = json_decode($jsonData, true);
            if (is_array($data)) {
                $this->data = array_merge($this->data, $data);
                $this->saveToFile();
                return true;
            }
        } catch (Exception $e) {
            error_log('Failed to import data: ' . $e->getMessage());
        }
        return false;
    }
    
    /**
     * Cleanup expired keys
     */
    public function cleanup() {
        $cleaned = 0;
        $expireKeys = array_filter(array_keys($this->data), function($key) {
            return strpos($key, '_expires_at') !== false;
        });
        
        foreach ($expireKeys as $expireKey) {
            $baseKey = str_replace('_expires_at', '', $expireKey);
            if ($this->isExpired($baseKey)) {
                $cleaned++;
            }
        }
        
        return $cleaned;
    }
    
    public function __destruct() {
        $this->saveToFile();
    }
}
?>
