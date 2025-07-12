<?php

// Database configuration for PostgreSQL
class DatabaseConfig {
    private static $connection = null;
    
    public static function getConnection() {
        if (self::$connection === null) {
            try {
                $host = $_ENV['PGHOST'] ?? 'localhost';
                $port = $_ENV['PGPORT'] ?? '5432';
                $dbname = $_ENV['PGDATABASE'] ?? 'defaultdb';
                $username = $_ENV['PGUSER'] ?? 'postgres';
                $password = $_ENV['PGPASSWORD'] ?? '';
                
                $dsn = "pgsql:host={$host};port={$port};dbname={$dbname}";
                self::$connection = new PDO($dsn, $username, $password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]);
                
                // Initialize database tables
                self::initializeTables();
                
            } catch (PDOException $e) {
                error_log('Database connection failed: ' . $e->getMessage());
                throw new Exception('Database connection failed');
            }
        }
        
        return self::$connection;
    }
    
    private static function initializeTables() {
        $sql = [
            // Products table
            "CREATE TABLE IF NOT EXISTS products (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) UNIQUE NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                cost DECIMAL(10,2) DEFAULT 0,
                category VARCHAR(100),
                image VARCHAR(500),
                stock INTEGER DEFAULT 0,
                featured BOOLEAN DEFAULT FALSE,
                meta_title VARCHAR(255),
                meta_description TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Users table
            "CREATE TABLE IF NOT EXISTS users (
                id SERIAL PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                role VARCHAR(50) DEFAULT 'customer',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Orders table
            "CREATE TABLE IF NOT EXISTS orders (
                id SERIAL PRIMARY KEY,
                order_number VARCHAR(50) UNIQUE NOT NULL,
                customer_name VARCHAR(255) NOT NULL,
                customer_email VARCHAR(255) NOT NULL,
                customer_phone VARCHAR(50),
                customer_address TEXT NOT NULL,
                customer_city VARCHAR(100) NOT NULL,
                customer_postal_code VARCHAR(20) NOT NULL,
                customer_country VARCHAR(100) NOT NULL,
                subtotal DECIMAL(10,2) NOT NULL,
                tax DECIMAL(10,2) DEFAULT 0,
                shipping DECIMAL(10,2) DEFAULT 0,
                total DECIMAL(10,2) NOT NULL,
                status VARCHAR(50) DEFAULT 'pending',
                payment_id VARCHAR(255),
                payment_status VARCHAR(50) DEFAULT 'pending',
                supplier_order_id VARCHAR(255),
                tracking_number VARCHAR(255),
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Order items table
            "CREATE TABLE IF NOT EXISTS order_items (
                id SERIAL PRIMARY KEY,
                order_id INTEGER REFERENCES orders(id) ON DELETE CASCADE,
                product_id INTEGER REFERENCES products(id),
                product_name VARCHAR(255) NOT NULL,
                product_image VARCHAR(500),
                price DECIMAL(10,2) NOT NULL,
                quantity INTEGER NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )",
            
            // Cart sessions table (for persistent cart storage)
            "CREATE TABLE IF NOT EXISTS cart_sessions (
                id SERIAL PRIMARY KEY,
                session_id VARCHAR(255) NOT NULL,
                product_id INTEGER REFERENCES products(id),
                quantity INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(session_id, product_id)
            )"
        ];
        
        foreach ($sql as $query) {
            self::$connection->exec($query);
        }
        
        // Insert default products if table is empty
        self::insertDefaultProducts();
    }
    
    private static function insertDefaultProducts() {
        $stmt = self::$connection->query("SELECT COUNT(*) as count FROM products");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            $defaultProducts = [
                [
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
                    'meta_description' => 'High-quality wireless headphones with noise cancellation. Free shipping worldwide.'
                ],
                [
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
                    'meta_description' => 'Track your fitness goals with our advanced smart watch. GPS, heart rate monitor included.'
                ],
                [
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
                    'meta_description' => '20000mAh power bank with fast charging. Keep your devices powered anywhere.'
                ],
                [
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
                    'meta_description' => 'Precision wireless mouse with ergonomic design. Perfect for work and gaming.'
                ],
                [
                    'name' => 'USB-C Hub',
                    'slug' => 'usb-c-hub',
                    'description' => '7-in-1 USB-C hub with HDMI, USB 3.0, and PD charging support.',
                    'price' => 49.99,
                    'cost' => 22.00,
                    'category' => 'Accessories',
                    'image' => 'https://images.unsplash.com/photo-1625842268584-8f3296236761?w=500',
                    'stock' => 60,
                    'featured' => true,
                    'meta_title' => '7-in-1 USB-C Hub | DropShip Pro',
                    'meta_description' => 'Expand your connectivity with our versatile USB-C hub. HDMI, USB 3.0, and more.'
                ],
                [
                    'name' => 'Bluetooth Speaker',
                    'slug' => 'bluetooth-speaker',
                    'description' => 'Waterproof portable speaker with 360-degree sound and 12-hour battery.',
                    'price' => 79.99,
                    'cost' => 35.00,
                    'category' => 'Electronics',
                    'image' => 'https://images.unsplash.com/photo-1608043152269-423dbba4e7e1?w=500',
                    'stock' => 90,
                    'featured' => false,
                    'meta_title' => 'Waterproof Bluetooth Speaker | DropShip Pro',
                    'meta_description' => 'Premium sound quality with waterproof design. Perfect for outdoor adventures.'
                ]
            ];
            
            $stmt = self::$connection->prepare("
                INSERT INTO products (name, slug, description, price, cost, category, image, stock, featured, meta_title, meta_description)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            foreach ($defaultProducts as $product) {
                $stmt->execute([
                    $product['name'],
                    $product['slug'],
                    $product['description'],
                    $product['price'],
                    $product['cost'],
                    $product['category'],
                    $product['image'],
                    $product['stock'],
                    $product['featured'] ? 't' : 'f',
                    $product['meta_title'],
                    $product['meta_description']
                ]);
            }
        }
    }
    
    public static function getDefaultProducts() {
        // This method is kept for backward compatibility
        // but now returns empty array since products are loaded from DB
        return [];
    }
}
?>