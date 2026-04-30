<?php
$pageTitle = 'Kullanıcılar';
require_once 'includes/header.php';

// Kullanıcı silme
if (isset($_GET['sil']) && is_numeric($_GET['sil'])) {
    $id = (int)$_GET['sil'];
    // Admin kendini silemesin
    if ($id != $_SESSION['user_id']) {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        setFlash('success', 'Kullanıcı silindi.');
    }
    header('Location: kullanicilar.php');
    exit;
}

// Kullanıcı durumu değiştirme
if (isset($_GET['durum']) && is_numeric($_GET['durum'])) {
    $id = (int)$_GET['durum'];
    $stmt = $db->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND id != ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
    setFlash('success', 'Kullanıcı durumu güncellendi.');
    header('Location: kullanicilar.php');
    exit;
}

// Kullanıcıları listele
$stmt = $db->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<div class="page-header">
    <div class="page-header-left">
        <h1>Kullanıcılar</h1>
        <p><?php echo count($users); ?> kullanıcı bulundu</p>
    </div>
    <a href="kullanici-ekle.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Yeni Kullanıcı Ekle
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
    <?php if (empty($users)): ?>
        <div class="empty-state">
            <i class="fas fa-users"></i>
            <p>Henüz kullanıcı yok.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kullanıcı Adı</th>
                        <th>E-posta</th>
                        <th>Ad Soyad</th>
                        <th>Rol</th>
                        <th>Durum</th>
                        <th>Kayıt Tarihi</th>
                        <th>İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><strong><?php echo e($user['username']); ?></strong></td>
                            <td><?php echo e($user['email']); ?></td>
                            <td><?php echo e($user['full_name'] ?? '-'); ?></td>
                            <td>
                                <span class="badge <?php echo $user['role'] === 'admin' ? 'badge-primary' : 'badge-secondary'; ?>">
                                    <?php echo $user['role'] === 'admin' ? 'Admin' : 'Kullanıcı'; ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?php echo $user['is_active'] ? 'badge-success' : 'badge-danger'; ?>">
                                    <?php echo $user['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="kullanici-duzenle.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-primary" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                        <a href="kullanicilar.php?durum=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-warning" title="Durumu Değiştir">
                                            <i class="fas fa-power-off"></i>
                                        </a>
                                        <a href="kullanicilar.php?sil=<?php echo $user['id']; ?>" 
                                           class="btn btn-sm btn-danger" title="Sil"
                                           onclick="return confirm('Bu kullanıcıyı silmek istediğinize emin misiniz?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
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
