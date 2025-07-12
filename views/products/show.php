<div class="container py-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="/">Home</a></li>
            <li class="breadcrumb-item"><a href="/products">Products</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($product['name']) ?></li>
        </ol>
    </nav>
    
    <div class="row">
        <!-- Product Image -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <img src="<?= htmlspecialchars($product['image']) ?>" 
                     class="card-img-top" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     style="height: 500px; object-fit: cover;">
            </div>
        </div>
        
        <!-- Product Details -->
        <div class="col-lg-6">
            <h1 class="mb-3"><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="mb-3">
                <span class="badge bg-primary me-2"><?= htmlspecialchars($product['category']) ?></span>
                <?php if ($product['featured']): ?>
                    <span class="badge bg-warning">Featured</span>
                <?php endif; ?>
            </div>
            
            <div class="mb-4">
                <span class="h2 text-primary"><?= formatPrice($product['price']) ?></span>
            </div>
            
            <div class="mb-4">
                <h5>Description</h5>
                <p class="text-muted"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
            </div>
            
            <div class="mb-4">
                <div class="row">
                    <div class="col-6">
                        <strong>Stock:</strong>
                        <span class="text-success">
                            <i class="fas fa-check-circle me-1"></i><?= $product['stock'] ?> available
                        </span>
                    </div>
                    <div class="col-6">
                        <strong>SKU:</strong> <?= $product['id'] ?>
                    </div>
                </div>
            </div>
            
            <!-- Add to Cart Form -->
            <form hx-post="/cart/add" hx-target="#cart-message" hx-swap="innerHTML">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                
                <div class="row mb-3">
                    <div class="col-4">
                        <label for="quantity" class="form-label">Quantity</label>
                        <input type="number" 
                               class="form-control" 
                               id="quantity" 
                               name="quantity" 
                               value="1" 
                               min="1" 
                               max="<?= $product['stock'] ?>">
                    </div>
                </div>
                
                <div class="d-grid gap-2 d-md-flex">
                    <button type="submit" 
                            class="btn btn-primary btn-lg flex-grow-1"
                            <?= $product['stock'] <= 0 ? 'disabled' : '' ?>>
                        <i class="fas fa-cart-plus me-2"></i>
                        <?= $product['stock'] > 0 ? 'Add to Cart' : 'Out of Stock' ?>
                    </button>
                    <button type="button" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-heart"></i>
                    </button>
                </div>
            </form>
            
            <!-- Cart Message Area -->
            <div id="cart-message" class="mt-3"></div>
            
            <!-- Product Features -->
            <div class="mt-5">
                <h5>Product Features</h5>
                <ul class="list-unstyled">
                    <li><i class="fas fa-shipping-fast text-primary me-2"></i>Fast worldwide shipping</li>
                    <li><i class="fas fa-shield-alt text-primary me-2"></i>Quality guarantee</li>
                    <li><i class="fas fa-undo text-primary me-2"></i>30-day return policy</li>
                    <li><i class="fas fa-headset text-primary me-2"></i>24/7 customer support</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <div class="mt-5">
        <h3 class="mb-4">You might also like</h3>
        <div class="row">
            <div class="col-12">
                <p class="text-muted">Related products will be displayed here based on category and preferences.</p>
            </div>
        </div>
    </div>
</div>

<!-- SEO Structured Data -->
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "<?= htmlspecialchars($product['name']) ?>",
    "description": "<?= htmlspecialchars($product['description']) ?>",
    "image": "<?= htmlspecialchars($product['image']) ?>",
    "sku": "<?= $product['id'] ?>",
    "brand": {
        "@type": "Brand",
        "name": "<?= APP_NAME ?>"
    },
    "offers": {
        "@type": "Offer",
        "price": "<?= $product['price'] ?>",
        "priceCurrency": "<?= CURRENCY ?>",
        "availability": "<?= $product['stock'] > 0 ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock' ?>",
        "seller": {
            "@type": "Organization",
            "name": "<?= APP_NAME ?>"
        }
    }
}
</script>
