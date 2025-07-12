<?php

class Cart extends Model {
    private $sessionId;
    
    public function __construct() {
        parent::__construct();
        $this->sessionId = session_id() ?: 'guest_' . uniqid();
    }
    
    public function addItem($productId, $quantity = 1) {
        $productModel = new Product();
        $product = $productModel->getById($productId);
        
        if (!$product) {
            throw new Exception('Product not found');
        }
        
        // Check if item already exists in cart
        $existingItem = $this->getItem($productId);
        
        if ($existingItem) {
            // Update quantity
            $newQuantity = $existingItem['quantity'] + $quantity;
            $this->updateItem($productId, $newQuantity);
        } else {
            // Add new item
            $cartItem = [
                'session_id' => $this->sessionId,
                'product_id' => $productId,
                'quantity' => $quantity
            ];
            
            $this->insert('cart_sessions', $cartItem);
        }
    }
    
    public function updateItem($productId, $quantity) {
        if ($quantity <= 0) {
            $this->removeItem($productId);
            return;
        }
        
        $stmt = $this->db->prepare("
            UPDATE cart_sessions 
            SET quantity = ?, updated_at = ? 
            WHERE session_id = ? AND product_id = ?
        ");
        $stmt->execute([$quantity, date('Y-m-d H:i:s'), $this->sessionId, $productId]);
    }
    
    public function removeItem($productId) {
        $stmt = $this->db->prepare("
            DELETE FROM cart_sessions 
            WHERE session_id = ? AND product_id = ?
        ");
        $stmt->execute([$this->sessionId, $productId]);
    }
    
    public function getItems() {
        $stmt = $this->db->prepare("
            SELECT 
                cs.*,
                p.name as product_name,
                p.price as product_price,
                p.image as product_image,
                p.stock as product_stock,
                p.slug as product_slug
            FROM cart_sessions cs
            JOIN products p ON cs.product_id = p.id
            WHERE cs.session_id = ?
            ORDER BY cs.created_at DESC
        ");
        $stmt->execute([$this->sessionId]);
        $items = $stmt->fetchAll();
        
        // Format items for display
        $formattedItems = [];
        foreach ($items as $item) {
            $formattedItems[] = [
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['product_price'],
                'product' => [
                    'id' => $item['product_id'],
                    'name' => $item['product_name'],
                    'price' => $item['product_price'],
                    'image' => $item['product_image'],
                    'stock' => $item['product_stock'],
                    'slug' => $item['product_slug']
                ]
            ];
        }
        
        return $formattedItems;
    }
    
    public function getItem($productId) {
        $stmt = $this->db->prepare("
            SELECT 
                cs.*,
                p.name as product_name,
                p.price as product_price,
                p.image as product_image,
                p.stock as product_stock
            FROM cart_sessions cs
            JOIN products p ON cs.product_id = p.id
            WHERE cs.session_id = ? AND cs.product_id = ?
        ");
        $stmt->execute([$this->sessionId, $productId]);
        $item = $stmt->fetch();
        
        if (!$item) {
            return null;
        }
        
        return [
            'product_id' => $item['product_id'],
            'quantity' => $item['quantity'],
            'price' => $item['product_price'],
            'product' => [
                'id' => $item['product_id'],
                'name' => $item['product_name'],
                'price' => $item['product_price'],
                'image' => $item['product_image'],
                'stock' => $item['product_stock']
            ]
        ];
    }
    
    public function getTotal() {
        $stmt = $this->db->prepare("
            SELECT SUM(cs.quantity * p.price) as total
            FROM cart_sessions cs
            JOIN products p ON cs.product_id = p.id
            WHERE cs.session_id = ?
        ");
        $stmt->execute([$this->sessionId]);
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    public function getItemCount() {
        $stmt = $this->db->prepare("
            SELECT SUM(quantity) as count
            FROM cart_sessions 
            WHERE session_id = ?
        ");
        $stmt->execute([$this->sessionId]);
        $result = $stmt->fetch();
        return $result['count'] ?? 0;
    }
    
    public function clear() {
        $stmt = $this->db->prepare("DELETE FROM cart_sessions WHERE session_id = ?");
        $stmt->execute([$this->sessionId]);
    }
    
    public function isEmpty() {
        return $this->getItemCount() == 0;
    }
    
    public function validateStock() {
        $items = $this->getItems();
        $issues = [];
        
        foreach ($items as $item) {
            if ($item['quantity'] > $item['product']['stock']) {
                $issues[] = [
                    'product_id' => $item['product_id'],
                    'product_name' => $item['product']['name'],
                    'requested' => $item['quantity'],
                    'available' => $item['product']['stock']
                ];
            }
        }
        
        return $issues;
    }
    
    public function getSubtotal() {
        return $this->getTotal();
    }
    
    public function getTax($rate = 0) {
        return $this->getSubtotal() * ($rate / 100);
    }
    
    public function getShipping() {
        // Free shipping for orders over $50
        $subtotal = $this->getSubtotal();
        return $subtotal >= 50 ? 0 : 5.99;
    }
    
    public function getFinalTotal($taxRate = 0) {
        $subtotal = $this->getSubtotal();
        $tax = $this->getTax($taxRate);
        $shipping = $this->getShipping();
        
        return $subtotal + $tax + $shipping;
    }
    
    public function transferToOrder() {
        // Get all cart items for order creation
        $items = $this->getItems();
        
        // Validate stock before transfer
        $stockIssues = $this->validateStock();
        if (!empty($stockIssues)) {
            throw new Exception('Insufficient stock for some items');
        }
        
        return $items;
    }
    
    public function mergeCarts($otherSessionId) {
        // Merge items from another session (e.g., when user logs in)
        $stmt = $this->db->prepare("
            SELECT product_id, quantity 
            FROM cart_sessions 
            WHERE session_id = ?
        ");
        $stmt->execute([$otherSessionId]);
        $otherItems = $stmt->fetchAll();
        
        foreach ($otherItems as $item) {
            $this->addItem($item['product_id'], $item['quantity']);
        }
        
        // Clear the other cart
        $clearStmt = $this->db->prepare("DELETE FROM cart_sessions WHERE session_id = ?");
        $clearStmt->execute([$otherSessionId]);
    }
    
    public function cleanupOldCarts($daysOld = 7) {
        // Static method to clean up old abandoned carts
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        $stmt = $this->db->prepare("DELETE FROM cart_sessions WHERE updated_at < ?");
        return $stmt->execute([$cutoffDate]);
    }
    
    public function setSessionId($sessionId) {
        $this->sessionId = $sessionId;
    }
    
    public function getSessionId() {
        return $this->sessionId;
    }
}
?>