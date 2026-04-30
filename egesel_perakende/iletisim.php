<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'İletişim';
include 'includes/header.php';

// Site ayarlarını al
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (PDOException $e) {
    // Varsayılan değerler kullan
}

$whatsapp = $settings['whatsapp'] ?? '05318741028';
$email = $settings['email'] ?? 'info@egeselperakende.com';
$address = $settings['address'] ?? 'İstanbul, Türkiye';
?>

<main class="main-content">
    <section class="page-hero">
        <div class="container">
            <h1>İletişim</h1>
            <p>Bizimle iletişime geçmek için aşağıdaki kanalları kullanabilirsiniz</p>
        </div>
    </section>
    
    <section class="contact-content">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-info">
                    <h2>İletişim Bilgileri</h2>
                    <p>Sorularınız veya siparişleriniz için WhatsApp üzerinden bize ulaşabilirsiniz. En kısa sürede size geri dönüş yapacağız.</p>
                    
                    <div class="contact-items">
                        <div class="contact-item">
                            <div class="contact-icon whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="contact-details">
                                <h3>WhatsApp</h3>
                                <p><?php echo formatPhone($whatsapp); ?></p>
                                <a href="https://wa.me/90<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>?text=Merhaba, bilgi almak istiyorum." target="_blank" class="contact-link">
                                    Mesaj Gönder <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon email">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h3>E-posta</h3>
                                <p><?php echo htmlspecialchars($email); ?></p>
                                <a href="mailto:<?php echo htmlspecialchars($email); ?>" class="contact-link">
                                    E-posta Gönder <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                        
                        <div class="contact-item">
                            <div class="contact-icon location">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h3>Adres</h3>
                                <p><?php echo htmlspecialchars($address); ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="working-hours">
                        <h3><i class="fas fa-clock"></i> Çalışma Saatleri</h3>
                        <ul>
                            <li><span>Pazartesi - Cumartesi</span> <span>09:00 - 19:00</span></li>
                            <li><span>Pazar</span> <span>Kapalı</span></li>
                        </ul>
                    </div>
                </div>
                
                <div class="contact-whatsapp-card">
                    <div class="whatsapp-card-content">
                        <div class="whatsapp-icon-large">
                            <i class="fab fa-whatsapp"></i>
                        </div>
                        <h2>Hızlı İletişim</h2>
                        <p>En hızlı yanıt için WhatsApp üzerinden bize ulaşın. Ürünler hakkında bilgi alabilir, sipariş verebilirsiniz.</p>
                        <a href="https://wa.me/90<?php echo preg_replace('/[^0-9]/', '', $whatsapp); ?>?text=Merhaba, bilgi almak istiyorum." class="btn-whatsapp-large" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp ile Yazın
                        </a>
                        <div class="response-time">
                            <i class="fas fa-bolt"></i> Genellikle 30 dakika içinde yanıt veriyoruz
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
