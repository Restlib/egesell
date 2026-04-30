<?php
$pageTitle = 'Ürünler';
require_once 'includes/header.php';

// Parametreler
$kategoriSlug = isset($_GET['kategori']) ? $_GET['kategori'] : null;
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$sort = isset($_GET['siralama']) ? $_GET['siralama'] : 'newest';
$page = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$perPage = 12;

// Kategori bilgisi
$currentCategory = null;
if ($kategoriSlug) {
    $stmt = $db = getDB();
    $stmt = $db->prepare("SELECT * FROM categories WHERE slug = ? AND is_active = 1");
    $stmt->execute([$kategoriSlug]);
    $currentCategory = $stmt->fetch();
    if ($currentCategory) {
        $pageTitle = $currentCategory['name'];
    }
}

// Ürünleri say
$db = getDB();
$countSql = "SELECT COUNT(*) as total FROM products WHERE is_active = 1";
$params = [];

if ($currentCategory) {
    $countSql .= " AND category_id = ?";
    $params[] = $currentCategory['id'];
}

if ($search) {
    $countSql .= " AND (name LIKE ? OR description LIKE ? OR sku LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalProducts = $stmt->fetch()['total'];

// Sayfalama
$pagination = paginate($totalProducts, $page, $perPage);

// Ürünleri getir
$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.is_active = 1";
$params = [];

if ($currentCategory) {
    $sql .= " AND p.category_id = ?";
    $params[] = $currentCategory['id'];
}

if ($search) {
    $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
    $searchTerm = "%{$search}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

// Sıralama
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name':
        $sql .= " ORDER BY p.name ASC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$sql .= " LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1><?php echo $currentCategory ? e($currentCategory['name']) : ($search ? 'Arama Sonuçları' : 'Tüm Ürünler'); ?></h1>
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
            <span>/</span>
            <?php if ($currentCategory): ?>
                <a href="urunler.php">Ürünler</a>
                <span>/</span>
                <span><?php echo e($currentCategory['name']); ?></span>
            <?php else: ?>
                <span>Ürünler</span>
            <?php endif; ?>
        </nav>
    </div>
</section>

<section class="products-page">
    <div class="container">
        <div class="products-layout">
            <!-- Sidebar -->
            <aside class="products-sidebar">
                <div class="sidebar-section">
                    <h3>Kategoriler</h3>
                    <ul class="category-list">
                        <li>
                            <a href="urunler.php" class="<?php echo !$currentCategory ? 'active' : ''; ?>">
                                Tüm Ürünler
                            </a>
                        </li>
                        <?php foreach ($categories as $cat): ?>
                            <li>
                                <a href="urunler.php?kategori=<?php echo e($cat['slug']); ?>"
                                   class="<?php echo ($currentCategory && $currentCategory['id'] == $cat['id']) ? 'active' : ''; ?>">
                                    <?php echo e($cat['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </aside>

            <!-- Products Grid -->
            <div class="products-content">
                <!-- Toolbar -->
                <div class="products-toolbar">
                    <div class="toolbar-left">
                        <span class="product-count"><?php echo $totalProducts; ?> ürün bulundu</span>
                    </div>
                    <div class="toolbar-right">
                        <form action="" method="GET" class="sort-form">
                            <?php if ($kategoriSlug): ?>
                                <input type="hidden" name="kategori" value="<?php echo e($kategoriSlug); ?>">
                            <?php endif; ?>
                            <?php if ($search): ?>
                                <input type="hidden" name="q" value="<?php echo e($search); ?>">
                            <?php endif; ?>
                            <select name="siralama" onchange="this.form.submit()">
                                <option value="newest" <?php echo $sort == 'newest' ? 'selected' : ''; ?>>En Yeni</option>
                                <option value="price_asc" <?php echo $sort == 'price_asc' ? 'selected' : ''; ?>>Fiyat (Düşükten Yükseğe)</option>
                                <option value="price_desc" <?php echo $sort == 'price_desc' ? 'selected' : ''; ?>>Fiyat (Yüksekten Düşüğe)</option>
                                <option value="name" <?php echo $sort == 'name' ? 'selected' : ''; ?>>İsme Göre</option>
                            </select>
                        </form>
                    </div>
                </div>

                <?php if (empty($products)): ?>
                    <div class="no-products">
                        <i class="fas fa-box-open"></i>
                        <h3>Ürün Bulunamadı</h3>
                        <p>Aradığınız kriterlere uygun ürün bulunamadı.</p>
                        <a href="urunler.php" class="btn btn-primary">Tüm Ürünleri Görüntüle</a>
                    </div>
                <?php else: ?>
                    <div class="products-grid">
                        <?php foreach ($products as $product): ?>
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
                                           class="btn btn-sm btn-whatsapp" target="_blank">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <?php if (isLoggedIn()): ?>
                                            <button class="btn btn-sm btn-outline favorite-btn" 
                                                    data-product-id="<?php echo $product['id']; ?>">
                                                <i class="<?php echo isProductFavorite($product['id']) ? 'fas' : 'far'; ?> fa-heart"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($pagination['total_pages'] > 1): ?>
                        <div class="pagination">
                            <?php if ($pagination['current_page'] > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $pagination['current_page'] - 1])); ?>" class="page-link">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $i])); ?>" 
                                   class="page-link <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($pagination['current_page'] < $pagination['total_pages']): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $pagination['current_page'] + 1])); ?>" class="page-link">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
