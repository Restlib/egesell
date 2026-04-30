    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Company Info -->
                <div class="footer-section">
                    <div class="footer-logo">
                        <span class="logo-text">EGESEL</span>
                        <span class="logo-subtext">PERAKENDE</span>
                    </div>
                    <p>Mobilya aksesuarları ve hırdavat ürünlerinde güvenilir adresiniz. Kaliteli ürünler, uygun fiyatlar ve hızlı teslimat.</p>
                    <div class="social-links">
                        <a href="#" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="<?php echo getWhatsAppLink(); ?>" target="_blank" title="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="footer-section">
                    <h4>Hızlı Linkler</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Ana Sayfa</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/urunler.php">Tüm Ürünler</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/hakkimizda.php">Hakkımızda</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/iletisim.php">İletişim</a></li>
                    </ul>
                </div>

                <!-- Categories -->
                <div class="footer-section">
                    <h4>Kategoriler</h4>
                    <ul>
                        <?php foreach (array_slice($categories, 0, 6) as $category): ?>
                            <li>
                                <a href="<?php echo SITE_URL; ?>/urunler.php?kategori=<?php echo e($category['slug']); ?>">
                                    <?php echo e($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

                <!-- Contact Info -->
                <div class="footer-section">
                    <h4>İletişim</h4>
                    <ul class="contact-list">
                        <li>
                            <i class="fas fa-phone"></i>
                            <a href="tel:+905318741028">+90 531 874 10 28</a>
                        </li>
                        <li>
                            <i class="fab fa-whatsapp"></i>
                            <a href="<?php echo getWhatsAppLink(); ?>" target="_blank">WhatsApp ile Ulaşın</a>
                        </li>
                        <li>
                            <i class="fas fa-envelope"></i>
                            <a href="mailto:info@egeselperakende.com">info@egeselperakende.com</a>
                        </li>
                        <li>
                            <i class="fas fa-map-marker-alt"></i>
                            <span>Ege Bölgesi, Türkiye</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> Egesel Perakende. Tüm hakları saklıdır.</p>
            </div>
        </div>
    </footer>

    <!-- WhatsApp Float Button -->
    <a href="<?php echo getWhatsAppLink('Merhaba, Egesel Perakende hakkında bilgi almak istiyorum.'); ?>" 
       class="whatsapp-float" target="_blank" title="WhatsApp ile İletişim">
        <i class="fab fa-whatsapp"></i>
    </a>

    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
</body>
</html>
