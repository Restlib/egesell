<?php
$pageTitle = 'Site Ayarları';
require_once 'includes/header.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings = [
        'site_name' => trim($_POST['site_name'] ?? ''),
        'site_description' => trim($_POST['site_description'] ?? ''),
        'whatsapp_number' => trim($_POST['whatsapp_number'] ?? ''),
        'phone' => trim($_POST['phone'] ?? ''),
        'email' => trim($_POST['email'] ?? ''),
        'address' => trim($_POST['address'] ?? ''),
        'footer_text' => trim($_POST['footer_text'] ?? '')
    ];
    
    foreach ($settings as $key => $value) {
        $stmt = $db->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?) 
                              ON DUPLICATE KEY UPDATE setting_value = ?");
        $stmt->execute([$key, $value, $value]);
    }
    
    setFlash('success', 'Ayarlar kaydedildi.');
    header('Location: ayarlar.php');
    exit;
}

// Mevcut ayarları al
$stmt = $db->query("SELECT * FROM settings");
$settingsData = [];
while ($row = $stmt->fetch()) {
    $settingsData[$row['setting_key']] = $row['setting_value'];
}
?>

<div class="page-header">
    <h1>Site Ayarları</h1>
</div>

<!-- Flash Messages -->
<?php $flash = getFlash(); ?>
<?php if ($flash): ?>
    <div class="alert alert-<?php echo e($flash['type']); ?>">
        <?php echo e($flash['message']); ?>
    </div>
<?php endif; ?>

<form method="POST" action="" class="admin-form">
    <div class="settings-grid">
        <div class="admin-card">
            <h3>Genel Bilgiler</h3>
            
            <div class="form-group">
                <label for="site_name">Site Adı</label>
                <input type="text" id="site_name" name="site_name" 
                       value="<?php echo e($settingsData['site_name'] ?? 'Egesel Perakende'); ?>">
            </div>
            
            <div class="form-group">
                <label for="site_description">Site Açıklaması</label>
                <textarea id="site_description" name="site_description" rows="3"><?php echo e($settingsData['site_description'] ?? ''); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="footer_text">Footer Metni</label>
                <input type="text" id="footer_text" name="footer_text" 
                       value="<?php echo e($settingsData['footer_text'] ?? ''); ?>">
            </div>
        </div>
        
        <div class="admin-card">
            <h3>İletişim Bilgileri</h3>
            
            <div class="form-group">
                <label for="whatsapp_number">WhatsApp Numarası</label>
                <input type="text" id="whatsapp_number" name="whatsapp_number" 
                       placeholder="905XXXXXXXXX"
                       value="<?php echo e($settingsData['whatsapp_number'] ?? ''); ?>">
                <small>Ülke kodu ile birlikte, başında + olmadan (örn: 905318741028)</small>
            </div>
            
            <div class="form-group">
                <label for="phone">Telefon</label>
                <input type="text" id="phone" name="phone" 
                       value="<?php echo e($settingsData['phone'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="email">E-posta</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo e($settingsData['email'] ?? ''); ?>">
            </div>
            
            <div class="form-group">
                <label for="address">Adres</label>
                <textarea id="address" name="address" rows="2"><?php echo e($settingsData['address'] ?? ''); ?></textarea>
            </div>
        </div>
    </div>
    
    <button type="submit" class="btn btn-primary btn-lg">
        <i class="fas fa-save"></i> Ayarları Kaydet
    </button>
</form>

<?php require_once 'includes/footer.php'; ?>
