<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' | ' : '' ?><?= APP_NAME ?></title>
    <meta name="description" content="<?= isset($meta_description) ? $meta_description : 'Premium dropshipping products with fast worldwide shipping' ?>">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- HTMX -->
    <script src="https://unpkg.com/htmx.org@1.9.10"></script>
    
    <!-- Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?= APP_NAME ?>",
        "url": "<?= APP_URL ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": "<?= APP_URL ?>/products?search={search_term_string}",
            "query-input": "required name=search_term_string"
        }
    }
    </script>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/">
                <i class="fas fa-shipping-fast me-2"></i><?= APP_NAME ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Products</a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center">
                    <!-- Search Form -->
                    <form class="d-flex me-3" hx-get="/products/search" hx-target="#search-results" hx-trigger="keyup changed delay:300ms from:input">
                        <input class="form-control" type="search" name="q" placeholder="Search products..." style="width: 200px;">
                    </form>
                    
                    <!-- Cart -->
                    <a href="/cart" class="btn btn-outline-light position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="cart-count">
                            <?= $cartCount ?? 0 ?>
                        </span>
                    </a>
                    
                    <!-- Admin Link -->
                    <a href="/admin" class="btn btn-outline-light ms-2">
                        <i class="fas fa-cog"></i>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    
    <!-- Search Results Dropdown -->
    <div class="container">
        <div id="search-results" class="position-absolute bg-white shadow-lg rounded mt-1 w-100" style="z-index: 1000; display: none;"></div>
    </div>
    
    <!-- Main Content -->
    <main>
        <?= $content ?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-light py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5><?= APP_NAME ?></h5>
                    <p>Premium dropshipping products with fast worldwide shipping.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="/products" class="text-light text-decoration-none">Products</a></li>
                        <li><a href="/cart" class="text-light text-decoration-none">Cart</a></li>
                        <li><a href="#" class="text-light text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Info</h5>
                    <p><i class="fas fa-envelope me-2"></i>info@dropshippro.com</p>
                    <p><i class="fas fa-phone me-2"></i>+1 (555) 123-4567</p>
                </div>
            </div>
            <hr class="my-4">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> <?= APP_NAME ?>. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p>Powered by HTMX & PHP</p>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Stripe JS -->
    <script src="https://js.stripe.com/v3/"></script>
    <!-- Custom JS -->
    <script src="/assets/js/app.js"></script>
    
    <script>
        // Update cart count on HTMX events
        document.addEventListener('cartUpdated', function() {
            setTimeout(() => {
                location.reload();
            }, 1000);
        });
        
        // Show/hide search results
        document.querySelector('input[name="q"]').addEventListener('input', function() {
            const results = document.getElementById('search-results');
            if (this.value.trim()) {
                results.style.display = 'block';
            } else {
                results.style.display = 'none';
            }
        });
        
        // Hide search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.d-flex') && !e.target.closest('#search-results')) {
                document.getElementById('search-results').style.display = 'none';
            }
        });
    </script>
</body>
</html>
