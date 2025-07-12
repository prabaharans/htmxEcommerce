<?php

class AdminController extends Controller {
    
    public function __construct() {
        parent::__construct();
        
        // Simple admin authentication
        if (!isAdmin()) {
            $_SESSION['admin'] = true; // Auto-login for MVP
        }
    }
    
    public function dashboard() {
        $orderModel = new Order();
        $productModel = new Product();
        
        $stats = [
            'total_orders' => $orderModel->getCount(),
            'total_revenue' => $orderModel->getTotalRevenue(),
            'total_products' => $productModel->getCount(),
            'recent_orders' => $orderModel->getRecent(5)
        ];
        
        $this->view->render('admin/dashboard', $stats, 'admin');
    }
    
    public function products() {
        $productModel = new Product();
        $products = $productModel->getAll();
        
        $this->view->render('admin/products', [
            'products' => $products
        ], 'admin');
    }
    
    public function orders() {
        $orderModel = new Order();
        $orders = $orderModel->getAll();
        
        $this->view->render('admin/orders', [
            'orders' => $orders
        ], 'admin');
    }
    
    public function createProduct() {
        $data = [
            'name' => sanitizeInput($_POST['name'] ?? ''),
            'description' => sanitizeInput($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cost' => (float)($_POST['cost'] ?? 0),
            'category' => sanitizeInput($_POST['category'] ?? ''),
            'image' => sanitizeInput($_POST['image'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'featured' => isset($_POST['featured']),
            'meta_title' => sanitizeInput($_POST['meta_title'] ?? ''),
            'meta_description' => sanitizeInput($_POST['meta_description'] ?? ''),
            'slug' => generateSlug($_POST['name'] ?? ''),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $productModel = new Product();
        $product = $productModel->create($data);
        
        if ($this->isHtmxRequest()) {
            $this->view->render('admin/product-form', ['product' => $product]);
        } else {
            $this->redirect('/admin/products');
        }
    }
    
    public function updateProduct() {
        $id = $_POST['id'] ?? '';
        $data = [
            'id' => $id,
            'name' => sanitizeInput($_POST['name'] ?? ''),
            'description' => sanitizeInput($_POST['description'] ?? ''),
            'price' => (float)($_POST['price'] ?? 0),
            'cost' => (float)($_POST['cost'] ?? 0),
            'category' => sanitizeInput($_POST['category'] ?? ''),
            'image' => sanitizeInput($_POST['image'] ?? ''),
            'stock' => (int)($_POST['stock'] ?? 0),
            'featured' => isset($_POST['featured']),
            'meta_title' => sanitizeInput($_POST['meta_title'] ?? ''),
            'meta_description' => sanitizeInput($_POST['meta_description'] ?? ''),
            'slug' => generateSlug($_POST['name'] ?? ''),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $productModel = new Product();
        $productModel->update($id, $data);
        
        if ($this->isHtmxRequest()) {
            echo '<div class="alert alert-success">Product updated successfully!</div>';
        } else {
            $this->redirect('/admin/products');
        }
    }
    
    public function deleteProduct() {
        $id = $_POST['id'] ?? '';
        
        $productModel = new Product();
        $productModel->delete($id);
        
        if ($this->isHtmxRequest()) {
            echo '';
        } else {
            $this->redirect('/admin/products');
        }
    }
}
?>
