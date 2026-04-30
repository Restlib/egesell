<?php
$pageTitle = 'Yeni Kullanıcı Ekle';
require_once 'includes/header.php';

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
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Geçerli bir e-posta adresi girin.';
    }
    if (empty($password)) {
        $errors[] = 'Şifre gereklidir.';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Şifre en az 6 karakter olmalıdır.';
    }
    
    if (empty($errors)) {
        // Kullanıcı adı ve e-posta kontrolü
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $errors[] = 'Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone, role, is_active) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $fullName, $phone, $role, $isActive]);
            
            setFlash('success', 'Kullanıcı oluşturuldu.');
            header('Location: kullanicilar.php');
            exit;
        }
    }
}
?>

<div class="page-header">
    <div class="page-header-left">
        <a href="kullanicilar.php" class="back-link"><i class="fas fa-arrow-left"></i></a>
        <h1>Yeni Kullanıcı Ekle</h1>
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
                       value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="email">E-posta *</label>
                <input type="email" id="email" name="email" required 
                       value="<?php echo isset($_POST['email']) ? e($_POST['email']) : ''; ?>">
            </div>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="full_name">Ad Soyad</label>
                <input type="text" id="full_name" name="full_name" 
                       value="<?php echo isset($_POST['full_name']) ? e($_POST['full_name']) : ''; ?>">
            </div>
            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="tel" id="phone" name="phone" 
                       value="<?php echo isset($_POST['phone']) ? e($_POST['phone']) : ''; ?>">
            </div>
        </div>
        
        <div class="form-group">
            <label for="password">Şifre *</label>
            <input type="password" id="password" name="password" required>
        </div>
        
        <div class="form-row">
            <div class="form-group">
                <label for="role">Rol</label>
                <select name="role" id="role">
                    <option value="user">Kullanıcı</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_active" value="1" checked>
                    <span>Aktif</span>
                </label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Kullanıcı Oluştur
        </button>
    </div>
</form>

<?php require_once 'includes/footer.php'; ?>
