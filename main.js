/**
 * Bloom Flower Shop — Main JavaScript
 * Uses: localStorage, sessionStorage, DOM manipulation,
 *       BOM features, animations, cart counter
 */

/* ============================================
   NAVBAR — Enhanced with scroll-hide & smooth toggle
   ============================================ */
const navbar    = document.getElementById('navbar');
const hamburger = document.getElementById('hamburger');
const navLinks  = document.getElementById('navLinks');

// --- Scroll: shadow + smart hide on fast scroll-down ---
let lastScrollY   = 0;
let scrollTicking = false;

window.addEventListener('scroll', () => {
    if (!scrollTicking) {
        requestAnimationFrame(() => {
            const currentY = window.scrollY;

            // Scrolled state (shadow)
            if (currentY > 10) {
                navbar?.classList.add('scrolled');
            } else {
                navbar?.classList.remove('scrolled');
            }

            // Hide on rapid downscroll (>120px from top), show on upscroll
            if (currentY > 120) {
                if (currentY > lastScrollY + 6) {
                    navbar?.classList.add('nav-hidden');
                    // Close mobile menu when nav hides
                    navLinks?.classList.remove('open');
                    hamburger?.classList.remove('active');
                } else if (currentY < lastScrollY - 4) {
                    navbar?.classList.remove('nav-hidden');
                }
            } else {
                navbar?.classList.remove('nav-hidden');
            }

            lastScrollY   = currentY;
            scrollTicking = false;
        });
        scrollTicking = true;
    }
}, { passive: true });

// --- Mobile menu toggle ---
function closeMenu() {
    navLinks?.classList.remove('open');
    hamburger?.classList.remove('active');
    hamburger?.setAttribute('aria-expanded', 'false');
}

function openMenu() {
    navLinks?.classList.add('open');
    hamburger?.classList.add('active');
    hamburger?.setAttribute('aria-expanded', 'true');
}

hamburger?.addEventListener('click', (e) => {
    e.stopPropagation();
    if (navLinks?.classList.contains('open')) {
        closeMenu();
    } else {
        openMenu();
    }
});

// Close on outside click
document.addEventListener('click', (e) => {
    if (
        navLinks?.classList.contains('open') &&
        !navLinks.contains(e.target) &&
        !hamburger?.contains(e.target)
    ) {
        closeMenu();
    }
});

// Close on Escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && navLinks?.classList.contains('open')) {
        closeMenu();
        hamburger?.focus();
    }
});

// Close menu when a nav link is tapped on mobile
navLinks?.querySelectorAll('a:not(.nav-user-btn)').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 768) {
            closeMenu();
        }
    });
});

// Reset menu state on resize to desktop
window.addEventListener('resize', () => {
    if (window.innerWidth > 768) {
        closeMenu();
    }
}, { passive: true });

/* ============================================
   CART COUNTER (localStorage)
   ============================================ */
function updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;
    const cart = getCart();
    const count = cart.reduce((sum, item) => sum + item.quantity, 0);
    badge.textContent = count;
    if (count > 0) {
        badge.classList.add('visible');
    } else {
        badge.classList.remove('visible');
    }
}

function getCart() {
    try {
        return JSON.parse(localStorage.getItem('bloom_cart') || '[]');
    } catch {
        return [];
    }
}

function saveCart(cart) {
    localStorage.setItem('bloom_cart', JSON.stringify(cart));
    updateCartBadge();
}

function addToCart(id, name, price, category) {
    const cart = getCart();
    const numId = parseInt(id, 10);
    const existing = cart.find(item => item.id === numId);
    if (existing) {
        existing.quantity++;
    } else {
        cart.push({ id: numId, name, price: parseFloat(price), category, quantity: 1 });
    }
    saveCart(cart);
    showToast(`🌸 "${name}" added to cart!`, 'success');
}

function removeFromCart(id) {
    const numId = parseInt(id, 10);
    const cart = getCart().filter(item => item.id !== numId);
    saveCart(cart);
}

function updateQuantity(id, delta) {
    const numId = parseInt(id, 10);
    const cart = getCart();
    const item = cart.find(i => i.id === numId);
    if (item) {
        item.quantity = Math.max(1, item.quantity + delta);
        saveCart(cart);
    }
}

