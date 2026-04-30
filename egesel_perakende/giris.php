<?php
$pageTitle = 'Giriş Yap';
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Zaten giriş yapmışsa
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Lütfen tüm alanları doldurun.';
    } else {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Oturum başlat
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'] ?? $user['username'];
            $_SESSION['user_role'] = $user['role'];
            
            // Session sepetini veritabanına aktar
            if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $productId => $quantity) {
                    $stmt = $db->prepare("INSERT INTO cart_items (user_id, product_id, quantity) VALUES (?, ?, ?)
                                          ON DUPLICATE KEY UPDATE quantity = quantity + ?");
                    $stmt->execute([$user['id'], $productId, $quantity, $quantity]);
                }
                unset($_SESSION['cart']);
            }
            
            setFlash('success', 'Hoş geldiniz, ' . e($_SESSION['user_name']) . '!');
            
            // Admin ise admin paneline yönlendir
            if ($user['role'] === 'admin') {
                header('Location: admin/');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Kullanıcı adı veya şifre hatalı.';
        }
    }
}

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <h1>Giriş Yap</h1>
        <nav class="breadcrumb">
            <a href="<?php echo SITE_URL; ?>">Ana Sayfa</a>
            <span>/</span>
            <span>Giriş Yap</span>
        </nav>
    </div>
</section>

<section class="auth-section">
    <div class="container">
        <div class="auth-form-container">
            <div class="auth-form-box">
                <h2>Hesabınıza Giriş Yapın</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo e($error); ?></div>
                <?php endif; ?>
                
                <form method="POST" action="" class="auth-form">
                    <div class="form-group">
                        <label for="username">Kullanıcı Adı veya E-posta</label>
                        <input type="text" id="username" name="username" required 
                               value="<?php echo isset($_POST['username']) ? e($_POST['username']) : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Şifre</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Giriş Yap</button>
                </form>
                
                <div class="auth-footer">
                    <p>Hesabınız yok mu? <a href="kayit.php">Kayıt Olun</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
