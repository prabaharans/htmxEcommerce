<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Products</h1>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
        <i class="fas fa-plus me-2"></i>Add Product
    </button>
</div>

<!-- Products Table -->
<div class="card shadow">
    <div class="card-body">
        <?php if (empty($products)): ?>
            <div class="text-center py-5">
                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No products found</h4>
                <p class="text-muted">Add your first product to get started.</p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal">
                    <i class="fas fa-plus me-2"></i>Add First Product
                </button>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Cost</th>
                            <th>Stock</th>
                            <th>Featured</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr id="product-<?= $product['id'] ?>">
                                <td>
                                    <img src="<?= htmlspecialchars($product['image']) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>"
                                         class="img-thumbnail" 
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($product['name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars(substr($product['description'], 0, 50)) ?>...</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary"><?= htmlspecialchars($product['category']) ?></span>
                                </td>
                                <td class="fw-bold text-success"><?= formatPrice($product['price']) ?></td>
                                <td class="text-muted"><?= formatPrice($product['cost']) ?></td>
                                <td>
                                    <span class="badge <?= $product['stock'] > 10 ? 'bg-success' : ($product['stock'] > 0 ? 'bg-warning' : 'bg-danger') ?>">
                                        <?= $product['stock'] ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($product['featured']): ?>
                                        <i class="fas fa-star text-warning"></i>
                                    <?php else: ?>
                                        <i class="far fa-star text-muted"></i>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-primary" 
                                                onclick="editProduct(<?= htmlspecialchars(json_encode($product)) ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" 
                                                hx-post="/admin/products/delete"
                                                hx-vals='{"id": "<?= $product['id'] ?>"}'
                                                hx-target="#product-<?= $product['id'] ?>"
                                                hx-swap="outerHTML"
                                                hx-confirm="Are you sure you want to delete this product?">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Product Modal -->
<div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Add Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="productForm" hx-post="/admin/products/create" hx-target="#form-result">
                <div class="modal-body">
                    <div id="form-result"></div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="name" class="form-label">Product Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <input type="text" class="form-control" id="category" name="category" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Description *</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Selling Price *</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="cost" class="form-label">Cost Price</label>
                            <div class="input-group">
                                <span class="input-group-text"><?= CURRENCY_SYMBOL ?></span>
                                <input type="number" class="form-control" id="cost" name="cost" step="0.01">
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="image" class="form-label">Image URL *</label>
                        <input type="url" class="form-control" id="image" name="image" required>
                        <div class="form-text">Enter a valid URL for the product image</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="meta_title" class="form-label">SEO Title</label>
                            <input type="text" class="form-control" id="meta_title" name="meta_title">
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured">
                                <label class="form-check-label" for="featured">
                                    Featured Product
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="meta_description" class="form-label">SEO Description</label>
                        <textarea class="form-control" id="meta_description" name="meta_description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Save Product
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editProduct(product) {
    // Populate form with product data
    document.getElementById('name').value = product.name;
    document.getElementById('category').value = product.category;
    document.getElementById('description').value = product.description;
    document.getElementById('price').value = product.price;
    document.getElementById('cost').value = product.cost;
    document.getElementById('stock').value = product.stock;
    document.getElementById('image').value = product.image;
    document.getElementById('meta_title').value = product.meta_title || '';
    document.getElementById('meta_description').value = product.meta_description || '';
    document.getElementById('featured').checked = product.featured;
    
    // Change form action and modal title
    document.getElementById('productForm').setAttribute('hx-post', '/admin/products/update');
    document.getElementById('productModalLabel').textContent = 'Edit Product';
    
    // Add hidden ID field
    let idField = document.getElementById('product_id');
    if (!idField) {
        idField = document.createElement('input');
        idField.type = 'hidden';
        idField.id = 'product_id';
        idField.name = 'id';
        document.getElementById('productForm').appendChild(idField);
    }
    idField.value = product.id;
    
    // Show modal
    new bootstrap.Modal(document.getElementById('productModal')).show();
}

// Reset form when modal is closed
document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('productForm').reset();
    document.getElementById('productForm').setAttribute('hx-post', '/admin/products/create');
    document.getElementById('productModalLabel').textContent = 'Add Product';
    
    const idField = document.getElementById('product_id');
    if (idField) {
        idField.remove();
    }
    
    document.getElementById('form-result').innerHTML = '';
});
</script>
