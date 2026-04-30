<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Giriş yapmalısınız']);
    exit;
}

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

if ($productId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz ürün']);
    exit;
}

$db = getDB();

// Favori durumunu kontrol et
$stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
$stmt->execute([$_SESSION['user_id'], $productId]);
$favorite = $stmt->fetch();

if ($favorite) {
    // Favoriden çıkar
    $stmt = $db->prepare("DELETE FROM favorites WHERE id = ?");
    $stmt->execute([$favorite['id']]);
    echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Favorilerden çıkarıldı']);
} else {
    // Favoriye ekle
    $stmt = $db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Favorilere eklendi']);
}
?>
