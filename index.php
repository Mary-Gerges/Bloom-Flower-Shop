<?php
// ============================================
// Bloom Flower Shop — Landing / Home Page
// ============================================
$pageTitle = 'Welcome to Bloom';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Fetch featured flowers
$stmt = $pdo->query("SELECT * FROM flowers ORDER BY created_at DESC LIMIT 6");
$flowers = $stmt->fetchAll();

// Count categories
$catStmt = $pdo->query("SELECT category, COUNT(*) as count FROM flowers GROUP BY category");
$categories = $catStmt->fetchAll();
?>
<?php include __DIR__ . '/header.php'; ?>
<main>

<!-- ===== HERO ===== -->
<section class="hero">
    <div class="hero-bg-pattern"></div>
    <div class="hero-decor">🌸</div>
    <div class="hero-container">
        <div class="hero-content" data-aos="fade-up">
            <div class="hero-badge">🌺 Fresh Daily Arrangements</div>
            <h1 class="hero-title">
                Express Love<br>
                Through <em>Flowers</em>
            </h1>
            <p class="hero-subtitle">
                Hand-picked blooms delivered to your door. From intimate bouquets to grand arrangements, every petal is chosen with care and elegance.
            </p>
            <div class="hero-actions">
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg">Shop Now</a>
                <a href="#featured" class="btn btn-outline btn-lg">Explore</a>
            </div>
            <div class="hero-stats">
                <div class="stat-item">
                    <span class="stat-num">500+</span>
                    <span class="stat-label">Happy Customers</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num"><?php echo count($flowers) > 0 ? $pdo->query("SELECT COUNT(*) FROM flowers")->fetchColumn() : '50+'; ?></span>
                    <span class="stat-label">Flower Varieties</span>
                </div>
                <div class="stat-item">
                    <span class="stat-num">100%</span>
                    <span class="stat-label">Fresh Guarantee</span>
                </div>
            </div>
        </div>
        <div class="hero-visual">
            <div class="hero-image-wrap">🌸</div>
            <div class="hero-float-card card-1">
                <div class="float-icon">🌹</div>
                <div class="float-text">
                    <strong>Free Delivery</strong>
                    <span>Orders over $40</span>
                </div>
            </div>
            <div class="hero-float-card card-2">
                <div class="float-icon">⭐</div>
                <div class="float-text">
                    <strong>Top Rated</strong>
                    <span>4.9 / 5 Stars</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ===== FEATURES ===== -->
<section class="section section-sm" style="background: var(--white);">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">🌿</div>
                <h3 class="feature-title">Farm Fresh</h3>
                <p class="feature-text">All flowers sourced directly from local and international farms, ensuring peak freshness on arrival.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🚚</div>
                <h3 class="feature-title">Same-Day Delivery</h3>
                <p class="feature-text">Order before 2pm and receive your blooms the same day. We handle every arrangement with love.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">🎁</div>
                <h3 class="feature-title">Gift Wrapping</h3>
                <p class="feature-text">Elegant packaging and personalized cards included with every order to make moments memorable.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">♻️</div>
                <h3 class="feature-title">Eco-Friendly</h3>
                <p class="feature-text">Sustainably packaged with biodegradable materials. We care for the planet as much as we do for flowers.</p>
            </div>
        </div>
    </div>
</section>

<!-- ===== CATEGORIES ===== -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Browse by Type</span>
            <h2 class="section-title">Our <em>Collections</em></h2>
            <p class="section-subtitle">From classic roses to exotic orchids — find the perfect bloom for every occasion.</p>
        </div>
        <div class="categories-grid">
            <?php
            $allCats = [
                'Roses'      => '🌹',
                'Lilies'     => '🌷',
                'Sunflowers' => '🌻',
                'Orchids'    => '🌺',
                'Peonies'    => '💐',
                'Tulips'     => '🌸',
                'Wildflowers'=> '🌼',
                'Daisies'    => '🌼',
                'Hydrangeas' => '💜',
                'Carnations' => '🏵️',
            ];
            // Build count map
            $countMap = [];
            foreach ($categories as $cat) {
                $countMap[$cat['category']] = $cat['count'];
            }
            foreach ($allCats as $catName => $icon):
                if (!isset($countMap[$catName])) continue;
            ?>
            <a href="<?php echo SITE_URL; ?>/products.php?category=<?php echo urlencode($catName); ?>" class="category-card">
                <span class="category-icon"><?php echo $icon; ?></span>
                <div class="category-name"><?php echo sanitize($catName); ?></div>
                <div class="category-count"><?php echo $countMap[$catName] ?? 0; ?> items</div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== FEATURED PRODUCTS ===== -->
