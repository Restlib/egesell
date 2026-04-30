<?php
/**
 * Egesel Perakende - Yardımcı Fonksiyonlar
 */

// Güvenli çıktı
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Slug oluştur
function createSlug($string) {
    $turkish = array('ş', 'Ş', 'ı', 'İ', 'ğ', 'Ğ', 'ü', 'Ü', 'ö', 'Ö', 'ç', 'Ç');
    $english = array('s', 's', 'i', 'i', 'g', 'g', 'u', 'u', 'o', 'o', 'c', 'c');
    $string = str_replace($turkish, $english, $string);
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

// Fiyat formatla
function formatPrice($price) {
    return number_format($price, 2, ',', '.') . ' TL';
}

// WhatsApp link oluştur
function getWhatsAppLink($message = '') {
    $number = WHATSAPP_NUMBER;
    $message = urlencode($message);
    return "https://wa.me/{$number}?text={$message}";
}

// Ürün için WhatsApp mesajı oluştur
function getProductWhatsAppMessage($product) {
    $message = "Merhaba, aşağıdaki ürün hakkında bilgi almak istiyorum:\n\n";
    $message .= "Ürün: " . $product['name'] . "\n";
    $message .= "Fiyat: " . formatPrice($product['price']) . "\n";
    $message .= "Ürün Kodu: " . ($product['sku'] ?? 'Belirtilmemiş') . "\n";
    $message .= "Link: " . SITE_URL . "/urun.php?slug=" . $product['slug'];
    return $message;
}

// Sepet için WhatsApp mesajı oluştur
function getCartWhatsAppMessage($cartItems) {
    $message = "Merhaba, aşağıdaki ürünleri sipariş etmek istiyorum:\n\n";
    $total = 0;
    
    foreach ($cartItems as $item) {
        $subtotal = $item['price'] * $item['quantity'];
        $total += $subtotal;
        $message .= "- " . $item['name'] . " x " . $item['quantity'] . " = " . formatPrice($subtotal) . "\n";
    }
    
    $message .= "\nToplam: " . formatPrice($total);
    return $message;
}

// Kullanıcı giriş kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Admin kontrolü
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Giriş gerektir
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: giris.php');
        exit;
    }
}

// Admin girişi gerektir
function requireAdmin() {
    if (!isAdmin()) {
        header('Location: ../giris.php');
        exit;
    }
}

// Resim yükle
function uploadImage($file, $directory = 'uploads/products/') {
    $targetDir = __DIR__ . '/../' . $directory;
    
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // Dosya türü kontrolü
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    if (!in_array($imageFileType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Sadece JPG, JPEG, PNG, GIF ve WEBP dosyaları yüklenebilir.'];
    }
    
    // Dosya boyutu kontrolü (5MB)
    if ($file['size'] > 5000000) {
        return ['success' => false, 'message' => 'Dosya boyutu 5MB\'dan küçük olmalıdır.'];
    }
    
    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => true, 'filename' => $directory . $fileName];
    }
    
    return ['success' => false, 'message' => 'Dosya yüklenirken bir hata oluştu.'];
}

// Flash mesaj ayarla
function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

// Flash mesaj göster
function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Sepet sayısı al
function getCartCount() {
    if (!isLoggedIn()) {
        return isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT SUM(quantity) as count FROM cart_items WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

// Favori sayısı al
function getFavoriteCount() {
    if (!isLoggedIn()) {
        return 0;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM favorites WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $result = $stmt->fetch();
    return $result['count'] ?? 0;
}

// Ürün favoride mi kontrol et
function isProductFavorite($productId) {
    if (!isLoggedIn()) {
        return false;
    }
    
    $db = getDB();
    $stmt = $db->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$_SESSION['user_id'], $productId]);
    return $stmt->fetch() !== false;
}

// Ayar değeri al
function getSetting($key) {
    $db = getDB();
    $stmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
    $stmt->execute([$key]);
    $result = $stmt->fetch();
    return $result['setting_value'] ?? null;
}

// Tüm kategorileri al
function getCategories() {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY sort_order ASC");
    return $stmt->fetchAll();
}

// Öne çıkan ürünleri al
function getFeaturedProducts($limit = 8) {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM products WHERE is_featured = 1 AND is_active = 1 ORDER BY created_at DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll();
}

// Sayfalama
function paginate($totalItems, $currentPage, $perPage = 12) {
    $totalPages = ceil($totalItems / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'per_page' => $perPage,
        'offset' => $offset
    ];
}

// CSRF token oluştur
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token doğrula
function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
?>
