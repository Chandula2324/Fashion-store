// Fashion Store - Main JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('.nav');
    if (menuToggle && nav) {
        menuToggle.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    }

    // Size Selection on Product Cards
    document.querySelectorAll('.product-card').forEach(card => {
        const sizeOptions = card.querySelectorAll('.size-option');
        sizeOptions.forEach(opt => {
            opt.addEventListener('click', function() {
                if (this.classList.contains('disabled')) return;
                sizeOptions.forEach(o => o.classList.remove('selected'));
                this.classList.add('selected');
                const size = this.dataset.size;
                const btn = card.querySelector('.add-to-cart');
                if (btn) btn.dataset.size = size;
            });
        });
    });

    // Size Selection on Product Detail
    const detailSizes = document.querySelectorAll('.size-btn');
    detailSizes.forEach(btn => {
        btn.addEventListener('click', function() {
            detailSizes.forEach(b => b.classList.remove('selected'));
            this.classList.add('selected');
            const hiddenInput = document.getElementById('selected-size');
            if (hiddenInput) hiddenInput.value = this.dataset.size;
        });
    });

    // Quantity Selector
    const qtyBtns = document.querySelectorAll('.quantity-btn');
    qtyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.quantity-input');
            let val = parseInt(input.value) || 1;
            if (this.dataset.action === 'minus' && val > 1) val--;
            if (this.dataset.action === 'plus') val++;
            input.value = val;
        });
    });

    // Payment Method Selection
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(pm => {
        pm.addEventListener('click', function() {
            paymentMethods.forEach(p => p.classList.remove('selected'));
            this.classList.add('selected');
            const hidden = document.getElementById('payment-method');
            if (hidden) hidden.value = this.dataset.method;
        });
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.5s';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });

    // Add to Cart AJAX
    document.querySelectorAll('.add-to-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.dataset.id;
            const size = this.dataset.size || 'M';
            const qtyInput = document.querySelector('.quantity-input');
            const qty = qtyInput ? qtyInput.value : 1;

            fetch('cart.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `product_id=${productId}&size=${size}&quantity=${qty}`
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    updateCartCount(data.count);
                    showNotification('Added to cart!', 'success');
                } else {
                    showNotification(data.message || 'Error adding to cart', 'error');
                }
            })
            .catch(() => showNotification('Network error', 'error'));
        });
    });
});

function updateCartCount(count) {
    const badge = document.querySelector('.cart-count');
    if (badge) {
        badge.textContent = count;
        badge.style.transform = 'scale(1.3)';
        setTimeout(() => badge.style.transform = 'scale(1)', 200);
    }
}

function showNotification(message, type) {
    const div = document.createElement('div');
    div.className = `alert alert-${type}`;
    div.textContent = message;
    div.style.position = 'fixed';
    div.style.top = '20px';
    div.style.right = '20px';
    div.style.zIndex = '9999';
    div.style.minWidth = '250px';
    document.body.appendChild(div);
    setTimeout(() => {
        div.style.opacity = '0';
        div.style.transition = 'opacity 0.5s';
        setTimeout(() => div.remove(), 500);
    }, 3000);
}