<section class="section" id="featured" style="background: var(--white);">
    <div class="container">
        <div class="section-header">
            <span class="section-tag">Hand-Picked for You</span>
            <h2 class="section-title">Featured <em>Blooms</em></h2>
            <p class="section-subtitle">Our freshest arrivals, loved by thousands of happy customers.</p>
        </div>
        <div class="products-grid" id="productsContainer">
            <?php if (empty($flowers)): ?>
                <div class="empty-state" style="grid-column:1/-1">
                    <div class="empty-icon">🌿</div>
                    <h3 class="empty-title">No flowers yet</h3>
                    <p class="empty-text">Check back soon — our gardens are blooming!</p>
                </div>
            <?php else: ?>
                <?php foreach ($flowers as $flower): ?>
                <div class="product-card"
                    data-id="<?php echo $flower['id']; ?>"
                    data-name="<?php echo sanitize($flower['flower_name']); ?>"
                    data-category="<?php echo sanitize($flower['category']); ?>"
                    data-price="<?php echo $flower['price']; ?>">
                    <div class="card-img-wrap">
                        <?php
                        $imgPath = __DIR__ . '/uploads/flowers/' . $flower['image'];
                        if (file_exists($imgPath) && !empty($flower['image']) && $flower['image'] !== 'default.jpg'):
                        ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/flowers/<?php echo sanitize($flower['image']); ?>?v=1"
                                 alt="<?php echo sanitize($flower['flower_name']); ?>" loading="lazy">
                        <?php else: ?>
                            <div class="card-emoji">🌸</div>
                        <?php endif; ?>
                        <span class="card-badge"><?php echo sanitize($flower['category']); ?></span>
                        <div class="card-actions-hover">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $flower['id']; ?>"
                               class="btn btn-outline btn-sm" style="color:#fff;border-color:#fff;">View</a>
                            <?php if (isLoggedIn()): ?>
                            <button class="btn btn-primary btn-sm"
                                onclick="window.bloomCart.addToCart(<?php echo $flower['id']; ?>, '<?php echo sanitize($flower['flower_name']); ?>', <?php echo $flower['price']; ?>, '<?php echo sanitize($flower['category']); ?>')">
                                Add to Cart
                            </button>
                            <?php else: ?>
                            <a href="<?php echo SITE_URL; ?>/login.php" class="btn btn-primary btn-sm">Add to Cart</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="card-category"><?php echo sanitize($flower['category']); ?></div>
                        <h3 class="card-title"><?php echo sanitize($flower['flower_name']); ?></h3>
                        <p class="card-desc"><?php echo sanitize($flower['description']); ?></p>
                        <div class="card-footer">
                            <div class="card-price">$<?php echo number_format($flower['price'], 2); ?></div>
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $flower['id']; ?>"
                               class="btn btn-outline btn-sm">Details</a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div style="text-align:center; margin-top:3rem;">
            <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-primary btn-lg">View All Flowers</a>
        </div>
    </div>
</section>

<!-- ===== BANNER CTA ===== -->
<section class="section" style="background: linear-gradient(135deg, var(--charcoal), #3d2c2c); color:#fff; text-align:center;">
    <div class="container">
        <p style="font-size:0.8rem;letter-spacing:0.2em;text-transform:uppercase;opacity:0.6;margin-bottom:1rem;">Special Offer</p>
        <h2 style="font-family:var(--font-display);font-size:clamp(2rem,4vw,3.5rem);font-weight:300;margin-bottom:1rem;">
            Send Flowers to Someone<br><em style="color:var(--blush);">You Love Today</em>
        </h2>
        <p style="opacity:0.75;max-width:500px;margin:0 auto 2.5rem;line-height:1.8;">Free delivery on all orders over $40. Use code <strong style="color:var(--blush);">BLOOM20</strong> for 20% off your first order.</p>
        <a href="<?php echo SITE_URL; ?>/products.php" class="btn" style="background:var(--blush);color:var(--charcoal);border-color:var(--blush);font-size:1rem;padding:1rem 2.5rem;">
            Shop Now — Free Delivery
        </a>
    </div>
</section>

</main>
<?php include __DIR__ . '/footer.php'; ?>