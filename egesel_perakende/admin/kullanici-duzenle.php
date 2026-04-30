<?php
$pageTitle = 'Kullanıcı Düzenle';
require_once 'includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    header('Location: kullanicilar.php');
    exit;
}

$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    header('Location: kullanicilar.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';
    $isActive = isset($_POST['is_active']) ? 1 : 0;
    
    // Validasyon
    if (empty($username)) {
        $errors[] = 'Kullanıcı adı gereklidir.';
    }
    if (empty($email)) {
        $errors[] = 'E-posta adresi gereklidir.';
    }
    
    if (empty($errors)) {
        // Kullanıcı adı ve e-posta kontrolü (kendisi hariç)
        $stmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$username, $email, $id]);
        
        if ($stmt->fetch()) {
            $errors[] = 'Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.';
        } else {
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, password = ?, full_name = ?, phone = ?, role = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$username, $email, $hashedPassword, $fullName, $phone, $role, $isActive, $id]);
            } else {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, phone = ?, role = ?, is_active = ? WHERE id = ?");
                $stmt->execute([$username, $email, $fullName, $phone, $role, $isActive, $id]);
            }
            
            setFlash('success', 'Kullanıcı güncellendi.');
            header('Location: kullanicilar.php');
            exit;
        }
    }
}
?>

<div class="page-header">
    <div class="page-header-left">
        <a href="kullanicilar.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <h1>Kullanıcı Düzenle</h1>
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
        <h3>Kullanıcı Bilgileri</h3>
        
        <div class="form-row">
            <div class="form-group">
                <label for="username">Kullanıcı Adı *</label>
                <input type="text" id="username" name="username" required 
                       value="<?php echo e($user['username']); ?>">
            </div>
            <div class="form-group">
                <label for="email">E-posta *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo e($user['email']); ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Ad Soyad</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo e($user['full_name']); ?>">
            </div>
            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo e($user['phone']); ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="password">Yeni Şifre</label>
            <input type="password" id="password" name="password">
            <small>Değiştirmek istemiyorsanız boş bırakın</small>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="role">Rol</label>
                <select name="role" id="role" <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                    <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>Kullanıcı</option>
                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                </select>
                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                    <input type="hidden" name="role" value="admin">
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" 
                           <?php echo $user['is_active'] ? 'checked' : ''; ?>
                           <?php echo $user['id'] == $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                    <span>Aktif</span>
                </label>
                <?php if ($user['id'] == $_SESSION['user_id']): ?>
                    <input type="hidden" name="is_active" value="1">
                <?php endif; ?>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Değişiklikleri Kaydet
        </button>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
