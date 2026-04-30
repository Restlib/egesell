<?php
$pageTitle = 'Yeni Ürün Ekle';
require_once 'includes/header.php';

// Kategorileri al
$stmt = $db->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
$allCategories = $stmt->fetchAll();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $shortDescription = trim($_POST['short_description'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $oldPrice = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : null;
    $sku = trim($_POST['sku'] ?? '');
    $stock = (int)($_POST['stock'] ?? 0);
    $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasyon
    if (empty($name)) {
        $errors[] = 'Ürün adı gereklidir.';
    }
    if ($price <= 0) {
        $errors[] = 'Geçerli bir fiyat girin.';
    }
    
    // Resim yükleme
    $imagePath = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['image']);
        if ($upload['success']) {
            $imagePath = $upload['filename'];
        } else {
            $errors[] = $upload['message'];
        }
    }
    
    if (empty($errors)) {
        $slug = createSlug($name);
        
        // Slug benzersizliği
        $stmt = $db->prepare("SELECT id FROM products WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        $stmt = $db->prepare("INSERT INTO products (name, slug, description, short_description, price, old_price, sku, stock, category_id, image, is_featured, is_active) 
                              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $description, $shortDescription, $price, $oldPrice, $sku, $stock, $categoryId, $imagePath, $isFeatured, $isActive]);
        
        setFlash('success', 'Ürün başarıyla eklendi.');
        header('Location: urunler.php');
        exit;
    }
}
?>

<div class="page-header">
    <div class="page-header-left">
        <a href="urunler.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <h1>Yeni Ürün Ekle</h1>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-error">
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="POST" action="" enctype="multipart/form-data" class="admin-form">
    <div class="form-grid">
        <div class="form-main">
            <div class="admin-card">
                <h3>Ürün Bilgileri</h3>
                
                <div class="form-group">
                    <label for="name">Ürün Adı *</label>
                    <input type="text" id="name" name="name" required 
                           value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="short_description">Kısa Açıklama</label>
                    <input type="text" id="short_description" name="short_description" maxlength="500"
                           value="<?php echo isset($_POST['short_description']) ? e($_POST['short_description']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <label for="description">Detaylı Açıklama</label>
                    <textarea id="description" name="description" rows="6"><?php echo isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea>
                </div>
            </div>
            
            <div class="admin-card">
                <h3>Fiyat ve Stok</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="price">Satış Fiyatı (TL) *</label>
                        <input type="number" id="price" name="price" step="0.01" min="0" required
                               value="<?php echo isset($_POST['price']) ? e($_POST['price']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="old_price">Eski Fiyat (TL)</label>
                        <input type="number" id="old_price" name="old_price" step="0.01" min="0"
                               value="<?php echo isset($_POST['old_price']) ? e($_POST['old_price']) : ''; ?>">
                        <small>İndirimli gösterim için</small>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="sku">Ürün Kodu (SKU)</label>
                        <input type="text" id="sku" name="sku"
                               value="<?php echo isset($_POST['sku']) ? e($_POST['sku']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label for="stock">Stok Adedi</label>
                        <input type="number" id="stock" name="stock" min="0"
                               value="<?php echo isset($_POST['stock']) ? e($_POST['stock']) : '0'; ?>">
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-sidebar">
            <div class="admin-card">
                <h3>Yayınlama</h3>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>Aktif (Yayında)</span>
                    </label>
                </div>
                
                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="is_featured" value="1">
                        <span>Öne Çıkan Ürün</span>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">
                    <i class="fas fa-save"></i> Ürünü Kaydet
                </button>
            </div>
            
            <div class="admin-card">
                <h3>Kategori</h3>
                
                <div class="form-group">
                    <select name="category_id" id="category_id">
                        <option value="">Kategori Seçin</option>
                        <?php foreach ($allCategories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" 
                                    <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : ''; ?>>
                                <?php echo e($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="admin-card">
                <h3>Ürün Resmi</h3>
                
                <div class="form-group">
                    <div class="image-upload-area" id="imageUploadArea">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <p>Resim yüklemek için tıklayın</p>
                        <input type="file" name="image" id="image" accept="image/*">
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('imagePreview').innerHTML = '<img src="' + e.target.result + '">';
        }
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
