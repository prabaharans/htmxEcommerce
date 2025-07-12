<?php

class HomeController extends Controller {
    
    public function index() {
        $productModel = new Product();
        $products = $productModel->getFeatured();
        
        $this->view->render('home/index', [
            'products' => $products,
            'cartCount' => $this->getCartItemCount()
        ]);
    }
}
?>
