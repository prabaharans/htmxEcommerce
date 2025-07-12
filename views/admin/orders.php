<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Orders</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Export</button>
        </div>
    </div>
</div>

<!-- Orders Table -->
<div class="card shadow">
    <div class="card-body">
        <?php if (empty($orders)): ?>
            <div class="text-center py-5">
                <i class="fas fa-shopping-bag fa-3x text-muted mb-3"></i>
                <h4 class="text-muted">No orders yet</h4>
                <p class="text-muted">Orders will appear here once customers start purchasing.</p>
                <a href="/" class="btn btn-primary">
                    <i class="fas fa-external-link-alt me-2"></i>View Store
                </a>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>
                                    <code><?= substr($order['id'], 0, 8) ?>...</code>
                                    <br>
                                    <small class="text-muted">
                                        Payment: <?= substr($order['payment_id'], 0, 10) ?>...
                                    </small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($order['customer']['name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= htmlspecialchars($order['customer']['email']) ?></small>
                                    <br>
                                    <small class="text-muted">
                                        <?= htmlspecialchars($order['customer']['city']) ?>, 
                                        <?= htmlspecialchars($order['customer']['country']) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php foreach ($order['items'] as $item): ?>
                                        <div class="d-flex align-items-center mb-1">
                                            <img src="<?= htmlspecialchars($item['product']['image']) ?>" 
                                                 alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                                 class="me-2" 
                                                 style="width: 30px; height: 30px; object-fit: cover; border-radius: 3px;">
                                            <small>
                                                <?= htmlspecialchars($item['product']['name']) ?> 
                                                (x<?= $item['quantity'] ?>)
                                            </small>
                                        </div>
                                    <?php endforeach; ?>
                                </td>
                                <td class="fw-bold text-success"><?= formatPrice($order['total']) ?></td>
                                <td>
                                    <span class="badge bg-<?= $order['status'] === 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= date('M j, Y', strtotime($order['created_at'])) ?>
                                    <br>
                                    <small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button class="btn btn-outline-info" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#orderModal"
                                                onclick="viewOrder(<?= htmlspecialchars(json_encode($order)) ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <a href="/orders/<?= $order['id'] ?>/invoice" 
                                           class="btn btn-outline-primary"
                                           target="_blank">
                                            <i class="fas fa-file-pdf"></i>
                                        </a>
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

<!-- Order Details Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetails">
                <!-- Order details will be populated here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a id="downloadInvoice" href="#" class="btn btn-primary" target="_blank">
                    <i class="fas fa-download me-2"></i>Download Invoice
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function viewOrder(order) {
    const detailsContainer = document.getElementById('orderDetails');
    const invoiceLink = document.getElementById('downloadInvoice');
    
    invoiceLink.href = '/orders/' + order.id + '/invoice';
    
    detailsContainer.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6>Order Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Order ID:</strong></td>
                        <td><code>${order.id}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td><span class="badge bg-success">${order.status}</span></td>
                    </tr>
                    <tr>
                        <td><strong>Date:</strong></td>
                        <td>${new Date(order.created_at).toLocaleString()}</td>
                    </tr>
                    <tr>
                        <td><strong>Payment ID:</strong></td>
                        <td><code>${order.payment_id}</code></td>
                    </tr>
                    <tr>
                        <td><strong>Total:</strong></td>
                        <td><strong class="text-success">${formatPrice(order.total)}</strong></td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Customer Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Name:</strong></td>
                        <td>${order.customer.name}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>${order.customer.email}</td>
                    </tr>
                    <tr>
                        <td><strong>Address:</strong></td>
                        <td>
                            ${order.customer.address}<br>
                            ${order.customer.city}, ${order.customer.postal_code}<br>
                            ${order.customer.country}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <h6>Order Items</h6>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    ${order.items.map(item => `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <img src="${item.product.image}" 
                                         alt="${item.product.name}"
                                         class="me-3" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                                    <div>
                                        <strong>${item.product.name}</strong><br>
                                        <small class="text-muted">${item.product.category}</small>
                                    </div>
                                </div>
                            </td>
                            <td>${formatPrice(item.price)}</td>
                            <td>${item.quantity}</td>
                            <td><strong>${formatPrice(item.price * item.quantity)}</strong></td>
                        </tr>
                    `).join('')}
                </tbody>
                <tfoot>
                    <tr class="table-dark">
                        <td colspan="3"><strong>Total</strong></td>
                        <td><strong>${formatPrice(order.total)}</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;
}

function formatPrice(price) {
    return '<?= CURRENCY_SYMBOL ?>' + parseFloat(price).toFixed(2);
}
</script>
