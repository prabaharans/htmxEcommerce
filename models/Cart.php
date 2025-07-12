<?php

class Cart extends Model {
    private $sessionId;
    
    public function __construct() {
        parent::__construct();
        $this->sessionId = session_id();
    }
    
    public function addItem($productId, $quantity = 1) {
        $cart = $this->getCart();
        
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $productModel = new Product();
            $product = $productModel->getById($productId);
            
            if ($product) {
                $cart[$productId] = [
                    'product_id' => $productId,
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $product['price']
                ];
            }
        }
        
        $this->saveCart($cart);
    }
    
    public function updateItem($productId, $quantity) {
        $cart = $this->getCart();
        
        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            if (isset($cart[$productId])) {
                $cart[$productId]['quantity'] = $quantity;
            }
        }
        
        $this->saveCart($cart);
    }
    
    public function removeItem($productId) {
        $cart = $this->getCart();
        unset($cart[$productId]);
        $this->saveCart($cart);
    }
    
    public function getItems() {
        return array_values($this->getCart());
    }
    
    public function getItem($productId) {
        $cart = $this->getCart();
        return $cart[$productId] ?? null;
    }
    
    public function getTotal() {
        $cart = $this->getCart();
        $total = 0;
        
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }
        
        return $total;
    }
    
    public function getItemCount() {
        $cart = $this->getCart();
        return array_sum(array_column($cart, 'quantity'));
    }
    
    public function clear() {
        $this->storage->delete('cart_' . $this->sessionId);
    }
    
    private function getCart() {
        return $this->storage->get('cart_' . $this->sessionId, []);
    }
    
    private function saveCart($cart) {
        $this->storage->set('cart_' . $this->sessionId, $cart);
    }
}
?>
