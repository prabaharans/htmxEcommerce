<?php

class Controller {
    protected $view;
    protected $storage;
    
    public function __construct() {
        $this->view = new View();
        $this->storage = InMemoryStorage::getInstance();
    }
    
    protected function isHtmxRequest() {
        return isset($_SERVER['HTTP_HX_REQUEST']);
    }
    
    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
    
    protected function redirect($url) {
        if ($this->isHtmxRequest()) {
            header('HX-Redirect: ' . $url);
        } else {
            header('Location: ' . $url);
        }
        exit;
    }
    
    protected function getCartItemCount() {
        $cart = $this->storage->get('cart_' . session_id(), []);
        return array_sum(array_column($cart, 'quantity'));
    }
}
?>