// Initialize cart badge on page load
updateCartBadge();

/* ============================================
   TOAST NOTIFICATIONS
   ============================================ */
function showToast(message, type = 'info', duration = 3000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;

    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);

    // Auto remove
    setTimeout(() => {
        toast.classList.add('fade-out');
        setTimeout(() => toast.remove(), 300);
    }, duration);
}

/* ============================================
   CONFIRM MODAL (DOM manipulation)
   ============================================ */
function showConfirm(title, message, onConfirm) {
    // Create modal dynamically using DOM
    const overlay = document.createElement('div');
    overlay.className = 'modal-overlay';
    overlay.innerHTML = `
        <div class="modal">
            <div class="modal-icon">🌺</div>
            <h3 class="modal-title">${title}</h3>
            <p class="modal-text">${message}</p>
            <div class="modal-actions">
                <button class="btn btn-outline btn-sm" id="modalCancel">Cancel</button>
                <button class="btn btn-danger btn-sm" id="modalConfirm">Delete</button>
            </div>
        </div>
    `;
    document.body.appendChild(overlay);

    // Show with animation
    setTimeout(() => overlay.classList.add('active'), 10);

    overlay.querySelector('#modalCancel').addEventListener('click', () => {
        overlay.classList.remove('active');
        setTimeout(() => overlay.remove(), 300);
    });

    overlay.querySelector('#modalConfirm').addEventListener('click', () => {
        overlay.classList.remove('active');
        setTimeout(() => overlay.remove(), 300);
        onConfirm();
    });

    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            overlay.classList.remove('active');
            setTimeout(() => overlay.remove(), 300);
        }
    });
}

/* ============================================
   LOADING OVERLAY
   ============================================ */
function showLoading(message = 'Loading...') {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loadingOverlay';
    overlay.innerHTML = `
        <div class="loader-flower">🌸</div>
        <p class="loader-text">${message}</p>
    `;
    document.body.appendChild(overlay);
}

function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) overlay.remove();
}

/* ============================================
   FORM VALIDATION (DOM manipulation)
   ============================================ */
function validateField(input, rules = {}) {
    const errorEl = document.getElementById(input.id + 'Error');
    let errorMsg = '';

    const val = input.value.trim();

    if (rules.required && !val) {
        errorMsg = rules.requiredMsg || 'This field is required.';
    } else if (rules.minLen && val.length < rules.minLen) {
        errorMsg = `Minimum ${rules.minLen} characters required.`;
    } else if (rules.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(val)) {
        errorMsg = 'Please enter a valid email address.';
    } else if (rules.match && val !== document.getElementById(rules.match)?.value) {
        errorMsg = 'Passwords do not match.';
    } else if (rules.pattern && !rules.pattern.test(val)) {
        errorMsg = rules.patternMsg || 'Invalid format.';
    } else if (rules.min && parseFloat(val) < rules.min) {
        errorMsg = `Minimum value is ${rules.min}.`;
    }

    if (errorMsg) {
        input.classList.add('error');
        if (errorEl) { errorEl.textContent = errorMsg; errorEl.classList.add('show'); }
        return false;
    } else {
        input.classList.remove('error');
        if (errorEl) { errorEl.classList.remove('show'); }
        return true;
    }
}

// Clear validation on input
document.querySelectorAll('.form-input, .form-textarea, .form-select').forEach(input => {
    input.addEventListener('input', () => {
        input.classList.remove('error');
        const errEl = document.getElementById(input.id + 'Error');
        if (errEl) errEl.classList.remove('show');
    });
});

/* ============================================
   SEARCH & FILTER (DOM manipulation)
   ============================================ */
