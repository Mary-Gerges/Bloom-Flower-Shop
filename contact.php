<?php
// ============================================
// Bloom Flower Shop — Contact Page
// ============================================
$pageTitle = 'Contact Us';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');

    if (empty($name))    $errors['name']    = 'Name is required.';
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors['email'] = 'Valid email required.';
    if (empty($subject)) $errors['subject'] = 'Subject is required.';
    if (strlen($message) < 10) $errors['message'] = 'Message must be at least 10 characters.';

    if (empty($errors)) {
        // In a real project, you would send an email here using PHPMailer or mail()
        $success = true;
        setFlash('success', 'Message sent successfully! We\'ll get back to you within 24 hours 🌸');
    }
}
?>
<?php include __DIR__ . '/header.php'; ?>
<main class="page-enter">

<div class="page-hero">
    <span class="section-tag">Get In Touch</span>
    <h1 class="page-hero-title">Contact <em>Us</em></h1>
    <p class="page-hero-sub">We'd love to hear from you — reach out anytime</p>
</div>

<section class="contact-page">
    <div class="container">
        <div class="contact-grid">
            <!-- Contact Info -->
            <div class="contact-info">
                <h2 class="contact-info-title">Let's Talk <em style="font-style:italic;color:var(--rose);">Flowers</em></h2>
                <p class="contact-info-text">
                    Whether you're planning a wedding, ordering a birthday bouquet, or just have a question — our team is here to help. We respond within 24 hours.
                </p>

                <div class="contact-item">
                    <div class="contact-icon">📍</div>
                    <div class="contact-item-text">
                        <strong>Our Store</strong>
                        <span>123 Garden Lane, Cairo, Egypt</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">📞</div>
                    <div class="contact-item-text">
                        <strong>Phone</strong>
                        <span>+20 100 000 0000</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">✉️</div>
                    <div class="contact-item-text">
                        <strong>Email</strong>
                        <span>hello@bloomflowers.com</span>
                    </div>
                </div>
                <div class="contact-item">
                    <div class="contact-icon">🕐</div>
                    <div class="contact-item-text">
                        <strong>Working Hours</strong>
                        <span>Monday – Saturday: 9am – 8pm</span>
                    </div>
                </div>

                <!-- Social links -->
                <div style="margin-top:2rem;">
                    <p style="font-size:0.8rem;letter-spacing:0.12em;text-transform:uppercase;color:var(--warm-gray);margin-bottom:1rem;">Follow Us</p>
                    <div style="display:flex;gap:1rem;">
                        <a href="#" style="width:42px;height:42px;border-radius:50%;border:1.5px solid var(--blush);display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:var(--warm-gray);transition:all 0.3s;" onmouseover="this.style.borderColor='var(--rose)';this.style.color='var(--rose)';" onmouseout="this.style.borderColor='var(--blush)';this.style.color='var(--warm-gray)';">IG</a>
                        <a href="#" style="width:42px;height:42px;border-radius:50%;border:1.5px solid var(--blush);display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:var(--warm-gray);transition:all 0.3s;" onmouseover="this.style.borderColor='var(--rose)';this.style.color='var(--rose)';" onmouseout="this.style.borderColor='var(--blush)';this.style.color='var(--warm-gray)';">FB</a>
                        <a href="#" style="width:42px;height:42px;border-radius:50%;border:1.5px solid var(--blush);display:flex;align-items:center;justify-content:center;font-size:0.85rem;color:var(--warm-gray);transition:all 0.3s;" onmouseover="this.style.borderColor='var(--rose)';this.style.color='var(--rose)';" onmouseout="this.style.borderColor='var(--blush)';this.style.color='var(--warm-gray)';">PT</a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrap">
                <?php if ($success): ?>
                <div style="text-align:center;padding:2rem;">
                    <div style="font-size:3rem;margin-bottom:1rem;">🌸</div>
                    <h3 style="font-family:var(--font-display);font-size:1.5rem;margin-bottom:0.5rem;">Message Sent!</h3>
                    <p style="color:var(--warm-gray);">Thank you for reaching out. We'll get back to you within 24 hours.</p>
                    <button onclick="window.location.reload()" class="btn btn-outline" style="margin-top:1.5rem;">Send Another</button>
                </div>
                <?php else: ?>
                <form id="contactForm" method="POST" novalidate>
                    <h3 style="font-family:var(--font-display);font-size:1.5rem;font-weight:600;margin-bottom:1.5rem;">Send a Message</h3>

                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" class="form-input <?php echo isset($errors['name']) ? 'error' : ''; ?>"
                            id="name" name="name"
                            value="<?php echo sanitize($_POST['name'] ?? ''); ?>"
                            placeholder="Your full name">
                        <div class="form-error <?php echo isset($errors['name']) ? 'show' : ''; ?>" id="nameError">
                            <?php echo sanitize($errors['name'] ?? ''); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" class="form-input <?php echo isset($errors['email']) ? 'error' : ''; ?>"
                            id="email" name="email"
                            value="<?php echo sanitize($_POST['email'] ?? ''); ?>"
                            placeholder="your@email.com">
                        <div class="form-error <?php echo isset($errors['email']) ? 'show' : ''; ?>" id="emailError">
                            <?php echo sanitize($errors['email'] ?? ''); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="subject">Subject</label>
                        <select class="form-select <?php echo isset($errors['subject']) ? 'error' : ''; ?>" id="subject" name="subject">
                            <option value="">Select a topic</option>
                            <option value="order"    <?php echo (($_POST['subject'] ?? '') === 'order')    ? 'selected' : ''; ?>>Order Inquiry</option>
                            <option value="delivery" <?php echo (($_POST['subject'] ?? '') === 'delivery') ? 'selected' : ''; ?>>Delivery Question</option>
                            <option value="custom"   <?php echo (($_POST['subject'] ?? '') === 'custom')   ? 'selected' : ''; ?>>Custom Arrangement</option>
                            <option value="feedback" <?php echo (($_POST['subject'] ?? '') === 'feedback') ? 'selected' : ''; ?>>Feedback</option>
                            <option value="other"    <?php echo (($_POST['subject'] ?? '') === 'other')    ? 'selected' : ''; ?>>Other</option>
                        </select>
                        <div class="form-error <?php echo isset($errors['subject']) ? 'show' : ''; ?>" id="subjectError">
                            <?php echo sanitize($errors['subject'] ?? ''); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="message">Message</label>
                        <textarea class="form-textarea <?php echo isset($errors['message']) ? 'error' : ''; ?>"
                            id="message" name="message"
                            placeholder="Tell us how we can help..."><?php echo sanitize($_POST['message'] ?? ''); ?></textarea>
                        <div class="form-error <?php echo isset($errors['message']) ? 'show' : ''; ?>" id="messageError">
                            <?php echo sanitize($errors['message'] ?? ''); ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-full">Send Message 🌸</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
</main>
<?php include __DIR__ . '/footer.php'; ?>

<script>
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
    let valid = true;
    valid = window.bloomUI.validateField(document.getElementById('name'), { required: true, minLen: 2 }) && valid;
    valid = window.bloomUI.validateField(document.getElementById('email'), { required: true, email: true }) && valid;
    valid = window.bloomUI.validateField(document.getElementById('subject'), { required: true }) && valid;
    valid = window.bloomUI.validateField(document.getElementById('message'), { required: true, minLen: 10 }) && valid;
    if (!valid) e.preventDefault();
    else window.bloomUI.showLoading('Sending your message...');
});
</script>