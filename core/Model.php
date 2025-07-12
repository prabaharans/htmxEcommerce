<?php

class Model {
    protected $storage;
    
    public function __construct() {
        $this->storage = InMemoryStorage::getInstance();
    }
    
    protected function generateId() {
        return uniqid();
    }
    
    protected function find($collection, $id) {
        $items = $this->storage->get($collection, []);
        foreach ($items as $item) {
            if ($item['id'] == $id) {
                return $item;
            }
        }
        return null;
    }
    
    protected function where($collection, $field, $value) {
        $items = $this->storage->get($collection, []);
        return array_filter($items, function($item) use ($field, $value) {
            return isset($item[$field]) && $item[$field] === $value;
        });
    }
    
    protected function save($collection, $item) {
        $items = $this->storage->get($collection, []);
        
        if (isset($item['id'])) {
            // Update existing
            foreach ($items as $index => $existingItem) {
                if ($existingItem['id'] == $item['id']) {
                    $items[$index] = $item;
                    break;
                }
            }
        } else {
            // Create new
            $item['id'] = $this->generateId();
            $items[] = $item;
        }
        
        $this->storage->set($collection, $items);
        return $item;
    }
    
    protected function delete($collection, $id) {
        $items = $this->storage->get($collection, []);
        $items = array_filter($items, function($item) use ($id) {
            return $item['id'] != $id;
        });
        $this->storage->set($collection, array_values($items));
    }
}
?>
