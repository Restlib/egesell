<?php
$pageTitle = 'Ürünler';
require_once 'includes/header.php';

// Ürün silme
if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    $id = (int)$_GET['sil'];
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'Ürün silindi.');
    header('Location: urunler.php');
    exit;
}

// Ürünleri listele
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$page = isset($_GET['sayfa']) ? (int)$_GET['sayfa'] : 1;
$perPage = 20;

$countSql = "SELECT COUNT(*) as total FROM products";
$params = [];

if ($search) {
    $countSql .= " WHERE name LIKE ? OR sku LIKE ?";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$stmt = $db->prepare($countSql);
$stmt->execute($params);
$totalProducts = $stmt->fetch()['total'];

$pagination = paginate($totalProducts, $page, $perPage);

$sql = "SELECT p.*, c.name as category_name FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id";

if ($search) {
    $sql .= " WHERE p.name LIKE ? OR p.sku LIKE ?";
}

$sql .= " ORDER BY p.created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Ürünler</h1>
        <p><?php echo $totalProducts; ?> ürün bulundu</p>
    </div>
    <a href="urun-ekle.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Ürün Ekle
    </a>
</div>

<!-- Flash Messages -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['type']); ?>">
        <?php echo e($flash['message']); ?>
    </div>
<?php endif; ?>

<!-- Search -->
<div class="admin-toolbar">
    <form action="" method="GET" class="search-form-admin">
        <input type="text" name="q" placeholder="Ürün ara..." value="<?php echo e($search); ?>">
        <button type="submit"><i class="fas fa-search"></i></button>
    </form>
</div>

<!-- Products Table -->
<div class="admin-card">
    <?php if (empty($products)): ?>
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>Henüz ürün eklenmemiş.</p>
            <a href="urun-ekle.php" class="btn btn-primary">İlk Ürünü Ekle</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Resim</th>
                        <th>Ürün Adı</th>
                        <th>Kategori</th>
                        <th>Fiyat</th>
                        <th>Stok</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <?php if ($product['image']): ?>
                                    <img src="<?php echo SITE_URL . '/' . e($product['image']); ?>" alt="" class="table-img">
                                <?php else: ?>
                                    <div class="no-image-sm"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo e($product['name']); ?></strong>
                                <?php if ($product['sku']): ?>
                                    <br><small class="text-muted"><?php echo e($product['sku']); ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($product['category_name'] ?? '-'); ?></td>
                            <td>
                                <?php if ($product['old_price']): ?>
                                    <del class="text-muted"><?php echo formatPrice($product['old_price']); ?></del><br>
                                <?php endif; ?>
                                <strong><?php echo formatPrice($product['price']); ?></strong>
                            </td>
                            <td>
                                <span class="badge <?php echo $product['stock'] > 0 ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $product['stock']; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $product['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                                    <?php echo $product['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                                <?php if ($product['is_featured']): ?>
                                    <span class="badge badge-warning">Öne Çıkan</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo SITE_URL; ?>/urun.php?slug=<?php echo e($product['slug']); ?>" 
                                       class="btn btn-sm btn-outline" target="_blank" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="urun-duzenle.php?id=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="urunler.php?sil=<?php echo $product['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Sil"
                                       onclick="return confirm('Bu ürünü silmek istediğinize emin misiniz?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($pagination['total_pages'] > 1): ?>
            <div class="pagination">
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['sayfa' => $i])); ?>" 
                       class="page-link <?php echo $i == $pagination['current_page'] ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
