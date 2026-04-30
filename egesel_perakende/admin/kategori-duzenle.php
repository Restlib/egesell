<?php
$pageTitle = 'Kategori Düzenle';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: kategoriler.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: kategoriler.php');
    exit;
}

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
        $slug = $category['slug'];
        if ($name !== $category['name']) {
            $slug = createSlug($name);
            $stmt = $db->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            if ($stmt->fetch()) {
                $slug .= '-' . time();
            }
        }
        
        $stmt = $db->prepare("UPDATE categories SET name = ?, slug = ?, description = ?, sort_order = ?, is_active = ? WHERE id = ?");
        $stmt->execute([$name, $slug, $description, $sortOrder, $isActive, $id]);
        
        setFlash('success', 'Kategori güncellendi.');
        header('Location: kategoriler.php');
        exit;
    }
}
?>

<div class="page-header">
    <div class="page-header-left">
        <a href="kategoriler.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <h1>Kategori Düzenle</h1>
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
                   value="<?php echo e($category['name']); ?>">
        </div>
        
        <div class="form-group">
            <label for="description">Açıklama</label>
            <textarea id="description" name="description" rows="3"><?php echo e($category['description']); ?></textarea>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="sort_order">Sıralama</label>
                <input type="number" id="sort_order" name="sort_order" min="0" 
                       value="<?php echo e($category['sort_order']); ?>">
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" <?php echo $category['is_active'] ? 'checked' : ''; ?>>
                    <span>Aktif</span>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Değişiklikleri Kaydet
        </button>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
