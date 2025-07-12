<!-- Hero Section -->
<section class="hero bg-primary text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">Premium Products, Worldwide Shipping</h1>
                <p class="lead mb-4">Discover amazing products with fast, reliable dropshipping. Quality guaranteed, shipped directly to your door.</p>
                <a href="/products" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag me-2"></i>Shop Now
                </a>
            </div>
            <div class="col-lg-6">
                <i class="fas fa-shipping-fast" style="font-size: 200px; opacity: 0.3;"></i>
            </div>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Featured Products</h2>
        
        <?php if (empty($products)): ?>
            <div class="text-center">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No featured products available</h4>
                <p class="text-muted">Check back soon for exciting new arrivals!</p>
            </div>
        <?php else: ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-md-6 col-lg-3 mb-4">
                        <div class="card h-100 product-card">
                            <img src="<?= htmlspecialchars($product['image']) ?>" 
                                 class="card-img-top" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 style="height: 200px; object-fit: cover;">
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                                <p class="card-text text-muted flex-grow-1">
                                    <?= htmlspecialchars(substr($product['description'], 0, 100)) ?>...
                                </p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="h5 text-primary mb-0"><?= formatPrice($product['price']) ?></span>
                                    <span class="badge bg-secondary"><?= htmlspecialchars($product['category']) ?></span>
                                </div>
                            </div>
                            
                            <div class="card-footer bg-transparent">
                                <div class="d-grid gap-2">
                                    <a href="/products/<?= $product['id'] ?>" class="btn btn-outline-primary">
                                        <i class="fas fa-eye me-2"></i>View Details
                                    </a>
                                    <button class="btn btn-primary" 
                                            hx-post="/cart/add" 
                                            hx-vals='{"product_id": "<?= $product['id'] ?>", "quantity": 1}'
                                            hx-target="#add-to-cart-message"
                                            hx-swap="innerHTML">
                                        <i class="fas fa-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <!-- Add to Cart Message Area -->
        <div id="add-to-cart-message" class="mt-3"></div>
        
        <div class="text-center mt-5">
            <a href="/products" class="btn btn-primary btn-lg">
                <i class="fas fa-th-large me-2"></i>View All Products
            </a>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="bg-light py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-shipping-fast fa-3x text-primary mb-3"></i>
                <h4>Fast Shipping</h4>
                <p class="text-muted">Quick and reliable delivery worldwide</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-shield-alt fa-3x text-primary mb-3"></i>
                <h4>Quality Guaranteed</h4>
                <p class="text-muted">Premium products with full warranty</p>
            </div>
            <div class="col-md-4 text-center mb-4">
                <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                <h4>24/7 Support</h4>
                <p class="text-muted">Customer service when you need it</p>
            </div>
        </div>
    </div>
</section>
