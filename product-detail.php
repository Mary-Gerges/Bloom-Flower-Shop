<?php
// ============================================
// Bloom Flower Shop — Product Detail Page
// ============================================
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header('Location: ' . SITE_URL . '/products.php'); exit(); }

$stmt = $pdo->prepare("SELECT * FROM flowers WHERE id = ? LIMIT 1");
$stmt->execute([$id]);
$flower = $stmt->fetch();

if (!$flower) { header('Location: ' . SITE_URL . '/products.php'); exit(); }

$pageTitle = $flower['flower_name'];

// Related flowers (same category)
$rel = $pdo->prepare("SELECT * FROM flowers WHERE category = ? AND id != ? LIMIT 4");
$rel->execute([$flower['category'], $id]);
$related = $rel->fetchAll();
?>
<?php include __DIR__ . '/header.php'; ?>
<main class="page-enter">
<section class="product-detail">
    <div class="container">

        <!-- Breadcrumb -->
        <nav style="font-size:0.85rem;color:var(--warm-gray);margin-bottom:2rem;">
            <a href="<?php echo SITE_URL; ?>/index.php" style="color:var(--warm-gray);">Home</a>
            &nbsp;/&nbsp;
            <a href="<?php echo SITE_URL; ?>/products.php" style="color:var(--warm-gray);">Flowers</a>
            &nbsp;/&nbsp;
            <span style="color:var(--charcoal);"><?php echo sanitize($flower['flower_name']); ?></span>
        </nav>

        <div class="product-detail-grid">
            <!-- Image -->
            <div class="detail-img-wrap">
                <?php
                $imgPath = __DIR__ . '/uploads/flowers/' . $flower['image'];
                if (file_exists($imgPath) && !empty($flower['image']) && $flower['image'] !== 'default.jpg'): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/flowers/<?php echo sanitize($flower['image']); ?>?v=1"
                         alt="<?php echo sanitize($flower['flower_name']); ?>">
                <?php else: ?>
                    🌸
                <?php endif; ?>
            </div>

            <!-- Info -->
            <div class="detail-info">
                <div class="detail-category"><?php echo sanitize($flower['category']); ?></div>
                <h1 class="detail-title"><?php echo sanitize($flower['flower_name']); ?></h1>
                <div class="detail-price">$<?php echo number_format($flower['price'], 2); ?></div>
                <p class="detail-desc"><?php echo nl2br(sanitize($flower['description'])); ?></p>

                <!-- Highlights -->
                <div style="display:flex;flex-wrap:wrap;gap:0.75rem;margin-bottom:1.75rem;">
                    <span style="padding:0.4rem 1rem;background:var(--cream);border-radius:50px;font-size:0.82rem;color:var(--warm-gray);">🌿 Fresh Cut</span>
                    <span style="padding:0.4rem 1rem;background:var(--cream);border-radius:50px;font-size:0.82rem;color:var(--warm-gray);">📦 Free Packaging</span>
                    <span style="padding:0.4rem 1rem;background:var(--cream);border-radius:50px;font-size:0.82rem;color:var(--warm-gray);">🚚 Same-Day Delivery</span>
                </div>

                <!-- Quantity selector -->
                <div class="qty-selector">
                    <label>Quantity</label>
                    <div class="qty-wrap">
                        <button class="qty-btn" id="qtyMinus" onclick="changeQty(-1)">−</button>
                        <span class="qty-num" id="qtyDisplay">1</span>
                        <button class="qty-btn" id="qtyPlus" onclick="changeQty(1)">+</button>
                    </div>
                </div>

                <!-- Add to Cart Button -->
                <div style="display:flex;gap:1rem;flex-wrap:wrap;">
                    <?php if (isLoggedIn()): ?>
                    <button class="btn btn-primary btn-lg" id="addToCartBtn"
                        onclick="addToCartQty()">
                        🛒 Add to Cart
                    </button>
                    <?php else: ?>
                    <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary btn-lg">
                        Login to Add to Cart
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline btn-lg">
                        ← Back to Shop
                    </a>
                </div>

                <!-- In stock indicator -->
                <p style="margin-top:1.25rem;font-size:0.85rem;color:var(--dark-sage);">
                    ✓ In Stock — <?php echo (int)$flower['stock']; ?> available
                </p>
            </div>
        </div>

        <!-- Related Products -->
        <?php if (!empty($related)): ?>
        <div style="margin-top:5rem;">
            <div class="section-header" style="margin-bottom:2rem;">
                <span class="section-tag">You May Also Like</span>
                <h2 class="section-title">More <em><?php echo sanitize($flower['category']); ?></em></h2>
            </div>
            <div class="products-grid">
                <?php foreach ($related as $r): ?>
                <div class="product-card"
                    data-name="<?php echo strtolower(sanitize($r['flower_name'])); ?>"
                    data-category="<?php echo sanitize($r['category']); ?>"
                    data-price="<?php echo $r['price']; ?>">
                    <div class="card-img-wrap">
                        <div class="card-emoji">🌸</div>
                        <span class="card-badge"><?php echo sanitize($r['category']); ?></span>
                    </div>
                    <div class="card-body">
                        <div class="card-category"><?php echo sanitize($r['category']); ?></div>
                        <h3 class="card-title"><?php echo sanitize($r['flower_name']); ?></h3>
                        <div class="card-footer">
                            <div class="card-price">$<?php echo number_format($r['price'], 2); ?></div>
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $r['id']; ?>"
                               class="btn btn-outline btn-sm">View</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</section>
</main>
<?php include __DIR__ . '/footer.php'; ?>

<script>
let qty = 1;
const flowerId   = <?php echo $flower['id']; ?>;
const flowerName = '<?php echo addslashes(sanitize($flower['flower_name'])); ?>';
const flowerPrice = <?php echo $flower['price']; ?>;
const flowerCat   = '<?php echo sanitize($flower['category']); ?>';

function changeQty(delta) {
    qty = Math.max(1, qty + delta);
    document.getElementById('qtyDisplay').textContent = qty;
}

function addToCartQty() {
    const cart = window.bloomCart.getCart();
    const numId = parseInt(flowerId, 10);
    const existing = cart.find(x => x.id === numId);
    if (existing) {
        existing.quantity += qty;
        window.bloomCart.saveCart(cart);
    } else {
        cart.push({ id: numId, name: flowerName, price: parseFloat(flowerPrice), category: flowerCat, quantity: qty });
        window.bloomCart.saveCart(cart);
    }
    window.bloomUI.showToast(`🌸 Added ${qty}x ${flowerName} to cart!`, 'success');
}
</script>