function initProductSearch() {
    const searchInput = document.getElementById('productSearch');
    const categoryFilter = document.getElementById('categoryFilter');
    const sortFilter = document.getElementById('sortFilter');
    const productsContainer = document.getElementById('productsContainer');

    if (!productsContainer) return;

    function filterProducts() {
        const query = searchInput?.value.toLowerCase().trim() || '';
        const category = categoryFilter?.value || '';
        const sort = sortFilter?.value || '';

        const cards = productsContainer.querySelectorAll('.product-card');
        let visible = 0;

        cards.forEach(card => {
            const name     = card.dataset.name?.toLowerCase() || '';
            const cat      = card.dataset.category || '';
            const matchQ   = !query || name.includes(query) || cat.toLowerCase().includes(query);
            const matchC   = !category || cat === category;

            if (matchQ && matchC) {
                card.style.display = '';
                visible++;
                card.style.animation = 'fadeUp 0.4s ease both';
            } else {
                card.style.display = 'none';
            }
        });

        // Show/hide empty state
        let emptyState = productsContainer.querySelector('.empty-state');
        if (visible === 0) {
            if (!emptyState) {
                emptyState = document.createElement('div');
                emptyState.className = 'empty-state';
                emptyState.innerHTML = `
                    <div class="empty-icon">🌿</div>
                    <h3 class="empty-title">No flowers found</h3>
                    <p class="empty-text">Try adjusting your search or filters.</p>
                `;
                productsContainer.appendChild(emptyState);
            }
        } else {
            emptyState?.remove();
        }

        // Sort
        if (sort) {
            const cardsArr = [...productsContainer.querySelectorAll('.product-card:not(.empty-state)')];
            cardsArr.sort((a, b) => {
                const pA = parseFloat(a.dataset.price || 0);
                const pB = parseFloat(b.dataset.price || 0);
                if (sort === 'price-asc')  return pA - pB;
                if (sort === 'price-desc') return pB - pA;
                if (sort === 'name-asc')   return (a.dataset.name || '').localeCompare(b.dataset.name || '');
                return 0;
            });
            cardsArr.forEach(card => productsContainer.appendChild(card));
        }
    }

    searchInput?.addEventListener('input', filterProducts);
    categoryFilter?.addEventListener('change', filterProducts);
    sortFilter?.addEventListener('change', filterProducts);
}
initProductSearch();

/* ============================================
   BOM: Navigator / Browser Info
   ============================================ */
// Store browser info in sessionStorage for analytics
if (typeof navigator !== 'undefined') {
    sessionStorage.setItem('bloom_browser', navigator.userAgent.split(' ').pop() || 'Unknown');
    sessionStorage.setItem('bloom_platform', navigator.platform || 'Unknown');
}

/* ============================================
   ADMIN: Table Search
   ============================================ */
function initAdminSearch() {
    const searchEl = document.getElementById('adminSearch');
    if (!searchEl) return;
    const tbody = document.querySelector('table tbody');
    if (!tbody) return;

    searchEl.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        tbody.querySelectorAll('tr').forEach(row => {
            row.style.display = row.textContent.toLowerCase().includes(q) ? '' : 'none';
        });
    });
}
initAdminSearch();

/* ============================================
   SCROLL REVEAL (Intersection Observer)
   ============================================ */
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.animation = 'fadeUp 0.6s ease both';
            revealObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });

document.querySelectorAll('.feature-card, .category-card, .stat-card').forEach(el => {
    el.style.opacity = '0';
    el.style.animation = 'none';
    revealObserver.observe(el);
});

/* ============================================
   ACTIVE NAV LINK
   ============================================ */
const currentPath = window.location.pathname;
document.querySelectorAll('.nav-link').forEach(link => {
    if (link.href && link.href.includes(currentPath.split('/').pop())) {
        link.classList.add('active');
    }
});

/* ============================================
   AUTO DISMISS FLASH MESSAGE
   ============================================ */
setTimeout(() => {
    const flash = document.getElementById('flashMsg');
    if (flash) {
        flash.style.animation = 'fadeOut 0.4s ease forwards';
        setTimeout(() => flash.remove(), 400);
    }
}, 4000);

/* ============================================
   EXPOSE GLOBALS
   ============================================ */
window.bloomCart = { addToCart, removeFromCart, updateQuantity, getCart, saveCart };
window.bloomUI   = { showToast, showConfirm, showLoading, hideLoading, validateField };
/* ============================================
   SCROLL TO TOP BUTTON
   ============================================ */
