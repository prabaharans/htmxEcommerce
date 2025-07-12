<?php

class ProductController extends Controller {
    
    public function index() {
        $productModel = new Product();
        $category = $_GET['category'] ?? '';
        $search = $_GET['search'] ?? '';
        
        if ($category) {
            $products = $productModel->getByCategory($category);
        } elseif ($search) {
            $products = $productModel->search($search);
        } else {
            $products = $productModel->getAll();
        }
        
        $categories = $productModel->getCategories();
        
        if ($this->isHtmxRequest() && isset($_GET['search'])) {
            $this->view->render('products/search', [
                'products' => $products
            ]);
        } else {
            $this->view->render('products/index', [
                'products' => $products,
                'categories' => $categories,
                'currentCategory' => $category,
                'searchTerm' => $search,
                'cartCount' => $this->getCartItemCount()
            ]);
        }
    }
    
    public function show($id) {
        $productModel = new Product();
        $product = $productModel->getById($id);
        
        if (!$product) {
            http_response_code(404);
            echo "Product not found";
            return;
        }
        
        $this->view->render('products/show', [
            'product' => $product,
            'cartCount' => $this->getCartItemCount()
        ]);
    }
    
    public function search() {
        $query = $_GET['q'] ?? '';
        $productModel = new Product();
        $products = $productModel->search($query);
        
        $this->view->render('products/search', [
            'products' => $products
        ]);
    }
}
?>
