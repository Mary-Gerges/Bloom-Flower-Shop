<?php
// ============================================
// Bloom Flower Shop - Header Partial
// ============================================
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$currentUser = getCurrentUser();
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? sanitize($pageTitle) . ' — Bloom' : 'Bloom Flower Shop'; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/main.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/nav.css">
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
<?php
// Active nav detection
$currentPage = basename($_SERVER['PHP_SELF']);
function navActive($page) {
    global $currentPage;
    return $currentPage === $page ? ' active' : '';
}
?>
</head>
<body>

<!-- Flash Message -->
<?php if ($flash): ?>
<div class="flash-msg flash-<?php echo $flash['type']; ?>" id="flashMsg">
    <span><?php echo sanitize($flash['message']); ?></span>
    <button onclick="this.parentElement.remove()">✕</button>
</div>
<?php endif; ?>

<!-- ===== NAVBAR ===== -->
<nav class="navbar" id="navbar">
    <div class="nav-container">
        <a href="<?php echo SITE_URL; ?>/index.php" class="nav-logo">
            <span class="logo-bloom">Bloom</span>
            <span class="logo-sub">Flower Shop</span>
        </a>

        <ul class="nav-links" id="navLinks">
            <li><a href="<?php echo SITE_URL; ?>/index.php" class="nav-link<?php echo navActive('index.php'); ?>">Home</a></li>
            <li><a href="<?php echo SITE_URL; ?>/products.php" class="nav-link<?php echo navActive('products.php'); ?>">Flowers</a></li>
            <li><a href="<?php echo SITE_URL; ?>/contact.php" class="nav-link<?php echo navActive('contact.php'); ?>">Contact</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="<?php echo SITE_URL; ?>/cart.php" class="nav-link nav-cart<?php echo navActive('cart.php'); ?>">
                    Cart <span class="cart-badge" id="cartBadge">0</span>
                </a></li>
                <li class="nav-dropdown">
                    <a href="#" class="nav-link nav-user-btn" aria-haspopup="true" aria-expanded="false">
                        <?php echo sanitize($currentUser['username']); ?> ▾
                    </a>
                    <ul class="dropdown-menu" role="menu">
                        <li role="none"><a href="<?php echo SITE_URL; ?>/profile.php" role="menuitem">👤 My Profile</a></li>
                        <?php if (isAdmin()): ?>
                        <li role="none"><a href="<?php echo SITE_URL; ?>/dashboard.php" role="menuitem">⚙️ Admin Panel</a></li>
                        <?php endif; ?>
                        <li role="none"><a href="<?php echo SITE_URL; ?>/logout.php" role="menuitem">↩ Logout</a></li>
                    </ul>
                </li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/login.php" class="nav-link<?php echo navActive('login.php'); ?>">Login</a></li>
                <li><a href="<?php echo SITE_URL; ?>/register.php" class="btn-nav-cta<?php echo navActive('register.php') ? ' active' : ''; ?>">Register</a></li>
            <?php endif; ?>
        </ul>

        <button class="nav-hamburger" id="hamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>
</nav>
<!-- End Navbar -->