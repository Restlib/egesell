<?php
require_once 'includes/header.php';

// Ürün slug'ını al
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    header('Location: urunler.php');
    exit;
}

// Ürünü getir
$db = getDB();
$stmt = $db->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                      FROM products p 
                      LEFT JOIN categories c ON p.category_id = c.id 
                      WHERE p.slug = ? AND p.is_active = 1");
$stmt->execute([$slug]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: urunler.php');
    exit;
}

$pageTitle = $product['name'];

// Görüntülenme sayısını artır
$stmt = $db->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = ?");
$stmt->execute([$product['id']]);

// Benzer ürünleri getir
$stmt = $db->prepare("SELECT * FROM products 
                      WHERE category_id = ? AND id != ? AND is_active = 1 
                      ORDER BY RAND() LIMIT 4");
$stmt->execute([$product['category_id'], $product['id']]);
$relatedProducts = $stmt->fetchAll();

// Ürün resimleri
$productImages = [];
if ($product['image']) {
    $productImages[] = $product['image'];
}
if ($product['images']) {
    $additionalImages = json_decode($product['images'], true);
    if (is_array($additionalImages)) {
        $productImages = array_merge($productImages, $additionalImages);
    }
}
?>

<!-- Breadcrumb -->
<section class="page-header page-header-sm">
    <div class="container">
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
            <span>/</span>
            <a href="urunler.php">Ürünler</a>
            <span>/</span>
            <?php if ($product['category_name']): ?>
                <a href="urunler.php?kategori=<?php echo e($product['category_slug']); ?>"><?php echo e($product['category_name']); ?></a>
                <span>/</span>
            <?php endif; ?>
            <span><?php echo e($product['name']); ?></span>
        </nav>
    </div>
</section>

<!-- Product Detail -->
<section class="product-detail">
    <div class="container">
        <div class="product-detail-grid">
            <!-- Product Images -->
            <div class="product-gallery">
                <div class="main-image">
                    <?php if (!empty($productImages)): ?>
                        <img src="<?php echo SITE_URL . '/' . e($productImages[0]); ?>" alt="<?php echo e($product['name']); ?>" id="mainImage">
                    <?php else: ?>
                        <div class="no-image"><i class="fas fa-image"></i></div>
                    <?php endif; ?>
                </div>
                <?php if (count($productImages) > 1): ?>
                    <div class="thumbnail-images">
                        <?php foreach ($productImages as $index => $img): ?>
                            <img src="<?php echo SITE_URL . '/' . e($img); ?>" 
                                 alt="<?php echo e($product['name']); ?>" 
                                 class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>"
                                 onclick="changeMainImage(this)">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Info -->
            <div class="product-info-detail">
                <?php if ($product['category_name']): ?>
                    <span class="product-category-badge"><?php echo e($product['category_name']); ?></span>
                <?php endif; ?>
                
                <h1><?php echo e($product['name']); ?></h1>
                
                <?php if ($product['sku']): ?>
                    <p class="product-sku">Ürün Kodu: <strong><?php echo e($product['sku']); ?></strong></p>
                <?php endif; ?>

                <div class="product-price-detail">
                    <?php if ($product['old_price']): ?>
                        <span class="old-price"><?php echo formatPrice($product['old_price']); ?></span>
                        <?php 
                        $discount = round((($product['old_price'] - $product['price']) / $product['old_price']) * 100);
                        ?>
                        <span class="discount-badge">%<?php echo $discount; ?> İndirim</span>
                    <?php endif; ?>
                    <span class="current-price"><?php echo formatPrice($product['price']); ?></span>
                </div>

                <div class="product-stock">
                    <?php if ($product['stock'] > 0): ?>
                        <span class="in-stock"><i class="fas fa-check-circle"></i> Stokta (<?php echo $product['stock']; ?> adet)</span>
                    <?php else: ?>
                        <span class="out-of-stock"><i class="fas fa-times-circle"></i> Stokta Yok</span>
                    <?php endif; ?>
                </div>

                <?php if ($product['short_description']): ?>
                    <p class="product-short-desc"><?php echo e($product['short_description']); ?></p>
                <?php endif; ?>

                <!-- Quantity and Actions -->
                <div class="product-actions-detail">
                    <div class="quantity-selector">
                        <button type="button" class="qty-btn minus">-</button>
                        <input type="number" value="1" min="1" max="<?php echo $product['stock']; ?>" id="quantity">
                        <button type="button" class="qty-btn plus">+</button>
                    </div>
                    
                    <a href="sepet-ekle.php?id=<?php echo $product['id']; ?>&qty=1" class="btn btn-primary btn-lg add-to-cart-btn">
                        <i class="fas fa-cart-plus"></i> Sepete Ekle
                    </a>
                </div>

                <div class="product-whatsapp-actions">
                    <a href="<?php echo getWhatsAppLink(getProductWhatsAppMessage($product)); ?>" 
                       class="btn btn-whatsapp btn-lg btn-block" target="_blank">
                        <i class="fab fa-whatsapp"></i> WhatsApp ile İletişime Geç
                    </a>
                    <p class="whatsapp-note">Ürün hakkında soru sormak veya sipariş vermek için WhatsApp'tan ulaşın.</p>
                </div>

                <?php if (isLoggedIn()): ?>
                    <button class="btn btn-outline favorite-btn-lg" data-product-id="<?php echo $product['id']; ?>">
                        <i class="<?php echo isProductFavorite($product['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                        <?php echo isProductFavorite($product['id']) ? 'Favorilerimde' : 'Favorilere Ekle'; ?>
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <!-- Product Description -->
        <?php if ($product['description']): ?>
            <div class="product-description-full">
                <h2>Ürün Açıklaması</h2>
                <div class="description-content">
                    <?php echo nl2br(e($product['description'])); ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Related Products -->
<?php if (!empty($relatedProducts)): ?>
<section class="products-section">
    <div class="container">
        <div class="section-header">
            <h2>Benzer Ürünler</h2>
        </div>
        <div class="products-grid">
            <?php foreach ($relatedProducts as $relProduct): ?>
                <div class="product-card">
                    <?php if ($relProduct['old_price']): ?>
                        <span class="product-badge">İndirimli</span>
                    <?php endif; ?>
                    
                    <a href="urun.php?slug=<?php echo e($relProduct['slug']); ?>" class="product-image">
                        <?php if ($relProduct['image']): ?>
                            <img src="<?php echo SITE_URL . '/' . e($relProduct['image']); ?>" alt="<?php echo e($relProduct['name']); ?>">
                        <?php else: ?>
                            <div class="no-image"><i class="fas fa-image"></i></div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="product-info">
                        <h3><a href="urun.php?slug=<?php echo e($relProduct['slug']); ?>"><?php echo e($relProduct['name']); ?></a></h3>
                        <div class="product-price">
                            <?php if ($relProduct['old_price']): ?>
                                <span class="old-price"><?php echo formatPrice($relProduct['old_price']); ?></span>
                            <?php endif; ?>
                            <span class="current-price"><?php echo formatPrice($relProduct['price']); ?></span>
                        </div>
                        <div class="product-actions">
                            <a href="sepet-ekle.php?id=<?php echo $relProduct['id']; ?>" class="btn btn-sm btn-primary">
                                <i class="fas fa-cart-plus"></i> Sepete Ekle
                            </a>
                            <a href="<?php echo getWhatsAppLink(getProductWhatsAppMessage($relProduct)); ?>" 
                               class="btn btn-sm btn-whatsapp" target="_blank">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<script>
function changeMainImage(thumb) {
    document.getElementById('mainImage').src = thumb.src;
    document.querySelectorAll('.thumbnail').forEach(t => t.classList.remove('active'));
    thumb.classList.add('active');
}

// Quantity selector
document.querySelector('.qty-btn.minus').addEventListener('click', function() {
    let qty = document.getElementById('quantity');
    if (qty.value > 1) qty.value = parseInt(qty.value) - 1;
    updateCartLink();
});

document.querySelector('.qty-btn.plus').addEventListener('click', function() {
    let qty = document.getElementById('quantity');
    if (qty.value < <?php echo $product['stock']; ?>) qty.value = parseInt(qty.value) + 1;
    updateCartLink();
});

function updateCartLink() {
    let qty = document.getElementById('quantity').value;
    document.querySelector('.add-to-cart-btn').href = 'sepet-ekle.php?id=<?php echo $product['id']; ?>&qty=' + qty;
}
</script>

<?php require_once 'includes/footer.php'; ?>
