<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Hakkımızda';
include 'includes/header.php';
?>

<main class="main-content">
    <section class="page-hero">
        <div class="container">
            <h1>Hakkımızda</h1>
            <p>Egesel Perakende olarak kaliteli hırdavat ve mobilya aksesuarları sunuyoruz</p>
        </div>
    </section>
    
    <section class="about-content">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <h2>Biz Kimiz?</h2>
                    <p>Egesel Perakende, yıllardır hırdavat ve mobilya aksesuarları sektöründe hizmet veren güvenilir bir markadır. Müşterilerimize en kaliteli ürünleri en uygun fiyatlarla sunmayı hedefliyoruz.</p>
                    <p>Geniş ürün yelpazemizle ev ve ofis mobilyalarınız için ihtiyaç duyabileceğiniz tüm aksesuarları tek bir çatı altında bulabilirsiniz.</p>
                    
                    <div class="features-grid">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-medal"></i>
                            </div>
                            <h3>Kaliteli Ürünler</h3>
                            <p>Sadece en iyi markaların ürünlerini sunuyoruz</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-truck"></i>
                            </div>
                            <h3>Hızlı Teslimat</h3>
                            <p>Siparişleriniz en kısa sürede hazırlanır</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-headset"></i>
                            </div>
                            <h3>7/24 Destek</h3>
                            <p>WhatsApp üzerinden her zaman yanınızdayız</p>
                        </div>
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-tags"></i>
                            </div>
                            <h3>Uygun Fiyat</h3>
                            <p>Rekabetçi fiyatlarla kaliteli ürünler</p>
                        </div>
                    </div>
                </div>
                
                <div class="about-image">
                    <img src="assets/images/about-image.jpg" alt="Egesel Perakende Mağaza" onerror="this.src='https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=600&h=400&fit=crop'">
                </div>
            </div>
        </div>
    </section>
    
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">1000+</div>
                    <div class="stat-label">Ürün Çeşidi</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">5000+</div>
                    <div class="stat-label">Mutlu Müşteri</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">10+</div>
                    <div class="stat-label">Yıllık Deneyim</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">100%</div>
                    <div class="stat-label">Müşteri Memnuniyeti</div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="cta-section">
        <div class="container">
            <div class="cta-content">
                <h2>Sorularınız mı var?</h2>
                <p>WhatsApp üzerinden bizimle iletişime geçin, size yardımcı olmaktan mutluluk duyarız.</p>
                <a href="https://wa.me/905318741028?text=Merhaba, bilgi almak istiyorum." class="btn-whatsapp-large" target="_blank">
                    <i class="fab fa-whatsapp"></i> WhatsApp ile İletişime Geç
                </a>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
