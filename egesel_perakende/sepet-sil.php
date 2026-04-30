<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header('Location: sepet.php');
    exit;
}

if (isLoggedIn()) {
    $db = getDB();
    $stmt = $db->prepare("DELETE FROM cart_items WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
} else {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

setFlash('success', 'Ürün sepetinizden kaldırıldı.');
header('Location: sepet.php');
exit;
?>
