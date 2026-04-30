<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$quantity = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;

if ($productId <= 0) {
    setFlash('error', 'Geçersiz ürün.');
    header('Location: urunler.php');
    exit;
}

// Ürünü kontrol et
$db = getDB();
$stmt = $db->prepare("SELECT id, name, stock FROM products WHERE id = ? AND is_active = 1");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'Ürün bulunamadı.');
    header('Location: urunler.php');
    exit;
}

// Stok kontrolü
if ($product['stock'] < $quantity) {
    setFlash('error', 'Yeterli stok bulunmamaktadır.');
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

if (isLoggedIn()) {
    // Veritabanına ekle
    $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)
                          ON DUPLICATE KEY UPDATE quantity = quantity + ?");
    $stmt->execute([$_SESSION['user_id'], $productId, $quantity, $quantity]);
} else {
    // Session'a ekle
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

setFlash('success', '"' . $product['name'] . '" sepetinize eklendi.');
header('Location: sepet.php');
exit;
?>
