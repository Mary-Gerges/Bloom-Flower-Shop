<?php
// ============================================
// Bloom Flower Shop — User Profile Page
// ============================================
$pageTitle = 'My Profile';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
requireLogin();

$user = getCurrentUser();
$errors = [];
$success = false;

// Fetch full user from DB
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ? LIMIT 1");
$stmt->execute([$user['id']]);
$fullUser = $stmt->fetch();

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $newUsername = trim($_POST['username'] ?? '');
    $newEmail    = trim($_POST['email'] ?? '');

    if (strlen($newUsername) < 3) $errors['username'] = 'Username must be at least 3 characters.';
    if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) $errors['email'] = 'Invalid email address.';

    if (empty($errors)) {
        $checkDup = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $checkDup->execute([$newUsername, $newEmail, $user['id']]);
        if ($checkDup->fetch()) {
            $errors['general'] = 'Username or email is already taken.';
        } else {
            $upd = $pdo->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
            $upd->execute([$newUsername, $newEmail, $user['id']]);
            // Update session
            $_SESSION['username'] = $newUsername;
            $_SESSION['email']    = $newEmail;
            $fullUser['username'] = $newUsername;
            $fullUser['email']    = $newEmail;
            $success = true;
            setFlash('success', 'Profile updated successfully! 🌸');
        }
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current  = trim($_POST['current_password'] ?? '');
    $newPass  = trim($_POST['new_password'] ?? '');
    $confPass = trim($_POST['confirm_new_password'] ?? '');

    if (!password_verify($current, $fullUser['password'])) {
        $errors['current_password'] = 'Current password is incorrect.';
    } elseif (strlen($newPass) < 8) {
        $errors['new_password'] = 'New password must be at least 8 characters.';
    } elseif ($newPass !== $confPass) {
        $errors['confirm_new_password'] = 'Passwords do not match.';
    } else {
        $hashed = password_hash($newPass, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hashed, $user['id']]);
        setFlash('success', 'Password changed successfully!');
        header('Location: ' . SITE_URL . '/profile.php');
        exit();
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>
<main class="page-enter">
<section class="profile-page">
    <div class="container">
        <div class="profile-grid">

            <!-- Sidebar -->
            <div>
                <div class="profile-sidebar">
                    <!-- Avatar with first letter (DOM rendered) -->
                    <div class="profile-avatar" id="profileAvatar">
                        <?php echo strtoupper(substr($fullUser['username'], 0, 1)); ?>
                    </div>
                    <div class="profile-name" id="profileNameDisplay"><?php echo sanitize($fullUser['username']); ?></div>
                    <div class="profile-email" id="profileEmailDisplay"><?php echo sanitize($fullUser['email']); ?></div>
                    <div class="profile-role" style="margin-top:0.75rem;">
                        <span class="badge <?php echo $fullUser['role'] === 'admin' ? 'badge-admin' : 'badge-rose'; ?>">
                            <?php echo ucfirst($fullUser['role']); ?>
                        </span>
                    </div>
                    <div style="margin-top:1.5rem;font-size:0.82rem;color:var(--warm-gray);line-height:1.8;">
                        <div>🗓 Member since <?php echo date('M Y', strtotime($fullUser['created_at'])); ?></div>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="profile-card" style="margin-top:1rem;">
                    <h4 style="font-size:0.82rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--warm-gray);margin-bottom:1rem;">Quick Links</h4>
                    <nav style="display:flex;flex-direction:column;gap:0.5rem;">
                        <a href="<?php echo SITE_URL; ?>/products.php" class="btn btn-outline btn-sm">🌸 Shop Flowers</a>
                        <a href="<?php echo SITE_URL; ?>/cart.php"     class="btn btn-outline btn-sm">🛒 My Cart</a>
                        <?php if (isAdmin()): ?>
                        <a href="<?php echo SITE_URL; ?>/admin/dashboard.php" class="btn btn-sage btn-sm">⚙️ Admin Panel</a>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>/logout.php" class="btn btn-danger btn-sm">Logout</a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div>
                <?php if (!empty($errors['general'])): ?>
                <div class="flash-msg flash-error" style="position:relative;top:auto;right:auto;margin-bottom:1.5rem;">
                    <span><?php echo sanitize($errors['general']); ?></span>
                </div>
                <?php endif; ?>

                <!-- Profile Info Form -->
                <div class="profile-card">
                    <h3 class="profile-card-title">Account Information</h3>
                    <form method="POST" id="profileForm" novalidate>
                        <input type="hidden" name="update_profile" value="1">
                        <div class="form-group">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" class="form-input <?php echo isset($errors['username']) ? 'error' : ''; ?>"
                                id="username" name="username"
                                value="<?php echo sanitize($fullUser['username']); ?>" required>
                            <div class="form-error <?php echo isset($errors['username']) ? 'show' : ''; ?>" id="usernameError">
                                <?php echo sanitize($errors['username'] ?? ''); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email Address</label>
                            <input type="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>"
                                id="email" name="email"
                                value="<?php echo sanitize($fullUser['email']); ?>" required>
                            <div class="form-error <?php echo isset($errors['email']) ? 'show' : ''; ?>" id="emailError">
                                <?php echo sanitize($errors['email'] ?? ''); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-input" value="<?php echo ucfirst($fullUser['role']); ?>" disabled style="opacity:0.6;cursor:not-allowed;">
                        </div>
                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                </div>

                <!-- Change Password Form -->
                <div class="profile-card" style="margin-top:1.5rem;">
                    <h3 class="profile-card-title">Change Password</h3>
                    <form method="POST" id="passwordForm" novalidate>
                        <input type="hidden" name="change_password" value="1">
                        <div class="form-group">
                            <label class="form-label" for="current_password">Current Password</label>
                            <input type="password" class="form-input <?php echo isset($errors['current_password']) ? 'error' : ''; ?>"
                                id="current_password" name="current_password" placeholder="Your current password">
                            <div class="form-error <?php echo isset($errors['current_password']) ? 'show' : ''; ?>" id="current_passwordError">
                                <?php echo sanitize($errors['current_password'] ?? ''); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password</label>
                            <input type="password" class="form-input <?php echo isset($errors['new_password']) ? 'error' : ''; ?>"
                                id="new_password" name="new_password" placeholder="Min. 8 characters">
                            <div class="form-error <?php echo isset($errors['new_password']) ? 'show' : ''; ?>" id="new_passwordError">
                                <?php echo sanitize($errors['new_password'] ?? ''); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="confirm_new_password">Confirm New Password</label>
                            <input type="password" class="form-input <?php echo isset($errors['confirm_new_password']) ? 'error' : ''; ?>"
                                id="confirm_new_password" name="confirm_new_password" placeholder="Repeat new password">
                            <div class="form-error <?php echo isset($errors['confirm_new_password']) ? 'show' : ''; ?>" id="confirm_new_passwordError">
                                <?php echo sanitize($errors['confirm_new_password'] ?? ''); ?>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
</main>
<?php include __DIR__ . '/footer.php'; ?>

<script>
// Dynamically update profile display on input change (DOM manipulation)
document.getElementById('username')?.addEventListener('input', function() {
    const nameDisplay   = document.getElementById('profileNameDisplay');
    const avatarDisplay = document.getElementById('profileAvatar');
    if (nameDisplay)   nameDisplay.textContent   = this.value || 'Username';
    if (avatarDisplay) avatarDisplay.textContent = (this.value || 'U').charAt(0).toUpperCase();
});
document.getElementById('email')?.addEventListener('input', function() {
    const emailDisplay = document.getElementById('profileEmailDisplay');
    if (emailDisplay) emailDisplay.textContent = this.value || 'Email';
});

// Profile form validation
document.getElementById('profileForm')?.addEventListener('submit', function(e) {
    let valid = true;
    valid = window.bloomUI.validateField(document.getElementById('username'), { required: true, minLen: 3 }) && valid;
    valid = window.bloomUI.validateField(document.getElementById('email'), { required: true, email: true }) && valid;
    if (!valid) e.preventDefault();
});

// Password form validation
document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    let valid = true;
    valid = window.bloomUI.validateField(document.getElementById('current_password'), { required: true }) && valid;
    valid = window.bloomUI.validateField(document.getElementById('new_password'), { required: true, minLen: 8 }) && valid;
    valid = window.bloomUI.validateField(document.getElementById('confirm_new_password'), { required: true, match: 'new_password' }) && valid;
    if (!valid) e.preventDefault();
});
</script>