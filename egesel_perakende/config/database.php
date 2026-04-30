<?php
/**
 * Egesel Perakende - Veritabanı Bağlantı Yapılandırması
 * Bu dosyayı kendi sunucu bilgilerinize göre düzenleyin
 */

// Veritabanı bilgileri - cPanel'den alacağınız bilgileri buraya girin
define('DB_HOST', 'localhost');
define('DB_NAME', 'egesel_perakende'); // cPanel'de oluşturduğunuz veritabanı adı
define('DB_USER', 'root'); // cPanel veritabanı kullanıcı adı
define('DB_PASS', ''); // cPanel veritabanı şifresi
define('DB_CHARSET', 'utf8mb4');

// Site ayarları
define('SITE_URL', 'http://localhost/egesel_perakende'); // Sitenizin URL'si
define('SITE_NAME', 'Egesel Perakende');
define('WHATSAPP_NUMBER', '905318741028'); // WhatsApp numarası (ülke kodu ile)

// Güvenlik
define('SECURE_KEY', 'egesel_perakende_2024_secure_key'); // Değiştirin!

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Hata raporlama (Geliştirme için açık, canlıda kapatın)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PDO Veritabanı bağlantısı
class Database {
    private static $instance = null;
    private $connection;
    
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
}

// Veritabanı bağlantısını al
function getDB() {
    return Database::getInstance()->getConnection();
}
?>
