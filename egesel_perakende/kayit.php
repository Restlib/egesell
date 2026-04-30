<?php
$pageTitle = 'Kayıt Ol';
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Zaten giriş yapmışsa
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    // Validasyon
    if (empty($username)) {
        $errors[] = 'Kullanıcı adı gereklidir.';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Kullanıcı adı en az 3 karakter olmalıdır.';
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
    
    if ($password !== $passwordConfirm) {
        $errors[] = 'Şifreler eşleşmiyor.';
    }
    
    if (empty($errors)) {
        $db = getDB();
        
        // Kullanıcı adı ve e-posta kontrolü
        $stmt = $db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->fetch()) {
            $errors[] = 'Bu kullanıcı adı veya e-posta adresi zaten kullanılıyor.';
        } else {
            // Kayıt
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("INSERT INTO users (username, email, password, full_name, phone) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashedPassword, $fullName, $phone]);
            
            $success = true;
            setFlash('success', 'Hesabınız oluşturuldu. Giriş yapabilirsiniz.');
            header('Location: giris.php');
            exit;
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Kayıt Ol</h1>
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
            <span>/</span>
            <span>Kayıt Ol</span>
        </nav>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="auth-form-container">
            <div class="auth-form-box">
                <h2>Yeni Hesap Oluşturun</h2>
                
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Kullanıcı Adı *</label>
                            <input type="text" id="username" name="username" required 
                                   value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>">
                        </div>
                        <div class="form-group">
                            <label for="email">E-posta Adresi *</label>
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
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Şifre *</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        <div class="form-group">
                            <label for="password_confirm">Şifre Tekrar *</label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Kayıt Ol</button>
                </form>
                
                <div class="auth-footer">
                    <p>Zaten hesabınız var mı? <a href="giris.php">Giriş Yapın</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
