<!-- ===== FOOTER ===== -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-brand">
            <div class="footer-logo">Bloom</div>
            <p>Where every petal tells a story. We bring nature's finest blooms to your doorstep with care and elegance.</p>
            <div class="footer-social">
                <a href="#" aria-label="Instagram">IG</a>
                <a href="#" aria-label="Facebook">FB</a>
                <a href="#" aria-label="Pinterest">PT</a>
            </div>
        </div>
        <div class="footer-links">
            <h4>Shop</h4>
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/products.php">All Flowers</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products.php?category=Roses">Roses</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products.php?category=Lilies">Lilies</a></li>
                <li><a href="<?php echo SITE_URL; ?>/products.php?category=Orchids">Orchids</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Help</h4>
            <ul>
                <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact Us</a></li>
                <li><a href="#">Shipping Info</a></li>
                <li><a href="#">Returns Policy</a></li>
                <li><a href="#">FAQ</a></li>
            </ul>
        </div>
        <div class="footer-links">
            <h4>Contact</h4>
            <ul>
                <li>📍 123 Garden Lane, Cairo</li>
                <li>📞 +20 100 000 0000</li>
                <li>✉ hello@bloomflowers.com</li>
                <li>🕐 Mon–Sat 9am–8pm</li>
            </ul>
        </div>
    </div>
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Bloom Flower Shop. All rights reserved. | Crafted with NEXORA Team🌸</p>
    </div>
</footer>

<!-- ===== TOAST CONTAINER ===== -->
<div id="toastContainer" class="toast-container"></div>

<!-- ===== SCRIPTS ===== -->
<script src="<?php echo SITE_URL; ?>/main.js"></script>
<?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>