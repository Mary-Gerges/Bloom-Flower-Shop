<?php
// ============================================
// Bloom Flower Shop — Register Page
// ============================================
$pageTitle = 'Create Account';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

if (isLoggedIn()) { header('Location: ' . SITE_URL . '/index.php'); exit(); }

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username  = trim($_POST['username'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = trim($_POST['password'] ?? '');
    $confirm   = trim($_POST['confirm_password'] ?? '');

    // Validation
    if (empty($username) || strlen($username) < 3)
        $errors['username'] = 'Username must be at least 3 characters.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Please enter a valid email address.';
    if (
        strlen($password) < 8 ||
        !preg_match('/[A-Z]/', $password) ||
        !preg_match('/[a-z]/', $password) ||
        !preg_match('/[0-9]/', $password) ||
        !preg_match('/[^A-Za-z0-9]/', $password)
    )
        $errors['password'] = 'Password must contain uppercase, lowercase, number and special character.';
    if ($password !== $confirm)
        $errors['confirm'] = 'Passwords do not match.';

    if (empty($errors)) {
        // Check if email or username already exists
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ? LIMIT 1");
        $check->execute([$email, $username]);
        if ($check->fetch()) {
            $errors['general'] = 'An account with this email or username already exists.';
        } else {
            // Hash password
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt   = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
            $stmt->execute([$username, $email, $hashed]);

            // Auto-login
            $userId = $pdo->lastInsertId();
            $_SESSION['user_id']  = $userId;
            $_SESSION['username'] = $username;
            $_SESSION['email']    = $email;
            $_SESSION['role']     = 'user';

            setFlash('success', 'Account created successfully! Welcome to Bloom 🌸');
            header('Location: ' . SITE_URL . '/index.php');
            exit();
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
            <h2 class="form-title">Join Bloom</h2>
            <p class="form-subtitle">Create your account and start exploring our garden</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
        <div class="flash-msg flash-error" style="position:relative;top:auto;right:auto;margin-bottom:1.5rem;">
            <span><?php echo sanitize($errors['general']); ?></span>
        </div>
        <?php endif; ?>

        <form id="registerForm" method="POST" action="" novalidate>
            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text" class="form-input <?php echo isset($errors['username']) ? 'error' : ''; ?>"
                    id="username" name="username"
                    value="<?php echo sanitize($_POST['username'] ?? ''); ?>"
                    placeholder="flowerlover" required>
                <div class="form-error <?php echo isset($errors['username']) ? 'show' : ''; ?>" id="usernameError">
                    <?php echo sanitize($errors['username'] ?? ''); ?>
                </div>
            </div>

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
                    placeholder="8+ chars, upper, lower, number & symbol" required>
                <div class="form-error <?php echo isset($errors['password']) ? 'show' : ''; ?>" id="passwordError">
                    <?php echo sanitize($errors['password'] ?? ''); ?>
                </div>
                <!-- Password strength bar (DOM manipulation) -->
                <div id="passwordStrength" style="margin-top:0.5rem;height:4px;border-radius:2px;background:var(--light-gray);overflow:hidden;">
                    <div id="strengthBar" style="height:100%;width:0;transition:all 0.3s;border-radius:2px;"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="confirm_password">Confirm Password</label>
                <input type="password" class="form-input <?php echo isset($errors['confirm']) ? 'error' : ''; ?>"
                    id="confirm_password" name="confirm_password"
                    placeholder="Repeat your password" required>
                <div class="form-error <?php echo isset($errors['confirm']) ? 'show' : ''; ?>" id="confirm_passwordError">
                    <?php echo sanitize($errors['confirm'] ?? ''); ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Create Account</button>
        </form>

        <div class="form-divider"><span>or</span></div>
        <div class="form-footer">
            Already have an account? <a href="<?php echo SITE_URL; ?>/login.php">Sign in</a>
        </div>
    </div>
</div>
</main>
<?php include __DIR__ . '/footer.php'; ?>

<script>
// Password strength meter (DOM manipulation)
document.getElementById('password')?.addEventListener('input', function() {
    const val = this.value;
    const bar = document.getElementById('strengthBar');
    let strength = 0;
    if (val.length >= 8) strength++;
    if (/[A-Z]/.test(val)) strength++;
    if (/[0-9]/.test(val)) strength++;
    if (/[^A-Za-z0-9]/.test(val)) strength++;

    const colors = ['#e74c3c','#e67e22','#f1c40f','#2ecc71'];
    const widths = ['25%','50%','75%','100%'];
    bar.style.width  = widths[strength-1] || '0';
    bar.style.background = colors[strength-1] || 'transparent';
});

// Register form validation
document.getElementById('registerForm')?.addEventListener('submit', function(e) {
    let valid = true;
    valid = window.bloomUI.validateField(
        document.getElementById('username'),
        { required: true, minLen: 3 }
    ) && valid;
    valid = window.bloomUI.validateField(
        document.getElementById('email'),
        { required: true, email: true }
    ) && valid;
    valid = window.bloomUI.validateField(
        document.getElementById('password'),
        { required: true, minLen: 8, strongPassword: true }
    ) && valid;
    valid = window.bloomUI.validateField(
        document.getElementById('confirm_password'),
        { required: true, match: 'password' }
    ) && valid;
    if (!valid) e.preventDefault();
    else window.bloomUI.showLoading('Creating your account...');
});
</script>