<?php
$pageTitle = 'Yeni Kategori Ekle';
require_once 'includes/header.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sortOrder = (int)($_POST['sort_order'] ?? 0);
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    if (empty($name)) {
        $errors[] = 'Kategori adı gereklidir.';
    }
    
    if (empty($errors)) {
        $slug = createSlug($name);
        
        // Slug benzersizliği
        $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ?");
        $stmt->execute([$slug]);
        if ($stmt->fetch()) {
            $slug .= '-' . time();
        }
        
        $stmt = $db->prepare("INSERT INTO categories (name, slug, description, sort_order, is_active) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $description, $sortOrder, $isActive]);
        
        setFlash('success', 'Kategori oluşturuldu.');
        header('Location: kategoriler.php');
        exit;
    }
}
?>

<div class="page-header">
    <div class="page-header-left">
        <a href="kategoriler.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <h1>Yeni Kategori Ekle</h1>
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

<form method="POST" action="" class="admin-form">
    <div class="admin-card" style="max-width: 600px;">
        <h3>Kategori Bilgileri</h3>
        
        <div class="form-group">
            <label for="name">Kategori Adı *</label>
            <input type="text" id="name" name="name" required 
                   value="<?php echo isset($_POST['name']) ? e($_POST['name']) : ''; ?>">
        </div>
        
        <div class="form-group">
            <label for="description">Açıklama</label>
            <textarea id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? e($_POST['description']) : ''; ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Sıralama</label>
                <input type="number" id="sort_order" name="sort_order" min="0" 
                       value="<?php echo isset($_POST['sort_order']) ? e($_POST['sort_order']) : '0'; ?>">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Aktif</span>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Kategori Oluştur
        </button>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
