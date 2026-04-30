<?php
$pageTitle = 'Sepetim';
require_once 'includes/header.php';

// Sepet öğelerini al
$cartItems = [];
$total = 0;

if (isLoggedIn()) {
    $db = getDB();
    $stmt = $db->prepare("SELECT ci.*, p.name, p.slug, p.price, p.image, p.stock 
                          FROM cart_items ci 
                          JOIN products p ON ci.product_id = p.id 
                          WHERE ci.user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $cartItems = $stmt->fetchAll();
} else {
    // Oturum açmamış kullanıcılar için session'dan al
    if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
        $db = getDB();
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $stmt = $db->prepare("SELECT id, name, slug, price, image, stock FROM products WHERE id IN ({$placeholders})");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll();
        
        foreach ($products as $p) {
            $cartItems[] = [
                'product_id' => $p['id'],
                'name' => $p['name'],
                'slug' => $p['slug'],
                'price' => $p['price'],
                'image' => $p['image'],
                'stock' => $p['stock'],
                'quantity' => $_SESSION['cart'][$p['id']]
            ];
        }
    }
}

// Toplam hesapla
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Sepetim</h1>
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
            <span>/</span>
            <span>Sepetim</span>
        </nav>
    </div>
</section>

<section class="cart-section">
    <div class="container">
        <?php if (empty($cartItems)): ?>
            <div class="empty-cart">
                <i class="fas fa-shopping-cart"></i>
                <h3>Sepetiniz Boş</h3>
                <p>Henüz sepetinize ürün eklemediniz.</p>
                <a href="urunler.php" class="btn btn-primary">Alışverişe Başla</a>
            </div>
        <?php else: ?>
            <div class="cart-layout">
                <div class="cart-items">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Ürün</th>
                                <th>Fiyat</th>
                                <th>Adet</th>
                                <th>Toplam</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr data-product-id="<?php echo $item['product_id']; ?>">
                                    <td class="cart-product">
                                        <a href="urun.php?slug=<?php echo e($item['slug']); ?>" class="cart-product-image">
                                            <?php if ($item['image']): ?>
                                                <img src="<?php echo SITE_URL . '/' . e($item['image']); ?>" alt="<?php echo e($item['name']); ?>">
                                            <?php else: ?>
                                                <div class="no-image-sm"><i class="fas fa-image"></i></div>
                                            <?php endif; ?>
                                        </a>
                                        <div class="cart-product-info">
                                            <a href="urun.php?slug=<?php echo e($item['slug']); ?>"><?php echo e($item['name']); ?></a>
                                        </div>
                                    </td>
                                    <td class="cart-price"><?php echo formatPrice($item['price']); ?></td>
                                    <td class="cart-quantity">
                                        <div class="quantity-selector quantity-selector-sm">
                                            <button type="button" class="qty-btn minus" onclick="updateQuantity(<?php echo $item['product_id']; ?>, -1)">-</button>
                                            <input type="number" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['stock']; ?>" 
                                                   id="qty-<?php echo $item['product_id']; ?>" onchange="updateQuantity(<?php echo $item['product_id']; ?>, 0, this.value)">
                                            <button type="button" class="qty-btn plus" onclick="updateQuantity(<?php echo $item['product_id']; ?>, 1)">+</button>
                                        </div>
                                    </td>
                                    <td class="cart-subtotal" id="subtotal-<?php echo $item['product_id']; ?>">
                                        <?php echo formatPrice($item['price'] * $item['quantity']); ?>
                                    </td>
                                    <td class="cart-remove">
                                        <a href="sepet-sil.php?id=<?php echo $item['product_id']; ?>" class="remove-btn" title="Sepetten Kaldır">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="cart-summary">
                    <h3>Sepet Özeti</h3>
                    <div class="summary-row">
                        <span>Ara Toplam:</span>
                        <span id="cart-subtotal"><?php echo formatPrice($total); ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Toplam:</span>
                        <span id="cart-total"><?php echo formatPrice($total); ?></span>
                    </div>
                    
                    <div class="cart-actions">
                        <?php
                        $whatsappMessage = getCartWhatsAppMessage($cartItems);
                        ?>
                        <a href="<?php echo getWhatsAppLink($whatsappMessage); ?>" class="btn btn-whatsapp btn-lg btn-block" target="_blank">
                            <i class="fab fa-whatsapp"></i> WhatsApp ile Sipariş Ver
                        </a>
                        <p class="order-note">
                            <i class="fas fa-info-circle"></i>
                            Sepetinizdeki ürünler WhatsApp üzerinden tarafımıza iletilecek ve siparişiniz işleme alınacaktır.
                        </p>
                    </div>
                    
                    <a href="urunler.php" class="btn btn-outline btn-block">
                        <i class="fas fa-arrow-left"></i> Alışverişe Devam Et
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script>
function updateQuantity(productId, change, newValue = null) {
    let input = document.getElementById('qty-' + productId);
    let currentQty = parseInt(input.value);
    let maxQty = parseInt(input.max);
    
    if (newValue !== null) {
        currentQty = parseInt(newValue);
    } else {
        currentQty += change;
    }
    
    if (currentQty < 1) currentQty = 1;
    if (currentQty > maxQty) currentQty = maxQty;
    
    input.value = currentQty;
    
    // AJAX ile güncelle
    fetch('sepet-guncelle.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'product_id=' + productId + '&quantity=' + currentQty
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('subtotal-' + productId).textContent = data.subtotal;
            document.getElementById('cart-subtotal').textContent = data.total;
            document.getElementById('cart-total').textContent = data.total;
        }
    });
}
</script>

<?php require_once 'includes/footer.php'; ?>
