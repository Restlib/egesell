<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

$categories = getCategories();
$cartCount = getCartCount();
$favoriteCount = getFavoriteCount();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Egesel Perakende - Mobilya Aksesuarları ve Hırdavat. Kaliteli ürünler, uygun fiyatlar.">
    <title><?php echo isset($pageTitle) ? e($pageTitle) . ' - ' : ''; ?>Egesel Perakende</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="top-bar-content">
                <div class="top-bar-left">
                    <a href="tel:+905318741028">
                        <i class="fas fa-phone"></i> +90 531 874 10 28
                    </a>
                    <a href="mailto:info@egeselperakende.com">
                        <i class="fas fa-envelope"></i> info@egeselperakende.com
                    </a>
                </div>
                <div class="top-bar-right">
                    <?php if (isLoggedIn()): ?>
                        <span>Hoşgeldin, <?php echo e($_SESSION['user_name']); ?></span>
                        <?php if (isAdmin()): ?>
                            <a href="<?php echo SITE_URL; ?>/admin/"><i class="fas fa-cog"></i> Admin Panel</a>
                        <?php endif; ?>
                        <a href="<?php echo SITE_URL; ?>/cikis.php"><i class="fas fa-sign-out-alt"></i> Çıkış</a>
                    <?php else: ?>
                        <a href="<?php echo SITE_URL; ?>/giris.php"><i class="fas fa-sign-in-alt"></i> Giriş Yap</a>
                        <a href="<?php echo SITE_URL; ?>/kayit.php"><i class="fas fa-user-plus"></i> Kayıt Ol</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <!-- Logo -->
                <a href="<?php echo SITE_URL; ?>" class="logo">
                    <span class="logo-text">EGESEL</span>
                    <span class="logo-subtext">PERAKENDE</span>
                </a>

                <!-- Search -->
                <form action="<?php echo SITE_URL; ?>/urunler.php" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Ürün ara..." value="<?php echo isset($_GET['q']) ? e($_GET['q']) : ''; ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>

                <!-- Header Actions -->
                <div class="header-actions">
                    <?php if (isLoggedIn()): ?>
                    <a href="<?php echo SITE_URL; ?>/favoriler.php" class="header-action" title="Favorilerim">
                        <i class="fas fa-heart"></i>
                        <?php if ($favoriteCount > 0): ?>
                            <span class="badge"><?php echo $favoriteCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <?php endif; ?>
                    <a href="<?php echo SITE_URL; ?>/sepet.php" class="header-action" title="Sepetim">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if ($cartCount > 0): ?>
                            <span class="badge"><?php echo $cartCount; ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo getWhatsAppLink('Merhaba, Egesel Perakende hakkında bilgi almak istiyorum.'); ?>" 
                       class="whatsapp-btn" target="_blank" title="WhatsApp ile İletişim">
                        <i class="fab fa-whatsapp"></i>
                        <span>İletişim</span>
                    </a>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" id="mobileMenuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav class="main-nav" id="mainNav">
        <div class="container">
            <ul class="nav-menu">
                <li><a href="<?php echo SITE_URL; ?>">Ana Sayfa</a></li>
                <li class="has-dropdown">
                    <a href="<?php echo SITE_URL; ?>/urunler.php">Ürünler <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <?php foreach ($categories as $category): ?>
                            <li>
                                <a href="<?php echo SITE_URL; ?>/urunler.php?kategori=<?php echo e($category['slug']); ?>">
                                    <?php echo e($category['name']); ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li><a href="<?php echo SITE_URL; ?>/hakkimizda.php">Hakkımızda</a></li>
                <li><a href="<?php echo SITE_URL; ?>/iletisim.php">İletişim</a></li>
            </ul>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php $flash = getFlash(); ?>
    <?php if ($flash): ?>
        <div class="container">
            <div class="alert alert-<?php echo e($flash['type']); ?>">
                <?php echo e($flash['message']); ?>
                <button class="alert-close">&times;</button>
            </div>
        </div>
    <?php endif; ?>

    <main class="main-content">
