<?php

// In-memory storage configuration
class DatabaseConfig {
    public static function getDefaultProducts() {
        return [
            [
                'id' => 1,
                'name' => 'Wireless Bluetooth Headphones',
                'slug' => 'wireless-bluetooth-headphones',
                'description' => 'Premium wireless headphones with noise cancellation and 30-hour battery life.',
                'price' => 99.99,
                'cost' => 45.00,
                'category' => 'Electronics',
                'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500',
                'stock' => 50,
                'featured' => true,
                'meta_title' => 'Premium Wireless Bluetooth Headphones | DropShip Pro',
                'meta_description' => 'High-quality wireless headphones with noise cancellation. Free shipping worldwide.',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'name' => 'Smart Fitness Watch',
                'slug' => 'smart-fitness-watch',
                'description' => 'Advanced fitness tracking with heart rate monitor, GPS, and smartphone connectivity.',
                'price' => 199.99,
                'cost' => 85.00,
                'category' => 'Wearables',
                'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?w=500',
                'stock' => 75,
                'featured' => true,
                'meta_title' => 'Smart Fitness Watch with GPS | DropShip Pro',
                'meta_description' => 'Track your fitness goals with our advanced smart watch. GPS, heart rate monitor included.',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'name' => 'Portable Phone Charger',
                'slug' => 'portable-phone-charger',
                'description' => 'High-capacity 20000mAh power bank with fast charging and multiple USB ports.',
                'price' => 39.99,
                'cost' => 18.00,
                'category' => 'Accessories',
                'image' => 'https://images.unsplash.com/photo-1609592707596-c265371f7f9a?w=500',
                'stock' => 100,
                'featured' => false,
                'meta_title' => 'High-Capacity Portable Phone Charger | DropShip Pro',
                'meta_description' => '20000mAh power bank with fast charging. Keep your devices powered anywhere.',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'name' => 'Wireless Mouse',
                'slug' => 'wireless-mouse',
                'description' => 'Ergonomic wireless mouse with precision tracking and long battery life.',
                'price' => 29.99,
                'cost' => 12.00,
                'category' => 'Accessories',
                'image' => 'https://images.unsplash.com/photo-1527864550417-7fd91fc51a46?w=500',
                'stock' => 80,
                'featured' => false,
                'meta_title' => 'Ergonomic Wireless Mouse | DropShip Pro',
                'meta_description' => 'Precision wireless mouse with ergonomic design. Perfect for work and gaming.',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
    }
}
?>
