<?php

class StripeService {
    private $secretKey;
    
    public function __construct() {
        $this->secretKey = STRIPE_SECRET_KEY;
    }
    
    public function createPaymentIntent($amount, $currency = 'usd') {
        $url = 'https://api.stripe.com/v1/payment_intents';
        
        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'automatic_payment_methods' => ['enabled' => true]
        ];
        
        $headers = [
            'Authorization: Bearer ' . $this->secretKey,
            'Content-Type: application/x-www-form-urlencoded'
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200) {
            throw new Exception('Payment processing failed');
        }
        
        $paymentIntent = json_decode($response);
        
        // For MVP, simulate successful payment
        $paymentIntent->status = 'succeeded';
        
        return $paymentIntent;
    }
    
    public function retrievePaymentIntent($paymentIntentId) {
        // Mock implementation for MVP
        return (object) [
            'id' => $paymentIntentId,
            'status' => 'succeeded',
            'amount' => 0
        ];
    }
}
?>
