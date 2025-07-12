<div class="container py-4">
    <h1 class="mb-4">
        <i class="fas fa-credit-card me-2"></i>Checkout
    </h1>
    
    <div class="row">
        <!-- Checkout Form -->
        <div class="col-lg-8">
            <form id="checkout-form" hx-post="/checkout/process" hx-target="#checkout-result">
                <!-- Customer Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>Customer Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="name" 
                                       name="name" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" 
                                       class="form-control" 
                                       id="email" 
                                       name="email" 
                                       required>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Shipping Address -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-shipping-fast me-2"></i>Shipping Address
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="address" class="form-label">Street Address *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="address" 
                                   name="address" 
                                   required>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="city" class="form-label">City *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="city" 
                                       name="city" 
                                       required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="postal_code" class="form-label">Postal Code *</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="postal_code" 
                                       name="postal_code" 
                                       required>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="country" class="form-label">Country *</label>
                                <select class="form-control" id="country" name="country" required>
                                    <option value="">Select Country</option>
                                    <option value="US">United States</option>
                                    <option value="CA">Canada</option>
                                    <option value="UK">United Kingdom</option>
                                    <option value="AU">Australia</option>
                                    <option value="DE">Germany</option>
                                    <option value="FR">France</option>
                                    <option value="IT">Italy</option>
                                    <option value="ES">Spain</option>
                                    <option value="JP">Japan</option>
                                    <option value="BR">Brazil</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-credit-card me-2"></i>Payment Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div class="d-flex align-items-center">
                                <i class="fab fa-stripe fa-2x text-primary me-3"></i>
                                <div>
                                    <strong>Secure Payment via Stripe</strong>
                                    <br>
                                    <small class="text-muted">Your payment information is encrypted and secure</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Stripe Elements will be inserted here -->
                        <div id="stripe-card-element" class="form-control" style="height: 40px; padding: 10px;">
                            <!-- Stripe Elements will create form elements here -->
                        </div>
                        <div id="stripe-card-errors" class="text-danger mt-2"></div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid gap-2">
                    <button type="submit" 
                            class="btn btn-primary btn-lg" 
                            id="submit-payment">
                        <i class="fas fa-lock me-2"></i>
                        Complete Order - <?= formatPrice($total) ?>
                    </button>
                </div>
            </form>
            
            <!-- Checkout Result -->
            <div id="checkout-result" class="mt-3"></div>
        </div>
        
        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    <?php foreach ($items as $item): ?>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center">
                                <img src="<?= htmlspecialchars($item['product']['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['product']['name']) ?>"
                                     class="me-2" 
                                     style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px;">
                                <div>
                                    <small class="fw-bold"><?= htmlspecialchars($item['product']['name']) ?></small>
                                    <br>
                                    <small class="text-muted">Qty: <?= $item['quantity'] ?></small>
                                </div>
                            </div>
                            <span class="fw-bold"><?= formatPrice($item['price'] * $item['quantity']) ?></span>
                        </div>
                    <?php endforeach; ?>
                    
                    <hr>
                    
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
                        <span>$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total:</span>
                        <span class="h5 text-primary"><?= formatPrice($total) ?></span>
                    </div>
                </div>
            </div>
            
            <!-- Trust Badges -->
            <div class="card mt-3">
                <div class="card-body text-center">
                    <h6 class="mb-3">Secure & Trusted</h6>
                    <div class="row">
                        <div class="col-4">
                            <i class="fas fa-shield-alt fa-2x text-success mb-2"></i>
                            <small class="d-block">SSL Protected</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-undo fa-2x text-success mb-2"></i>
                            <small class="d-block">30-Day Returns</small>
                        </div>
                        <div class="col-4">
                            <i class="fas fa-headset fa-2x text-success mb-2"></i>
                            <small class="d-block">24/7 Support</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Initialize Stripe
const stripe = Stripe('<?= $stripeKey ?>');
const elements = stripe.elements();

// Create card element
const cardElement = elements.create('card', {
    style: {
        base: {
            fontSize: '16px',
            color: '#424770',
            '::placeholder': {
                color: '#aab7c4',
            },
        },
    },
});

cardElement.mount('#stripe-card-element');

// Handle form submission
const form = document.getElementById('checkout-form');
form.addEventListener('submit', async (event) => {
    event.preventDefault();
    
    const submitButton = document.getElementById('submit-payment');
    submitButton.disabled = true;
    submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    
    // Create payment method
    const {error, paymentMethod} = await stripe.createPaymentMethod({
        type: 'card',
        card: cardElement,
        billing_details: {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            address: {
                line1: document.getElementById('address').value,
                city: document.getElementById('city').value,
                postal_code: document.getElementById('postal_code').value,
                country: document.getElementById('country').value,
            },
        },
    });
    
    if (error) {
        // Show error to customer
        document.getElementById('stripe-card-errors').textContent = error.message;
        submitButton.disabled = false;
        submitButton.innerHTML = '<i class="fas fa-lock me-2"></i>Complete Order - <?= formatPrice($total) ?>';
    } else {
        // Submit form with payment method
        const formData = new FormData(form);
        formData.append('payment_method_id', paymentMethod.id);
        
        // Use HTMX to submit
        htmx.ajax('POST', '/checkout/process', {
            values: Object.fromEntries(formData),
            target: '#checkout-result'
        });
    }
});

// Handle card validation errors
cardElement.on('change', ({error}) => {
    const displayError = document.getElementById('stripe-card-errors');
    if (error) {
        displayError.textContent = error.message;
    } else {
        displayError.textContent = '';
    }
});
</script>
