<?php
// ============================================
// Bloom Flower Shop - Database Configuration
// ============================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'flower_shop');
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('SITE_URL', $basePath === '/' ? '' : $basePath);
define('SITE_NAME', 'Bloom Flower Shop');

// Create PDO connection with error handling
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    // Show friendly error message
    die('<div style="font-family:sans-serif;padding:40px;text-align:center;">
        <h2 style="color:#e74c3c;">Database Connection Error</h2>
        <p>Could not connect to the database. Please make sure XAMPP MySQL is running and the database <strong>flower_shop</strong> has been imported.</p>
        <p style="color:#888;font-size:13px;">' . $e->getMessage() . '</p>
    </div>');
}
?>