<?php
$pageTitle = 'Dashboard';
require_once 'includes/header.php';

// Son eklenen ürünler
$stmt = $db->query("SELECT * FROM products ORDER BY created_at DESC LIMIT 5");
$recentProducts = $stmt->fetchAll();

// Son kayıt olan kullanıcılar
$stmt = $db->query("SELECT * FROM users WHERE role = 'user' ORDER BY created_at DESC LIMIT 5");
$recentUsers = $stmt->fetchAll();
?>

<div class="page-header">
    <h1>Dashboard</h1>
    <p>Hoş geldiniz, <?php echo e($_SESSION['user_name']); ?>!</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon bg-primary">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalProducts; ?></h3>
            <p>Toplam Ürün</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-success">
            <i class="fas fa-folder"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalCategories; ?></h3>
            <p>Kategori</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-info">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-info">
            <h3><?php echo $totalUsers; ?></h3>
            <p>Kullanıcı</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon bg-warning">
            <i class="fab fa-whatsapp"></i>
        </div>
        <div class="stat-info">
            <h3>Aktif</h3>
            <p>WhatsApp Sipariş</p>
        </div>
    </div>
</div>

<div class="dashboard-grid">
    <!-- Recent Products -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>Son Eklenen Ürünler</h3>
            <a href="urunler.php" class="btn btn-sm btn-outline">Tümü</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentProducts)): ?>
                <p class="text-muted">Henüz ürün eklenmemiş.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ürün</th>
                            <th>Fiyat</th>
                            <th>Stok</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentProducts as $product): ?>
                            <tr>
                                <td>
                                    <div class="product-cell">
                                        <?php if ($product['image']): ?>
                                            <img src="<?php echo SITE_URL . '/' . e($product['image']); ?>" alt="">
                                        <?php endif; ?>
                                        <span><?php echo e($product['name']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo formatPrice($product['price']); ?></td>
                                <td><?php echo $product['stock']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="dashboard-card">
        <div class="card-header">
            <h3>Son Kayıt Olan Kullanıcılar</h3>
            <a href="kullanicilar.php" class="btn btn-sm btn-outline">Tümü</a>
        </div>
        <div class="card-body">
            <?php if (empty($recentUsers)): ?>
                <p class="text-muted">Henüz kullanıcı kaydı yok.</p>
            <?php else: ?>
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Kullanıcı</th>
                            <th>E-posta</th>
                            <th>Tarih</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo e($user['username']); ?></td>
                                <td><?php echo e($user['email']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="quick-actions">
    <h3>Hızlı İşlemler</h3>
    <div class="actions-grid">
        <a href="urun-ekle.php" class="action-card">
            <i class="fas fa-plus-circle"></i>
            <span>Yeni Ürün Ekle</span>
        </a>
        <a href="kategori-ekle.php" class="action-card">
            <i class="fas fa-folder-plus"></i>
            <span>Yeni Kategori Ekle</span>
        </a>
        <a href="kullanici-ekle.php" class="action-card">
            <i class="fas fa-user-plus"></i>
            <span>Yeni Kullanıcı Ekle</span>
        </a>
        <a href="ayarlar.php" class="action-card">
            <i class="fas fa-cog"></i>
            <span>Site Ayarları</span>
        </a>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
