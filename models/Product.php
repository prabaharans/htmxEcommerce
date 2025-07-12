<?php

class Product extends Model {
    
    public function __construct() {
        parent::__construct();
        
        // Initialize with default products if empty
        if (empty($this->storage->get('products', []))) {
            $defaultProducts = DatabaseConfig::getDefaultProducts();
            $this->storage->set('products', $defaultProducts);
        }
    }
    
    public function getAll() {
        return $this->storage->get('products', []);
    }
    
    public function getById($id) {
        return $this->find('products', $id);
    }
    
    public function getFeatured() {
        $products = $this->getAll();
        return array_filter($products, function($product) {
            return $product['featured'];
        });
    }
    
    public function getByCategory($category) {
        return $this->where('products', 'category', $category);
    }
    
    public function search($query) {
        $products = $this->getAll();
        return array_filter($products, function($product) use ($query) {
            return stripos($product['name'], $query) !== false ||
                   stripos($product['description'], $query) !== false;
        });
    }
    
    public function getCategories() {
        $products = $this->getAll();
        $categories = array_unique(array_column($products, 'category'));
        return array_values($categories);
    }
    
    public function create($data) {
        return $this->save('products', $data);
    }
    
    public function update($id, $data) {
        $data['id'] = $id;
        return $this->save('products', $data);
    }
    
    public function delete($collection, $id) {
        return parent::delete('products', $id);
    }
    
    public function getCount() {
        return count($this->getAll());
    }
}
?>
