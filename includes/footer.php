        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-links">
                <a href="about.php">About Us</a>
                <a href="programs.php">Programs</a>
                <a href="contact.php">Contact</a>
                <a href="privacy.php">Privacy Policy</a>
                <a href="terms.php">Terms & Conditions</a>
            </div>
            <div class="footer-info">
                <p>&copy; <?php echo date('Y'); ?> Buyunic Technologies. All rights reserved.</p>
                <p>Plot 28, North Road, Northern City Division, Mbale City</p>
                <p>WhatsApp: +256 207 901 434 | Email: info@buyunic.ug</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="assets/js/main.js"></script>
    
    <!-- Additional JavaScript -->
    <?php if (isset($additional_js)): ?>
        <?php foreach ($additional_js as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Inline JavaScript -->
    <?php if (isset($inline_js)): ?>
        <script><?php echo $inline_js; ?></script>
    <?php endif; ?>
</body>
</html>