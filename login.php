<?php
// ============================================
// Bloom Flower Shop — Login Page
// ============================================
$pageTitle = 'Login';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: ' . SITE_URL . '/index.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic validation
    if (empty($email)) $errors['email'] = 'Email is required.';
    if (empty($password)) $errors['password'] = 'Password is required.';

    if (empty($errors)) {
        // Fetch user by email (prepared statement — SQL injection protection)
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            setFlash('success', 'Welcome back, ' . $user['username'] . '! 🌸');

            // Redirect admins to dashboard
            if ($user['role'] === 'admin') {
                header('Location: ' . SITE_URL . '/admin/dashboard.php');
            } else {
                header('Location: ' . SITE_URL . '/index.php');
            }
            exit();
        } else {
            $errors['general'] = 'Invalid email or password. Please try again.';
        }
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>
<main class="page-enter">
<div class="form-page">
    <div class="form-card">
        <div class="form-header">
            <div class="form-logo">Bloom</div>
            <h2 class="form-title">Welcome Back</h2>
            <p class="form-subtitle">Sign in to your account to continue shopping</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="flash-msg flash-error" style="position:relative;top:auto;right:auto;margin-bottom:1.5rem;">
            <span><?php echo sanitize($errors['general']); ?></span>
        </div>
        <?php endif; ?>

        <form id="loginForm" method="POST" action="" novalidate>
            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>"
                    id="email" name="email"
                    value="<?php echo sanitize($_POST['email'] ?? ''); ?>"
                    placeholder="your@email.com" required>
                <div class="form-error <?php echo isset($errors['email']) ? 'show' : ''; ?>" id="emailError">
                    <?php echo sanitize($errors['email'] ?? ''); ?>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-input <?php echo isset($errors['password']) ? 'error' : ''; ?>"
                    id="password" name="password"
                    placeholder="Your password" required>
                <div class="form-error <?php echo isset($errors['password']) ? 'show' : ''; ?>" id="passwordError">
                    <?php echo sanitize($errors['password'] ?? ''); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full" id="loginBtn">
                Sign In
            </button>
        </form>

        <div class="form-divider"><span>or</span></div>
        <div class="form-footer">
            Don't have an account? <a href="<?php echo SITE_URL; ?>/register.php">Create one</a>
        </div>
        
    </div>
</div>
</main>
<?php include __DIR__ . '/footer.php'; ?>

<script>
// Client-side login form validation
document.getElementById('loginForm')?.addEventListener('submit', function(e) {
    let valid = true;
    valid = window.bloomUI.validateField(
        document.getElementById('email'),
        { required: true, email: true }
    ) && valid;
    valid = window.bloomUI.validateField(
        document.getElementById('password'),
        { required: true, minLen: 6 }
    ) && valid;
    if (!valid) e.preventDefault();
    else {
        window.bloomUI.showLoading('Signing you in...');
        // BOM: setTimeout to show loading effect
        setTimeout(() => {}, 1500);
    }
});
</script>