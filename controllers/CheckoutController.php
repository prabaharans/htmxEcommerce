<?php

class CheckoutController extends Controller {
    
    public function index() {
        $cart = new Cart();
        $items = $cart->getItems();
        $total = $cart->getTotal();
        
        if (empty($items)) {
            $this->redirect('/cart');
            return;
        }
        
        $this->view->render('checkout/index', [
            'items' => $items,
            'total' => $total,
            'stripeKey' => STRIPE_PUBLISHABLE_KEY,
            'cartCount' => $this->getCartItemCount()
        ]);
    }
    
    public function process() {
        $cart = new Cart();
        $items = $cart->getItems();
        $total = $cart->getTotal();
        
        if (empty($items)) {
            $this->json(['success' => false, 'message' => 'Cart is empty']);
            return;
        }
        
        // Get form data
        $customerData = [
            'name' => sanitizeInput($_POST['name'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'address' => sanitizeInput($_POST['address'] ?? ''),
            'city' => sanitizeInput($_POST['city'] ?? ''),
            'postal_code' => sanitizeInput($_POST['postal_code'] ?? ''),
            'country' => sanitizeInput($_POST['country'] ?? '')
        ];
        
        // Validate required fields
        $required = ['name', 'email', 'address', 'city', 'postal_code', 'country'];
        foreach ($required as $field) {
            if (empty($customerData[$field])) {
                $this->json(['success' => false, 'message' => 'All fields are required']);
                return;
            }
        }
        
        try {
            // Process payment with Stripe
            $stripeService = new StripeService();
            $paymentIntent = $stripeService->createPaymentIntent($total * 100, CURRENCY); // Convert to cents
            
            if ($paymentIntent->status === 'succeeded') {
                // Create order
                $orderModel = new Order();
                $orderId = $orderModel->create([
                    'customer' => $customerData,
                    'items' => $items,
                    'total' => $total,
                    'payment_id' => $paymentIntent->id,
                    'status' => 'completed'
                ]);
                
                // Clear cart
                $cart->clear();
                
                // Generate invoice
                $pdfService = new PDFService();
                $pdfService->generateInvoice($orderId);
                
                if ($this->isHtmxRequest()) {
                    header('HX-Redirect: /orders/' . $orderId . '/invoice');
                } else {
                    $this->json(['success' => true, 'order_id' => $orderId]);
                }
            } else {
                $this->json(['success' => false, 'message' => 'Payment failed']);
            }
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Payment processing error: ' . $e->getMessage()]);
        }
    }
}
?>