(function () {
    const btn = document.createElement('button');
    btn.className = 'scroll-top';
    btn.innerHTML = '↑';
    btn.title = 'Back to top';
    document.body.appendChild(btn);

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            btn.classList.add('visible');
        } else {
            btn.classList.remove('visible');
        }
    });

    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();

/* ============================================
   PASSWORD TOGGLE (show / hide)
   ============================================ */
function initPasswordToggles() {
    document.querySelectorAll('input[type="password"]').forEach(input => {
        // Wrap input if not already wrapped
        if (!input.parentElement.classList.contains('input-wrap')) {
            const wrap = document.createElement('div');
            wrap.className = 'input-wrap';
            input.parentNode.insertBefore(wrap, input);
            wrap.appendChild(input);
        }
        // Add toggle button
        if (!input.parentElement.querySelector('.toggle-pass')) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'toggle-pass';
            btn.innerHTML = '👁';
            btn.setAttribute('aria-label', 'Toggle password visibility');
            btn.addEventListener('click', () => {
                if (input.type === 'password') {
                    input.type = 'text';
                    btn.innerHTML = '🙈';
                } else {
                    input.type = 'password';
                    btn.innerHTML = '👁';
                }
            });
            input.parentElement.appendChild(btn);
        }
    });
}
initPasswordToggles();

/* ============================================
   PRODUCT RESULTS COUNT
   ============================================ */
(function () {
    const container = document.getElementById('productsContainer');
    if (!container) return;

    const countEl = document.createElement('p');
    countEl.className = 'results-count';
    container.parentNode.insertBefore(countEl, container);

    function updateCount() {
        const visible = container.querySelectorAll('.product-card:not([style*="display: none"])').length;
        countEl.innerHTML = `Showing <strong>${visible}</strong> flower${visible !== 1 ? 's' : ''}`;
    }

    // Observe filter changes
    const observer = new MutationObserver(updateCount);
    observer.observe(container, { attributes: true, childList: true, subtree: true });
    updateCount();
})();

/* ============================================
   WISHLIST (localStorage)
   ============================================ */
function getWishlist() {
    try { return JSON.parse(localStorage.getItem('bloom_wish') || '[]'); } catch { return []; }
}
function saveWishlist(list) {
    localStorage.setItem('bloom_wish', JSON.stringify(list));
}
function toggleWish(id, name) {
    const numId = parseInt(id, 10);
    let list = getWishlist();
    const idx = list.findIndex(x => x.id === numId);
    if (idx > -1) {
        list.splice(idx, 1);
        showToast(`💔 "${name}" removed from wishlist`, 'info');
    } else {
        list.push({ id: numId, name });
        showToast(`❤️ "${name}" added to wishlist!`, 'success');
    }
    saveWishlist(list);
    // Update button state
    document.querySelectorAll(`.btn-wish[data-id="${numId}"]`).forEach(btn => {
        btn.classList.toggle('active', idx === -1);
    });
}

// Init wishlist buttons
function initWishButtons() {
    const wish = getWishlist();
    document.querySelectorAll('.btn-wish[data-id]').forEach(btn => {
        const id = parseInt(btn.dataset.id, 10);
        if (wish.find(x => x.id === id)) btn.classList.add('active');
        btn.addEventListener('click', () => toggleWish(btn.dataset.id, btn.dataset.name || ''));
    });
}
initWishButtons();

/* ============================================
   LAZY IMAGE FADE-IN
   ============================================ */
document.querySelectorAll('img[loading="lazy"]').forEach(img => {
    img.style.opacity = '0';
    img.style.transition = 'opacity 0.4s ease';
    img.addEventListener('load', () => { img.style.opacity = '1'; });
    if (img.complete) img.style.opacity = '1';
});

/* ============================================
   KEYBOARD ACCESSIBILITY — close dropdown on Escape
   ============================================ */
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        // Close nav menu
        navLinks?.classList.remove('open');
        // Close modal
        document.querySelectorAll('.modal-overlay.active').forEach(m => {
            m.classList.remove('active');
            setTimeout(() => m.remove(), 300);
        });
    }
});

/* ============================================
   EXPOSE WISHLIST GLOBALLY
   ============================================ */
window.bloomWish = { toggleWish, getWishlist };
