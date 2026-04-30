<?php
$pageTitle = 'Kategoriler';
require_once 'includes/header.php';

// Kategori silme
if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    $id = (int)$_GET['sil'];
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    setFlash('success', 'Kategori silindi.');
    header('Location: kategoriler.php');
    exit;
}

// Kategorileri listele
$stmt = $db->query("SELECT c.*, (SELECT COUNT(*) FROM products WHERE category_id = c.id) as product_count 
                    FROM categories c ORDER BY c.sort_order ASC");
$allCategories = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Kategoriler</h1>
        <p><?php echo count($allCategories); ?> kategori bulundu</p>
    </div>
    <a href="kategori-ekle.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Kategori Ekle
    </a>
</div>

<!-- Flash Messages -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['type']); ?>">
        <?php echo e($flash['message']); ?>
    </div>
<?php endif; ?>

<div class="admin-card">
    <?php if (empty($allCategories)): ?>
        <div class="empty-state">
            <i class="fas fa-folder-open"></i>
            <p>Henüz kategori eklenmemiş.</p>
            <a href="kategori-ekle.php" class="btn btn-primary">İlk Kategoriyi Ekle</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Sıra</th>
                        <th>Kategori Adı</th>
                        <th>Slug</th>
                        <th>Ürün Sayısı</th>
                        <th>Durum</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allCategories as $category): ?>
                        <tr>
                            <td><?php echo $category['sort_order']; ?></td>
                            <td><strong><?php echo e($category['name']); ?></strong></td>
                            <td><code><?php echo e($category['slug']); ?></code></td>
                            <td><?php echo $category['product_count']; ?></td>
                            <td>
                                <span class="badge <?php echo $category['is_active'] ? 'badge-success' : 'badge-secondary'; ?>">
                                    <?php echo $category['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="<?php echo SITE_URL; ?>/urunler.php?kategori=<?php echo e($category['slug']); ?>" 
                                       class="btn btn-sm btn-outline" target="_blank" title="Görüntüle">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="kategori-duzenle.php?id=<?php echo $category['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="kategoriler.php?sil=<?php echo $category['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Sil"
                                       onclick="return confirm('Bu kategoriyi silmek istediğinize emin misiniz? Kategorideki ürünler kategorisiz kalacaktır.')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'includes/footer.php'; ?>
