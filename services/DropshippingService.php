<?php

class DropshippingService {
    private $apiUrl;
    private $apiKey;
    
    public function __construct() {
        $this->apiUrl = $_ENV['DROPSHIPPING_API_URL'] ?? 'https://api.mockdropshipping.com/v1';
        $this->apiKey = $_ENV['DROPSHIPPING_API_KEY'] ?? 'mock_api_key_123';
    }
    
    /**
     * Get products from dropshipping supplier
     */
    public function getProducts($category = null, $limit = 50) {
        try {
            $url = $this->apiUrl . '/products';
            $params = [
                'limit' => $limit,
                'api_key' => $this->apiKey
            ];
            
            if ($category) {
                $params['category'] = $category;
            }
            
            $response = $this->makeRequest($url, $params);
            
            if (!$response) {
                return $this->getMockProducts($category, $limit);
            }
            
            return $this->formatProducts($response['products'] ?? []);
            
        } catch (Exception $e) {
            error_log('Dropshipping API Error: ' . $e->getMessage());
            return $this->getMockProducts($category, $limit);
        }
    }
    
    /**
     * Get single product details
     */
    public function getProduct($supplierProductId) {
        try {
            $url = $this->apiUrl . '/products/' . $supplierProductId;
            $params = ['api_key' => $this->apiKey];
            
            $response = $this->makeRequest($url, $params);
            
            if (!$response) {
                return null;
            }
            
            return $this->formatProduct($response);
            
        } catch (Exception $e) {
            error_log('Dropshipping API Error: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Create order with supplier
     */
    public function createOrder($orderData) {
        try {
            $url = $this->apiUrl . '/orders';
            $data = [
                'api_key' => $this->apiKey,
                'customer' => [
                    'name' => $orderData['customer']['name'],
                    'email' => $orderData['customer']['email'],
                    'address' => [
                        'street' => $orderData['customer']['address'],
                        'city' => $orderData['customer']['city'],
                        'postal_code' => $orderData['customer']['postal_code'],
                        'country' => $orderData['customer']['country']
                    ]
                ],
                'items' => array_map(function($item) {
                    return [
                        'supplier_product_id' => $item['supplier_product_id'] ?? $item['product_id'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price']
                    ];
                }, $orderData['items']),
                'shipping_method' => 'standard',
                'notes' => 'Order from ' . APP_NAME
            ];
            
            $response = $this->makeRequest($url, [], 'POST', $data);
            
            if (!$response) {
                // Mock successful order creation for MVP
                return [
                    'supplier_order_id' => 'SUP_' . uniqid(),
                    'status' => 'processing',
                    'tracking_number' => null,
                    'estimated_delivery' => date('Y-m-d', strtotime('+7 days'))
                ];
            }
            
            return [
                'supplier_order_id' => $response['order_id'],
                'status' => $response['status'],
                'tracking_number' => $response['tracking_number'] ?? null,
                'estimated_delivery' => $response['estimated_delivery'] ?? null
            ];
            
        } catch (Exception $e) {
            error_log('Dropshipping Order Error: ' . $e->getMessage());
            throw new Exception('Failed to create supplier order: ' . $e->getMessage());
        }
    }
    
    /**
     * Get order status from supplier
     */
    public function getOrderStatus($supplierOrderId) {
        try {
            $url = $this->apiUrl . '/orders/' . $supplierOrderId;
            $params = ['api_key' => $this->apiKey];
            
            $response = $this->makeRequest($url, $params);
            
            if (!$response) {
                return [
                    'status' => 'processing',
                    'tracking_number' => null,
                    'estimated_delivery' => date('Y-m-d', strtotime('+5 days'))
                ];
            }
            
            return [
                'status' => $response['status'],
                'tracking_number' => $response['tracking_number'] ?? null,
                'estimated_delivery' => $response['estimated_delivery'] ?? null,
                'shipped_date' => $response['shipped_date'] ?? null
            ];
            
        } catch (Exception $e) {
            error_log('Dropshipping Status Error: ' . $e->getMessage());
            return ['status' => 'unknown'];
        }
    }
    
    /**
     * Get available categories from supplier
     */
    public function getCategories() {
        try {
            $url = $this->apiUrl . '/categories';
            $params = ['api_key' => $this->apiKey];
            
            $response = $this->makeRequest($url, $params);
            
            if (!$response) {
                return ['Electronics', 'Wearables', 'Accessories', 'Home & Garden', 'Sports & Fitness'];
            }
            
            return array_column($response['categories'] ?? [], 'name');
            
        } catch (Exception $e) {
            error_log('Dropshipping Categories Error: ' . $e->getMessage());
            return ['Electronics', 'Wearables', 'Accessories', 'Home & Garden', 'Sports & Fitness'];
        }
    }
    
    /**
     * Make HTTP request to dropshipping API
     */
    private function makeRequest($url, $params = [], $method = 'GET', $data = null) {
        $ch = curl_init();
        
        if ($method === 'GET' && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: ' . APP_NAME . '/1.0'
            ]
        ]);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            }
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($response === false || $httpCode >= 400) {
            return false;
        }
        
        return json_decode($response, true);
    }
    
    /**
     * Format products for internal use
     */
    private function formatProducts($products) {
        return array_map([$this, 'formatProduct'], $products);
    }
    
    /**
     * Format single product for internal use
     */
    private function formatProduct($product) {
        return [
            'supplier_product_id' => $product['id'],
            'name' => $product['name'],
            'description' => $product['description'],
            'price' => $product['retail_price'],
            'cost' => $product['wholesale_price'],
            'category' => $product['category'],
            'image' => $product['images'][0] ?? 'https://via.placeholder.com/500',
            'images' => $product['images'] ?? [],
            'stock' => $product['stock_quantity'] ?? 0,
            'weight' => $product['weight'] ?? 0,
            'dimensions' => $product['dimensions'] ?? null,
            'sku' => $product['sku'] ?? null,
            'brand' => $product['brand'] ?? null
        ];
    }
    
    /**
     * Mock products for MVP when API is unavailable
     */
    private function getMockProducts($category = null, $limit = 50) {
        $mockProducts = [
            [
                'supplier_product_id' => 'MOCK_001',
                'name' => 'Wireless Bluetooth Earbuds Pro',
                'description' => 'Premium wireless earbuds with active noise cancellation and 24-hour battery life.',
                'price' => 79.99,
                'cost' => 35.00,
                'category' => 'Electronics',
                'image' => 'https://images.unsplash.com/photo-1572569511254-d8f925fe2cbb?w=500',
                'stock' => 150,
                'brand' => 'TechPro'
            ],
            [
                'supplier_product_id' => 'MOCK_002',
                'name' => 'Smart LED Light Strip',
                'description' => 'WiFi-enabled RGB LED strip with voice control and app integration.',
                'price' => 24.99,
                'cost' => 12.00,
                'category' => 'Home & Garden',
                'image' => 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=500',
                'stock' => 200,
                'brand' => 'SmartHome'
            ],
            [
                'supplier_product_id' => 'MOCK_003',
                'name' => 'Fitness Resistance Bands Set',
                'description' => 'Complete set of resistance bands for home workouts with multiple resistance levels.',
                'price' => 19.99,
                'cost' => 8.50,
                'category' => 'Sports & Fitness',
                'image' => 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?w=500',
                'stock' => 300,
                'brand' => 'FitLife'
            ]
        ];
        
        if ($category) {
            $mockProducts = array_filter($mockProducts, function($product) use ($category) {
                return $product['category'] === $category;
            });
        }
        
        return array_slice($mockProducts, 0, $limit);
    }
}
?>
