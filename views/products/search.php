<div class="dropdown-menu show w-100 p-3">
    <?php if (empty($products)): ?>
        <div class="text-center py-2">
            <i class="fas fa-search text-muted"></i>
            <p class="text-muted mb-0">No products found</p>
        </div>
    <?php else: ?>
        <?php foreach (array_slice($products, 0, 5) as $product): ?>
            <a href="/products/<?= $product['id'] ?>" class="dropdown-item d-flex align-items-center py-2">
                <img src="<?= htmlspecialchars($product['image']) ?>" 
                     alt="<?= htmlspecialchars($product['name']) ?>"
                     class="me-3" 
                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                <div>
                    <div class="fw-bold"><?= htmlspecialchars($product['name']) ?></div>
                    <small class="text-primary"><?= formatPrice($product['price']) ?></small>
                </div>
            </a>
        <?php endforeach; ?>
        
        <?php if (count($products) > 5): ?>
            <div class="dropdown-divider"></div>
            <a href="/products?search=<?= urlencode($_GET['q'] ?? '') ?>" class="dropdown-item text-center text-primary">
                <i class="fas fa-eye me-2"></i>View all <?= count($products) ?> results
            </a>
        <?php endif; ?>
    <?php endif; ?>
</div>
