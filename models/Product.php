<?php

class Product extends Model {
    
    public function __construct() {
        parent::__construct();
    }
    
    public function getAll() {
        return $this->all('products');
    }
    
    public function getById($id) {
        return $this->find('products', $id);
    }
    
    public function getFeatured() {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE featured = TRUE ORDER BY id DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    public function getByCategory($category) {
        return $this->where('products', 'category', $category);
    }
    
    public function searchProducts($query) {
        return $this->search('products', ['name', 'description'], $query);
    }
    
    public function getCategories() {
        $stmt = $this->db->prepare("SELECT DISTINCT category FROM products WHERE category IS NOT NULL ORDER BY category");
        $stmt->execute();
        return array_column($stmt->fetchAll(), 'category');
    }
    
    public function create($data) {
        // Generate slug from name if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        return $this->insert('products', $data);
    }
    
    public function updateProduct($id, $data) {
        // Generate slug from name if not provided
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = $this->generateSlug($data['name']);
        }
        
        return $this->update('products', $id, $data);
    }
    
    public function deleteProduct($id) {
        return $this->delete('products', $id);
    }
    
    public function getCount() {
        return $this->count('products');
    }
    
    public function updateStock($id, $quantity) {
        $stmt = $this->db->prepare("UPDATE products SET stock = stock - ?, updated_at = ? WHERE id = ?");
        return $stmt->execute([$quantity, date('Y-m-d H:i:s'), $id]);
    }
    
    public function getBySlug($slug) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    public function getRecent($limit = 10) {
        return $this->recent('products', $limit);
    }
    
    public function getLowStock($threshold = 10) {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE stock <= ? ORDER BY stock ASC");
        $stmt->execute([$threshold]);
        return $stmt->fetchAll();
    }
    
    public function getPaginated($page = 1, $perPage = 12, $category = null, $featured = null) {
        $conditions = [];
        $params = [];
        
        if ($category) {
            $conditions[] = 'category = ?';
            $params[] = $category;
        }
        
        if ($featured !== null) {
            $conditions[] = 'featured = ?';
            $params[] = $featured;
        }
        
        return $this->paginate('products', $page, $perPage, $conditions, $params);
    }
    
    public function toggleFeatured($id) {
        $stmt = $this->db->prepare("UPDATE products SET featured = NOT featured, updated_at = ? WHERE id = ? RETURNING featured");
        $stmt->execute([date('Y-m-d H:i:s'), $id]);
        $result = $stmt->fetch();
        return $result['featured'] ?? false;
    }
    
    private function generateSlug($name) {
        // Convert to lowercase and replace spaces with hyphens
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9\-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->getBySlug($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    public function getRelated($id, $category, $limit = 4) {
        $stmt = $this->db->prepare("
            SELECT * FROM products 
            WHERE category = ? AND id != ? 
            ORDER BY RANDOM() 
            LIMIT ?
        ");
        $stmt->execute([$category, $id, $limit]);
        return $stmt->fetchAll();
    }
    
    public function incrementView($id) {
        // Add view tracking if needed in the future
        // For now, this is a placeholder
        return true;
    }
}
?>