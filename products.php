<?php
// ============================================
// Bloom Flower Shop — Products Page
// ============================================
$pageTitle = 'All Flowers';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Get filters from URL
$category  = trim($_GET['category'] ?? '');
$search    = trim($_GET['search'] ?? '');
$sort      = trim($_GET['sort'] ?? '');

// Build query with optional filters (prepared statements)
$where = [];
$params = [];
if (!empty($category)) { $where[] = "category = ?"; $params[] = $category; }
if (!empty($search))   { $where[] = "(flower_name LIKE ? OR description LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }

$whereSQL = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$orderBy = match($sort) {
    'price-asc'  => 'ORDER BY price ASC',
    'price-desc' => 'ORDER BY price DESC',
    'name-asc'   => 'ORDER BY flower_name ASC',
    'newest'     => 'ORDER BY created_at DESC',
    default      => 'ORDER BY id ASC',
};

$stmt = $pdo->prepare("SELECT * FROM flowers $whereSQL $orderBy");
$stmt->execute($params);
$flowers = $stmt->fetchAll();

// Get all categories for filter
$cats = $pdo->query("SELECT DISTINCT category FROM flowers ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
?>
<?php include __DIR__ . '/header.php'; ?>
<main class="page-enter">

<!-- Page Hero -->
<div class="page-hero">
    <span class="section-tag">Our Garden</span>
    <h1 class="page-hero-title">Fresh <em>Blooms</em></h1>
    <p class="page-hero-sub"><?php echo count($flowers); ?> varieties available — handpicked and ready to delight</p>
</div>

<section class="section" style="padding-top: 3rem;">
    <div class="container">

        <!-- Filter Bar -->
        <div class="filter-bar">
            <div class="search-input-wrap">
                <span class="search-icon">🔍</span>
                <input type="text" id="productSearch" placeholder="Search flowers..."
                    value="<?php echo sanitize($search); ?>">
            </div>
            <select class="filter-select" id="categoryFilter">
                <option value="">All Categories</option>
                <?php foreach ($cats as $cat): ?>
                <option value="<?php echo sanitize($cat); ?>"
                    <?php echo ($category === $cat) ? 'selected' : ''; ?>>
                    <?php echo sanitize($cat); ?>
                </option>
                <?php endforeach; ?>
            </select>
            <select class="filter-select" id="sortFilter">
                <option value="">Sort By</option>
                <option value="price-asc"  <?php echo ($sort === 'price-asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="price-desc" <?php echo ($sort === 'price-desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="name-asc"   <?php echo ($sort === 'name-asc') ? 'selected' : ''; ?>>Name A–Z</option>
                <option value="newest"     <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Newest First</option>
            </select>
        </div>

        <!-- Products Grid -->
        <div class="products-grid" id="productsContainer">
            <?php if (empty($flowers)): ?>
            <div class="empty-state" style="grid-column:1/-1;">
                <div class="empty-icon">🌿</div>
                <h3 class="empty-title">No flowers found</h3>
                <p class="empty-text">Try a different search term or category filter.</p>
                <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline" style="margin-top:1.5rem;">Clear Filters</a>
            </div>
            <?php else: ?>
                <?php foreach ($flowers as $flower): ?>
                <div class="product-card"
                    data-id="<?php echo $flower['id']; ?>"
                    data-name="<?php echo strtolower(sanitize($flower['flower_name'])); ?>"
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
                        <button class="btn-wish" data-id="<?php echo $flower['id']; ?>" data-name="<?php echo addslashes(sanitize($flower['flower_name'])); ?>" data-tooltip="Wishlist" style="position:absolute;top:0.75rem;right:0.75rem;z-index:2;">♡</button>
                        <div class="card-actions-hover">
                            <a href="<?php echo SITE_URL; ?>/product-detail.php?id=<?php echo $flower['id']; ?>"
                               class="btn btn-outline btn-sm" style="color:#fff;border-color:rgba(255,255,255,0.8);">View</a>
                            <?php if (isLoggedIn()): ?>
                            <button class="btn btn-primary btn-sm"
                                onclick="window.bloomCart.addToCart(<?php echo $flower['id']; ?>, '<?php echo addslashes(sanitize($flower['flower_name'])); ?>', <?php echo $flower['price']; ?>, '<?php echo sanitize($flower['category']); ?>')">
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

    </div>
</section>
</main>
<?php include __DIR__ . '/footer.php'; ?>