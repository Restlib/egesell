<?php
$pageTitle = 'Ana Sayfa';
require_once 'includes/header.php';

// Öne çıkan ürünleri al
$featuredProducts = getFeaturedProducts(8);

// Kategorileri al
$allCategories = getCategories();
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Mobilya Aksesuarlarında <span>Güvenilir Adres</span></h1>
            <p>Kulplar, menteşeler, raylar ve daha fazlası. Kaliteli ürünler, uygun fiyatlarla kapınızda.</p>
            <div class="hero-buttons">
                <a href="urunler.php" class="btn btn-primary">Ürünleri Keşfet</a>
                <a href="<?php echo getWhatsAppLink('Merhaba, Egesel Perakende hakkında bilgi almak istiyorum.'); ?>" 
                   class="btn btn-whatsapp" target="_blank">
                    <i class="fab fa-whatsapp"></i> Hemen Ulaşın
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features">
    <div class="container">
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3>Hızlı Kargo</h3>
                <p>Siparişleriniz en kısa sürede kargoya verilir</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3>Güvenli Alışveriş</h3>
                <p>Kaliteli ürünler, güvenilir hizmet</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <h3>WhatsApp Sipariş</h3>
                <p>WhatsApp üzerinden kolay sipariş verin</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <h3>Uygun Fiyat</h3>
                <p>En iyi fiyat garantisi sunuyoruz</p>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="categories-section">
    <div class="container">
        <div class="section-header">
            <h2>Kategoriler</h2>
            <p>İhtiyacınız olan ürünleri kategorilere göre keşfedin</p>
        </div>
        <div class="categories-grid">
            <?php foreach ($allCategories as $category): ?>
                <a href="urunler.php?kategori=<?php echo e($category['slug']); ?>" class="category-card">
                    <div class="category-icon">
                        <?php
                        // Kategoriye göre ikon
                        $icons = [
                            'mobilya-kulplari' => 'fa-grip-lines',
                            'menteseler' => 'fa-door-open',
                            'raylar-mekanizmalar' => 'fa-arrows-alt-h',
                            'vidalar-baglanti' => 'fa-screwdriver',
                            'kilit-sistemleri' => 'fa-lock',
                            'ayaklar-tekerlekler' => 'fa-cog'
                        ];
                        $icon = $icons[$category['slug']] ?? 'fa-box';
                        ?>
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <h3><?php echo e($category['name']); ?></h3>
                    <p><?php echo e($category['description']); ?></p>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2>Öne Çıkan Ürünler</h2>
            <p>En çok tercih edilen ürünlerimiz</p>
            <a href="urunler.php" class="btn btn-outline">Tümünü Gör</a>
        </div>
        <div class="products-grid">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card">
                    <?php if ($product['old_price']): ?>
                        <span class="product-badge">İndirimli</span>
                    <?php endif; ?>
                    
                    <a href="urun.php?slug=<?php echo e($product['slug']); ?>" class="product-image">
                        <?php if ($product['image']): ?>
                            <img src="<?php echo SITE_URL . '/' . e($product['image']); ?>" alt="<?php echo e($product['name']); ?>">
                        <?php else: ?>
                            <div class="no-image"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="product-info">
                        <h3><a href="urun.php?slug=<?php echo e($product['slug']); ?>"><?php echo e($product['name']); ?></a></h3>
                        <p class="product-description"><?php echo e($product['short_description']); ?></p>
                        <div class="product-price">
                            <?php if ($product['old_price']): ?>
                                <span class="old-price"><?php echo formatPrice($product['old_price']); ?></span>
                            <?php endif; ?>
                            <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                        </div>
                        <div class="product-actions">
                            <a href="sepet-ekle.php?id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-cart-plus"></i> Sepete Ekle
                            </a>
                            <a href="<?php echo getWhatsAppLink(getProductWhatsAppMessage($product)); ?>" 
                               class="btn btn-sm btn-whatsapp" target="_blank" title="WhatsApp ile İletişim">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <?php if (isLoggedIn()): ?>
                                <button class="btn btn-sm btn-outline favorite-btn" 
                                        data-product-id="<?php echo $product['id']; ?>"
                                        title="<?php echo isProductFavorite($product['id']) ? 'Favorilerden Çıkar' : 'Favorilere Ekle'; ?>">
                                    <i class="<?php echo isProductFavorite($product['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Hemen Sipariş Verin</h2>
            <p>WhatsApp üzerinden hızlı ve kolay sipariş. Sorularınız için 7/24 ulaşabilirsiniz.</p>
            <a href="<?php echo getWhatsAppLink('Merhaba, sipariş vermek istiyorum.'); ?>" 
               class="btn btn-whatsapp btn-lg" target="_blank">
                <i class="fab fa-whatsapp"></i> WhatsApp ile Sipariş Ver
            </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
