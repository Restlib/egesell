<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

requireAdmin();

// İstatistikler
$db = getDB();
$stmt = $db->query("SELECT COUNT(*) as count FROM products");
$totalProducts = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'user'");
$totalUsers = $stmt->fetch()['count'];

$stmt = $db->query("SELECT COUNT(*) as count FROM categories");
$totalCategories = $stmt->fetch()['count'];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?>Admin Panel - Egesel Perakende</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <a href="<?php echo SITE_URL; ?>/admin/" class="admin-logo">
                    <span>EGESEL</span>
                    <small>Admin Panel</small>
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/admin/" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/admin/urunler.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'urunler.php' ? 'active' : ''; ?>">
                            <i class="fas fa-box"></i> Ürünler
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/admin/kategoriler.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kategoriler.php' ? 'active' : ''; ?>">
                            <i class="fas fa-folder"></i> Kategoriler
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/admin/kullanicilar.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'kullanicilar.php' ? 'active' : ''; ?>">
                            <i class="fas fa-users"></i> Kullanıcılar
                        </a>
                    </li>
                    <li>
                        <a href="<?php echo SITE_URL; ?>/admin/ayarlar.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'ayarlar.php' ? 'active' : ''; ?>">
                            <i class="fas fa-cog"></i> Ayarlar
                        </a>
                    </li>
                </ul>
            </nav>
            
            <div class="sidebar-footer">
                <a href="<?php echo SITE_URL; ?>" target="_blank">
                    <i class="fas fa-external-link-alt"></i> Siteyi Görüntüle
                </a>
                <a href="<?php echo SITE_URL; ?>/cikis.php">
                    <i class="fas fa-sign-out-alt"></i> Çıkış Yap
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="admin-main">
            <!-- Top Bar -->
            <header class="admin-topbar">
                <button class="sidebar-toggle" id="sidebarToggle">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="topbar-right">
                    <span class="admin-user">
                        <i class="fas fa-user-circle"></i>
                        <?php echo e($_SESSION['user_name']); ?>
                    </span>
                </div>
            </header>

            <!-- Content -->
            <main class="admin-content">
