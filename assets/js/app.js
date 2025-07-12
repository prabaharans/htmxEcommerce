// DropShip Pro - Main JavaScript File

// Global App Configuration
const App = {
    config: {
        apiUrl: window.location.origin,
        currency: '$',
        cartUpdateDelay: 1000,
        searchDelay: 300,
        animationDuration: 300
    },
    
    // Initialize the application
    init() {
        this.setupEventListeners();
        this.initializeComponents();
        this.setupHTMXEvents();
        console.log('DropShip Pro initialized');
    },
    
    // Setup global event listeners
    setupEventListeners() {
        // Cart count updates
        document.addEventListener('cartUpdated', this.handleCartUpdate.bind(this));
        
        // Search functionality
        this.setupSearchHandlers();
        
        // Form validation
        this.setupFormValidation();
        
        // Image lazy loading
        this.setupLazyLoading();
        
        // Keyboard navigation
        this.setupKeyboardNavigation();
    },
    
    // Initialize components
    initializeComponents() {
        this.initializeTooltips();
        this.initializePopovers();
        this.initializeCarousels();
        this.initializeModals();
    },
    
    // Setup HTMX-specific event handlers
    setupHTMXEvents() {
        // Show loading states
        document.addEventListener('htmx:beforeRequest', (e) => {
            this.showLoading(e.target);
        });
        
        // Hide loading states
        document.addEventListener('htmx:afterRequest', (e) => {
            this.hideLoading(e.target);
        });
        
        // Handle errors
        document.addEventListener('htmx:responseError', (e) => {
            this.handleError('Request failed. Please try again.');
        });
        
        // Handle successful responses
        document.addEventListener('htmx:afterSwap', (e) => {
            this.initializeComponents();
        });
    },
    
    // Handle cart updates
    handleCartUpdate(event) {
        setTimeout(() => {
            this.updateCartCount();
            this.showNotification('Cart updated successfully!', 'success');
        }, this.config.cartUpdateDelay);
    },
    
    // Update cart count display
    updateCartCount() {
        fetch(`${this.config.apiUrl}/api/cart/count`)
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.getElementById('cart-count');
                if (cartBadge && data.count !== undefined) {
                    cartBadge.textContent = data.count;
                    this.animateElement(cartBadge, 'pulse');
                }
            })
            .catch(error => {
                console.error('Failed to update cart count:', error);
            });
    },
    
    // Setup search handlers
    setupSearchHandlers() {
        const searchInput = document.querySelector('input[name="q"]');
        const searchResults = document.getElementById('search-results');
        
        if (!searchInput || !searchResults) return;
        
        let searchTimeout;
        
        // Handle search input
        searchInput.addEventListener('input', (e) => {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            if (query.length === 0) {
                this.hideSearchResults();
                return;
            }
            
            searchTimeout = setTimeout(() => {
                this.performSearch(query);
            }, this.config.searchDelay);
        });
        
        // Handle search focus
        searchInput.addEventListener('focus', () => {
            if (searchInput.value.trim()) {
                this.showSearchResults();
            }
        });
        
        // Hide results when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.search-container') && !e.target.closest('#search-results')) {
                this.hideSearchResults();
            }
        });
    },
    
    // Perform search
    performSearch(query) {
        fetch(`${this.config.apiUrl}/products/search?q=${encodeURIComponent(query)}`)
            .then(response => response.text())
            .then(html => {
                const searchResults = document.getElementById('search-results');
                if (searchResults) {
                    searchResults.innerHTML = html;
                    this.showSearchResults();
                }
            })
            .catch(error => {
                console.error('Search failed:', error);
            });
    },
    
    // Show search results
    showSearchResults() {
        const searchResults = document.getElementById('search-results');
        if (searchResults) {
            searchResults.style.display = 'block';
            this.animateElement(searchResults, 'fadeIn');
        }
    },
    
    // Hide search results
    hideSearchResults() {
        const searchResults = document.getElementById('search-results');
        if (searchResults) {
            searchResults.style.display = 'none';
        }
    },
    
    // Setup form validation
    setupFormValidation() {
        const forms = document.querySelectorAll('form[data-validate]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.validateForm(form)) {
                    e.preventDefault();
                    return false;
                }
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });
            });
        });
    },
    
    // Validate form
    validateForm(form) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!this.validateField(input)) {
                isValid = false;
            }
        });
        
        return isValid;
    },
    
    // Validate individual field
    validateField(field) {
        const value = field.value.trim();
        let isValid = true;
        let message = '';
        
        // Required validation
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            message = 'This field is required.';
        }
        
        // Email validation
        if (field.type === 'email' && value && !this.isValidEmail(value)) {
            isValid = false;
            message = 'Please enter a valid email address.';
        }
        
        // Phone validation
        if (field.type === 'tel' && value && !this.isValidPhone(value)) {
            isValid = false;
            message = 'Please enter a valid phone number.';
        }
        
        // Number validation
        if (field.type === 'number' && value) {
            const min = field.getAttribute('min');
            const max = field.getAttribute('max');
            const numValue = parseFloat(value);
            
            if (min && numValue < parseFloat(min)) {
                isValid = false;
                message = `Value must be at least ${min}.`;
            }
            
            if (max && numValue > parseFloat(max)) {
                isValid = false;
                message = `Value must be no more than ${max}.`;
            }
        }
        
        this.showFieldValidation(field, isValid, message);
        return isValid;
    },
    
    // Show field validation
    showFieldValidation(field, isValid, message) {
        // Remove existing validation
        field.classList.remove('is-valid', 'is-invalid');
        const existingFeedback = field.parentNode.querySelector('.invalid-feedback');
        if (existingFeedback) {
            existingFeedback.remove();
        }
        
        if (!isValid) {
            field.classList.add('is-invalid');
            
            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback';
            feedback.textContent = message;
            field.parentNode.appendChild(feedback);
        } else if (field.value.trim()) {
            field.classList.add('is-valid');
        }
    },
    
    // Email validation
    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },
    
    // Phone validation
    isValidPhone(phone) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        return phoneRegex.test(phone.replace(/[\s\-\(\)]/g, ''));
    },
    
    // Setup lazy loading for images
    setupLazyLoading() {
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
    },
    
    // Setup keyboard navigation
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // ESC key to close modals/dropdowns
            if (e.key === 'Escape') {
                this.closeModals();
                this.hideSearchResults();
            }
            
            // Enter key for search
            if (e.key === 'Enter' && e.target.matches('input[name="q"]')) {
                e.preventDefault();
                const form = e.target.closest('form');
                if (form) {
                    form.dispatchEvent(new Event('submit'));
                }
            }
        });
    },
    
    // Initialize Bootstrap tooltips
    initializeTooltips() {
        const tooltipElements = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        tooltipElements.forEach(el => {
            new bootstrap.Tooltip(el);
        });
    },
    
    // Initialize Bootstrap popovers
    initializePopovers() {
        const popoverElements = document.querySelectorAll('[data-bs-toggle="popover"]');
        popoverElements.forEach(el => {
            new bootstrap.Popover(el);
        });
    },
    
    // Initialize Bootstrap carousels
    initializeCarousels() {
        const carouselElements = document.querySelectorAll('.carousel');
        carouselElements.forEach(el => {
            new bootstrap.Carousel(el);
        });
    },
    
    // Initialize modals
    initializeModals() {
        const modalElements = document.querySelectorAll('.modal');
        modalElements.forEach(el => {
            el.addEventListener('shown.bs.modal', () => {
                const autofocus = el.querySelector('[autofocus]');
                if (autofocus) {
                    autofocus.focus();
                }
            });
        });
    },
    
    // Show loading state
    showLoading(element) {
        if (element) {
            element.classList.add('loading');
            element.style.pointerEvents = 'none';
        }
    },
    
    // Hide loading state
    hideLoading(element) {
        if (element) {
            element.classList.remove('loading');
            element.style.pointerEvents = '';
        }
    },
    
    // Show notification
    showNotification(message, type = 'info', duration = 5000) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        
        notification.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto-remove after duration
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, duration);
    },
    
    // Handle errors
    handleError(message) {
        this.showNotification(message, 'danger');
        console.error(message);
    },
    
    // Animate element
    animateElement(element, animation) {
        element.classList.add(animation);
        setTimeout(() => {
            element.classList.remove(animation);
        }, this.config.animationDuration);
    },
    
    // Close all modals
    closeModals() {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    },
    
    // Format price
    formatPrice(price) {
        return this.config.currency + parseFloat(price).toFixed(2);
    },
    
    // Format number
    formatNumber(number) {
        return new Intl.NumberFormat().format(number);
    },
    
    // Debounce function
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },
    
    // Throttle function
    throttle(func, limit) {
        let inThrottle;
        return function() {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => inThrottle = false, limit);
            }
        };
    }
};

