<?php
// ============================================
// Bloom Flower Shop — Shopping Cart Page
// ============================================
$pageTitle = 'Your Cart';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
requireLogin(); // Must be logged in
?>
<?php include __DIR__ . '/header.php'; ?>
<main class="page-enter">
<section class="cart-section">
    <div class="container">
        <div class="section-header" style="text-align:left;margin-bottom:2rem;">
            <span class="section-tag">Your Selection</span>
            <h1 class="section-title">Shopping <em>Cart</em></h1>
        </div>

        <!-- Cart content rendered by JavaScript from localStorage -->
        <div class="cart-grid" id="cartGrid">
            <div class="cart-items" id="cartItemsContainer">
                <div class="cart-header">
                    Your Items <span id="cartItemCount" style="font-weight:300;font-size:1rem;color:var(--warm-gray);"></span>
                </div>
                <div id="cartItemsList">
                    <!-- Items rendered dynamically by JS -->
                </div>
            </div>

            <!-- Order Summary -->
            <div class="cart-summary" id="cartSummary">
                <h3 class="summary-title">Order Summary</h3>
                <div class="promo-row">
                    <input type="text" id="promoInput" placeholder="Promo code...">
                    <button class="btn btn-outline btn-sm" onclick="applyPromo()">Apply</button>
                </div>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span id="summarySubtotal">$0.00</span>
                </div>
                <div class="summary-row">
                    <span>Shipping</span>
                    <span id="summaryShipping">$5.00</span>
                </div>
                <div class="summary-row">
                    <span>Discount</span>
                    <span id="summaryDiscount">$0.00</span>
                </div>
                <div class="summary-row total">
                    <span>Total</span>
                    <span id="summaryTotal">$0.00</span>
                </div>
                <button class="btn btn-primary btn-full" style="margin-top:1.5rem;" onclick="checkout()">
                    Proceed to Checkout
                </button>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline btn-full" style="margin-top:0.75rem;">
                    Continue Shopping
                </a>
                <div style="margin-top:1.5rem;font-size:0.8rem;color:var(--warm-gray);text-align:center;line-height:1.7;">
                    🔒 Secure checkout &nbsp;|&nbsp; 🌿 Eco packaging<br>
                    🚚 Free delivery on orders over $40
                </div>
            </div>
        </div>

        <!-- Empty cart state (hidden by default) -->
        <div id="emptyCartState" class="empty-state" style="display:none;padding:5rem 2rem;">
            <div class="empty-icon">🛒</div>
            <h3 class="empty-title">Your cart is empty</h3>
            <p class="empty-text">Time to treat yourself — explore our beautiful flowers!</p>
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary" style="margin-top:1.5rem;">
                Shop Now
            </a>
        </div>
    </div>
</section>
</main>
<?php include __DIR__ . '/footer.php'; ?>

<script>
const SITE_URL = '<?php echo SITE_URL; ?>';

// Render cart from localStorage (DOM manipulation)
let appliedDiscount = 0;

function applyPromo() {
    const code = document.getElementById('promoInput')?.value.trim().toUpperCase();
    const promos = { 'BLOOM20': 0.20, 'WELCOME10': 0.10, 'ROSE15': 0.15 };
    if (promos[code]) {
        appliedDiscount = promos[code];
        window.bloomUI.showToast(`🎉 Code "${code}" applied! ${Math.round(appliedDiscount * 100)}% off`, 'success');
        renderCart();
    } else {
        window.bloomUI.showToast('❌ Invalid promo code.', 'error');
    }
}

function renderCart() {
    const cart = window.bloomCart.getCart();
    const listEl     = document.getElementById('cartItemsList');
    const countEl    = document.getElementById('cartItemCount');
    const cartGrid   = document.getElementById('cartGrid');
    const emptyState = document.getElementById('emptyCartState');

    if (cart.length === 0) {
        cartGrid.style.display   = 'none';
        emptyState.style.display = 'block';
        return;
    }

    cartGrid.style.display   = '';
    emptyState.style.display = 'none';

    const totalItems = cart.reduce((s, i) => s + i.quantity, 0);
    countEl.textContent = `(${totalItems} item${totalItems !== 1 ? 's' : ''})`;

    // Build cart items HTML using DOM manipulation
    listEl.innerHTML = '';
    cart.forEach(item => {
        const div = document.createElement('div');
        div.className = 'cart-item';
        div.dataset.id = item.id;
        div.innerHTML = `
            <div class="cart-item-img">🌸</div>
            <div class="cart-item-info">
                <div class="cart-item-name">${escapeHtml(item.name)}</div>
                <div class="cart-item-price">$${item.price.toFixed(2)} each</div>
                <div class="qty-controls">
                    <button class="qty-btn" onclick="changeItemQty(${item.id}, -1)">−</button>
                    <span class="qty-num" id="qty_${item.id}">${item.quantity}</span>
                    <button class="qty-btn" onclick="changeItemQty(${item.id}, 1)">+</button>
                </div>
            </div>
            <div>
                <div class="cart-item-total">$${(item.price * item.quantity).toFixed(2)}</div>
                <button class="cart-item-del" onclick="deleteItem(${item.id})" title="Remove">🗑</button>
            </div>
        `;
        listEl.appendChild(div);
    });

    updateSummary(cart);
}

function updateSummary(cart) {
    const subtotal = cart.reduce((s, i) => s + i.price * i.quantity, 0);
    const shipping  = subtotal > 40 ? 0 : 5;
    const discount  = subtotal * appliedDiscount;
    const total     = subtotal + shipping - discount;

    document.getElementById('summarySubtotal').textContent = `$${subtotal.toFixed(2)}`;
    document.getElementById('summaryShipping').textContent = shipping === 0 ? 'FREE 🎉' : `$${shipping.toFixed(2)}`;
    document.getElementById('summaryDiscount').textContent = `-$${discount.toFixed(2)}`;
    document.getElementById('summaryTotal').textContent    = `$${total.toFixed(2)}`;
}

function changeItemQty(id, delta) {
    window.bloomCart.updateQuantity(id, delta);
    renderCart();
}

function deleteItem(id) {
    window.bloomUI.showConfirm(
        'Remove Item',
        'Are you sure you want to remove this item from your cart?',
        () => {
            window.bloomCart.removeFromCart(id);
            renderCart();
            window.bloomUI.showToast('Item removed from cart.', 'info');
        }
    );
}

async function checkout() {
    const cart = window.bloomCart.getCart();

    if (cart.length === 0) {
        window.bloomUI.showToast('Your cart is empty.', 'error');
        return;
    }

    window.bloomUI.showLoading('Saving your order...');

    try {
        const response = await fetch(SITE_URL + '/checkout.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ cart })
        });

        const result = await response.json();

        window.bloomUI.hideLoading();

        if (result.success) {
            window.bloomUI.showToast(`🌸 Order #${result.order_id} placed successfully!`, 'success', 4000);
            window.bloomCart.saveCart([]);
            renderCart();

            setTimeout(() => {
                window.location.href = SITE_URL + '/index.php';
            }, 2000);
        } else {
            window.bloomUI.showToast(result.message || 'Failed to save order.', 'error');
        }

    } catch (error) {
        window.bloomUI.hideLoading();
        window.bloomUI.showToast('Server error while saving order.', 'error');
        console.error(error);
    }
}

// XSS protection helper
function escapeHtml(text) {
    const div = document.createElement('div');
    div.appendChild(document.createTextNode(text));
    return div.innerHTML;
}

// Initial render
renderCart();

// Listen for storage changes (multi-tab support)
window.addEventListener('storage', renderCart);
</script>