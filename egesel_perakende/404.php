<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$pageTitle = 'Sayfa Bulunamadı';
include 'includes/header.php';
?>

<main class="main-content">
    <section class="error-page">
        <div class="container">
            <div class="error-content">
                <div class="error-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h1>404</h1>
                <h2>Sayfa Bulunamadı</h2>
                <p>Aradığınız sayfa mevcut değil veya taşınmış olabilir.</p>
                <div class="error-actions">
                    <a href="index.php" class="btn btn-primary">
                        <i class="fas fa-home"></i> Ana Sayfaya Dön
                    </a>
                    <a href="urunler.php" class="btn btn-outline">
                        <i class="fas fa-shopping-bag"></i> Ürünleri İncele
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