// Product-specific functionality
const ProductManager = {
    // Add to cart with animation
    addToCart(productId, quantity = 1) {
        const data = {
            product_id: productId,
            quantity: quantity
        };
        
        fetch(`${App.config.apiUrl}/cart/add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                App.showNotification('Product added to cart!', 'success');
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            } else {
                App.handleError(result.message || 'Failed to add product to cart');
            }
        })
        .catch(error => {
            App.handleError('Failed to add product to cart');
        });
    },
    
    // Quick view product
    quickView(productId) {
        fetch(`${App.config.apiUrl}/products/${productId}/quick-view`)
            .then(response => response.text())
            .then(html => {
                const modal = document.createElement('div');
                modal.innerHTML = html;
                document.body.appendChild(modal);
                
                const modalInstance = new bootstrap.Modal(modal.querySelector('.modal'));
                modalInstance.show();
                
                modal.addEventListener('hidden.bs.modal', () => {
                    modal.remove();
                });
            })
            .catch(error => {
                App.handleError('Failed to load product details');
            });
    }
};

// Cart-specific functionality
const CartManager = {
    // Update cart item quantity
    updateQuantity(productId, quantity) {
        const data = {
            product_id: productId,
            quantity: quantity
        };
        
        fetch(`${App.config.apiUrl}/cart/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => {
            if (response.ok) {
                document.dispatchEvent(new CustomEvent('cartUpdated'));
            }
        })
        .catch(error => {
            App.handleError('Failed to update cart');
        });
    },
    
    // Remove item from cart
    removeItem(productId) {
        if (!confirm('Remove this item from cart?')) {
            return;
        }
        
        const data = { product_id: productId };
        
        fetch(`${App.config.apiUrl}/cart/remove`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams(data)
        })
        .then(response => {
            if (response.ok) {
                document.dispatchEvent(new CustomEvent('cartUpdated'));
                App.showNotification('Item removed from cart', 'info');
            }
        })
        .catch(error => {
            App.handleError('Failed to remove item');
        });
    }
};

// Initialize the application when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    App.init();
});

// Export for global access
window.App = App;
window.ProductManager = ProductManager;
window.CartManager = CartManager;
