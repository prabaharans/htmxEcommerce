<?php
session_start();

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include configuration and core files
require_once 'config/app.php';
require_once 'config/database.php';
require_once 'core/Router.php';
require_once 'core/Controller.php';
require_once 'core/Model.php';
require_once 'core/View.php';
require_once 'storage/InMemoryStorage.php';

// Initialize storage
$storage = InMemoryStorage::getInstance();

// Initialize router
$router = new Router();

// Define routes
$router->get('/', 'HomeController@index');
$router->get('/products', 'ProductController@index');
$router->get('/products/search', 'ProductController@search');
$router->get('/products/{id}', 'ProductController@show');
$router->get('/cart', 'CartController@index');
$router->post('/cart/add', 'CartController@add');
$router->post('/cart/update', 'CartController@update');
$router->post('/cart/remove', 'CartController@remove');
$router->get('/checkout', 'CheckoutController@index');
$router->post('/checkout/process', 'CheckoutController@process');
$router->get('/admin', 'AdminController@dashboard');
$router->get('/admin/products', 'AdminController@products');
$router->get('/admin/orders', 'AdminController@orders');
$router->post('/admin/products/create', 'AdminController@createProduct');
$router->post('/admin/products/update', 'AdminController@updateProduct');
$router->post('/admin/products/delete', 'AdminController@deleteProduct');
$router->get('/orders/{id}/invoice', 'OrderController@invoice');

// Handle the request
$router->dispatch();
?>
