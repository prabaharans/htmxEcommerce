<div class="container py-4">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <?php if (!empty($currentCategory)): ?>
                    <?= htmlspecialchars($currentCategory) ?> Products
                <?php elseif (!empty($searchTerm)): ?>
                    Search Results for "<?= htmlspecialchars($searchTerm) ?>"
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h1>
        </div>
    </div>
    
    <div class="row">
        <!-- Filters Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filters</h5>
                </div>
                <div class="card-body">
                    <!-- Categories -->
                    <h6 class="fw-bold">Categories</h6>
                    <div class="list-group list-group-flush mb-3">
                        <a href="/products" 
                           class="list-group-item list-group-item-action <?= empty($currentCategory) ? 'active' : '' ?>">
                            All Categories
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="/products?category=<?= urlencode($category) ?>" 
                               class="list-group-item list-group-item-action <?= $currentCategory === $category ? 'active' : '' ?>">
                                <?= htmlspecialchars($category) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Search -->
                    <h6 class="fw-bold">Search</h6>
                    <form hx-get="/products" hx-target="#product-grid" hx-trigger="submit">
                        <div class="input-group mb-3">
                            <input type="text" 
                                   class="form-control" 
                                   name="search" 
                                   placeholder="Search products..."
                                   value="<?= htmlspecialchars($searchTerm) ?>">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Products Grid -->
        <div class="col-lg-9">
            <div id="product-grid">
                <?php if (empty($products)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No products found</h4>
                        <p class="text-muted">Try adjusting your search criteria or browse different categories.</p>
                        <a href="/products" class="btn btn-primary">View All Products</a>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($products as $product): ?>
                            <div class="col-md-6 col-xl-4 mb-4">
                                <div class="card h-100 product-card">
                                    <img src="<?= htmlspecialchars($product['image']) ?>" 
                                         class="card-img-top" 
                                         alt="<?= htmlspecialchars($product['name']) ?>"
                                         style="height: 250px; object-fit: cover;">
                                    
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="/products/<?= $product['id'] ?>" 
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($product['name']) ?>
                                            </a>
                                        </h5>
                                        <p class="card-text text-muted flex-grow-1">
                                            <?= htmlspecialchars(substr($product['description'], 0, 120)) ?>...
                                        </p>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="h5 text-primary mb-0"><?= formatPrice($product['price']) ?></span>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($product['category']) ?></span>
                                            </div>
                                            <small class="text-muted">
                                                <i class="fas fa-box me-1"></i><?= $product['stock'] ?> in stock
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <div class="d-grid gap-2">
                                            <a href="/products/<?= $product['id'] ?>" 
                                               class="btn btn-outline-primary">
                                                <i class="fas fa-eye me-2"></i>View Details
                                            </a>
                                            <button class="btn btn-primary" 
                                                    hx-post="/cart/add" 
                                                    hx-vals='{"product_id": "<?= $product['id'] ?>", "quantity": 1}'
                                                    hx-target="#cart-message"
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
            </div>
            
            <!-- Cart Message Area -->
            <div id="cart-message" class="mt-3"></div>
        </div>
    </div>
</div>
