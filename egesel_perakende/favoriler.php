<?php
$pageTitle = 'Favorilerim';
require_once 'includes/header.php';

requireLogin();

// Favorileri al
$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.name as category_name FROM favorites f 
                      JOIN products p ON f.product_id = p.id 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE f.user_id = ? AND p.is_active = 1 
                      ORDER BY f.created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$favorites = $stmt->fetchAll();
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Favorilerim</h1>
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
            <span>/</span>
            <span>Favorilerim</span>
        </nav>
    </div>
</section>

<section class="favorites-section">
    <div class="container">
        <?php if (empty($favorites)): ?>
            <div class="empty-favorites">
                <i class="fas fa-heart"></i>
                <h3>Henüz Favoriniz Yok</h3>
                <p>Beğendiğiniz ürünleri favorilere ekleyerek daha sonra kolayca ulaşabilirsiniz.</p>
                <a href="urunler.php" class="btn btn-primary">Ürünleri Keşfet</a>
            </div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($favorites as $product): ?>
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
                            <span class="product-category"><?php echo e($product['category_name']); ?></span>
                            <h3><a href="urun.php?slug=<?php echo e($product['slug']); ?>"><?php echo e($product['name']); ?></a></h3>
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
                                   class="btn btn-sm btn-whatsapp" target="_blank">
                                    <i class="fab fa-whatsapp"></i>
                                </a>
                                <button class="btn btn-sm btn-danger favorite-btn" data-product-id="<?php echo $product['id']; ?>" title="Favorilerden Çıkar">
                                    <i class="fas fa-heart-broken"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
