<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

$productId = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($productId <= 0 || $quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Geçersiz istek']);
    exit;
}

// Ürün bilgisi
$db = getDB();
$stmt = $db->prepare("SELECT price FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Ürün bulunamadı']);
    exit;
}

if (isLoggedIn()) {
    // Veritabanını güncelle
    $stmt = $db->prepare("UPDATE cart_items SET quantity = ? WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$quantity, $_SESSION['user_id'], $productId]);
    
    // Toplam hesapla
    $stmt = $db->prepare("SELECT SUM(ci.quantity * p.price) as total FROM cart_items ci 
                          JOIN products p ON ci.product_id = p.id WHERE ci.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    $total = $result['total'] ?? 0;
} else {
    // Session güncelle
    $_SESSION['cart'][$productId] = $quantity;
    
    // Toplam hesapla
    $total = 0;
    if (!empty($_SESSION['cart'])) {
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $stmt = $db->prepare("SELECT id, price FROM products WHERE id IN ({$placeholders})");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll();
        
        foreach ($products as $p) {
            $total += $p['price'] * $_SESSION['cart'][$p['id']];
        }
    }
}

$subtotal = $product['price'] * $quantity;

echo json_encode([
    'success' => true,
    'subtotal' => formatPrice($subtotal),
    'total' => formatPrice($total)
]);
?>
