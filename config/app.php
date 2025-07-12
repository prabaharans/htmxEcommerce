<?php

// Application configuration
define('APP_NAME', 'DropShip Pro');
define('APP_URL', 'http://localhost:5000');
define('APP_ENV', 'development');

// Stripe configuration
define('STRIPE_PUBLISHABLE_KEY', $_ENV['STRIPE_PUBLISHABLE_KEY'] ?? 'pk_test_default');
define('STRIPE_SECRET_KEY', $_ENV['STRIPE_SECRET_KEY'] ?? 'sk_test_default');

// Currency settings
define('CURRENCY', 'USD');
define('CURRENCY_SYMBOL', '$');

// Pagination settings
define('PRODUCTS_PER_PAGE', 12);

// Autoloader for services and models
spl_autoload_register(function($class) {
    $directories = [
        'controllers/',
        'models/',
        'services/',
        'storage/'
    ];
    
    foreach ($directories as $directory) {
        $file = $directory . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            break;
        }
    }
});

// Helper functions
function formatPrice($price) {
    return CURRENCY_SYMBOL . number_format($price, 2);
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateSlug($string) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
}

function isAdmin() {
    return isset($_SESSION['admin']) && $_SESSION['admin'] === true;
}
?>
