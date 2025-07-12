<div class="container py-4">
    <h1 class="mb-4">
        <i class="fas fa-shopping-cart me-2"></i>Shopping Cart
    </h1>
    
    <?php if (empty($items)): ?>
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-3x text-muted mb-3"></i>
            <h4 class="text-muted">Your cart is empty</h4>
            <p class="text-muted">Add some products to get started!</p>
            <a href="/products" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-bag me-2"></i>Continue Shopping
            </a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Cart Items (<?= count($items) ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php foreach ($items as $item): ?>
                            <div class="cart-item border-bottom p-3" id="cart-item-<?= $item['product_id'] ?>">
                                <div class="row align-items-center">
                                    <div class="col-md-2">
                                        <img src="<?= htmlspecialchars($item['product']['image']) ?>" 
                                             alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                             class="img-fluid rounded" 
                                             style="height: 80px; object-fit: cover;">
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <h6 class="mb-1">
                                            <a href="/products/<?= $item['product_id'] ?>" 
                                               class="text-decoration-none">
                                                <?= htmlspecialchars($item['product']['name']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            <?= htmlspecialchars($item['product']['category']) ?>
                                        </small>
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <span class="fw-bold"><?= formatPrice($item['price']) ?></span>
                                    </div>
                                    
                                    <div class="col-md-3">
                                        <div class="input-group">
                                            <button class="btn btn-outline-secondary" 
                                                    type="button"
                                                    hx-post="/cart/update"
                                                    hx-vals='{"product_id": "<?= $item['product_id'] ?>", "quantity": "<?= $item['quantity'] - 1 ?>"}'
                                                    hx-target="#cart-item-<?= $item['product_id'] ?>"
                                                    hx-swap="outerHTML">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            
                                            <input type="number" 
                                                   class="form-control text-center" 
                                                   value="<?= $item['quantity'] ?>"
                                                   min="1"
                                                   hx-post="/cart/update"
                                                   hx-vals='{"product_id": "<?= $item['product_id'] ?>"}'
                                                   hx-target="#cart-item-<?= $item['product_id'] ?>"
                                                   hx-swap="outerHTML"
                                                   hx-trigger="change">
                                            
                                            <button class="btn btn-outline-secondary" 
                                                    type="button"
                                                    hx-post="/cart/update"
                                                    hx-vals='{"product_id": "<?= $item['product_id'] ?>", "quantity": "<?= $item['quantity'] + 1 ?>"}'
                                                    hx-target="#cart-item-<?= $item['product_id'] ?>"
                                                    hx-swap="outerHTML">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-1">
                                        <button class="btn btn-outline-danger btn-sm" 
                                                hx-post="/cart/remove"
                                                hx-vals='{"product_id": "<?= $item['product_id'] ?>"}'
                                                hx-target="#cart-item-<?= $item['product_id'] ?>"
                                                hx-swap="outerHTML"
                                                hx-confirm="Remove this item from cart?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span><?= formatPrice($total) ?></span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Shipping:</span>
                            <span class="text-success">Free</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tax:</span>
                            <span>Calculated at checkout</span>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between fw-bold">
                            <span>Total:</span>
                            <span class="h5 text-primary"><?= formatPrice($total) ?></span>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <a href="/checkout" class="btn btn-primary btn-lg">
                                <i class="fas fa-credit-card me-2"></i>Proceed to Checkout
                            </a>
                            <a href="/products" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Security Badges -->
                <div class="card mt-3">
                    <div class="card-body text-center">
                        <h6 class="mb-3">Secure Shopping</h6>
                        <div class="row">
                            <div class="col-4">
                                <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                                <small class="d-block">SSL Secure</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-lock fa-2x text-success mb-2"></i>
                                <small class="d-block">256-bit Encryption</small>
                            </div>
                            <div class="col-4">
                                <i class="fas fa-credit-card fa-2x text-success mb-2"></i>
                                <small class="d-block">Secure Payment</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
