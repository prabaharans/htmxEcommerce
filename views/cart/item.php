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
                       name="quantity"
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
