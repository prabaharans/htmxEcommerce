<?php

class CartController extends Controller {
    
    public function index() {
        $cart = new Cart();
        $items = $cart->getItems();
        $total = $cart->getTotal();
        
        $this->view->render('cart/index', [
            'items' => $items,
            'total' => $total,
            'cartCount' => $this->getCartItemCount()
        ]);
    }
    
    public function add() {
        $productId = $_POST['product_id'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 1);
        
        if (!$productId) {
            $this->json(['success' => false, 'message' => 'Product ID required']);
            return;
        }
        
        $productModel = new Product();
        $product = $productModel->getById($productId);
        
        if (!$product) {
            $this->json(['success' => false, 'message' => 'Product not found']);
            return;
        }
        
        $cart = new Cart();
        $cart->addItem($productId, $quantity);
        
        if ($this->isHtmxRequest()) {
            header('HX-Trigger: cartUpdated');
            echo '<div class="alert alert-success">Product added to cart!</div>';
        } else {
            $this->json(['success' => true, 'cartCount' => $this->getCartItemCount()]);
        }
    }
    
    public function update() {
        $productId = $_POST['product_id'] ?? '';
        $quantity = (int)($_POST['quantity'] ?? 0);
        
        $cart = new Cart();
        $cart->updateItem($productId, $quantity);
        
        $items = $cart->getItems();
        $total = $cart->getTotal();
        
        if ($this->isHtmxRequest()) {
            $this->view->render('cart/item', [
                'item' => $cart->getItem($productId),
                'total' => $total
            ]);
        } else {
            $this->json(['success' => true, 'total' => $total]);
        }
    }
    
    public function remove() {
        $productId = $_POST['product_id'] ?? '';
        
        $cart = new Cart();
        $cart->removeItem($productId);
        
        if ($this->isHtmxRequest()) {
            header('HX-Trigger: cartUpdated');
            echo '';
        } else {
            $this->json(['success' => true, 'cartCount' => $this->getCartItemCount()]);
        }
    }
}
?>
