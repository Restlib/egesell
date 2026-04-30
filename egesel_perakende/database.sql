-- =====================================================
-- EGESEL PERAKENDE - VERİTABANI ŞEMASI
-- phpMyAdmin / cPanel İçin SQL Dosyası
-- =====================================================

-- Veritabanı oluştur (cPanel'de manuel oluşturmanız gerekebilir)
CREATE DATABASE IF NOT EXISTS egesel_perakende CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE egesel_perakende;

-- =====================================================
-- TABLOLAR
-- =====================================================

-- Kullanıcılar Tablosu
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    phone VARCHAR(20),
    role ENUM('admin', 'user') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Kategoriler Tablosu
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    parent_id INT DEFAULT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ürünler Tablosu
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    short_description VARCHAR(500),
    price DECIMAL(10, 2) NOT NULL,
    old_price DECIMAL(10, 2) DEFAULT NULL,
    sku VARCHAR(50) UNIQUE,
    stock INT DEFAULT 0,
    category_id INT,
    image VARCHAR(255),
    images TEXT,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    view_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sepet Öğeleri Tablosu
CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cart_item (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Favoriler Tablosu
CREATE TABLE IF NOT EXISTS favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_favorite (user_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Site Ayarları Tablosu
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- VARSAYILAN VERİLER
-- =====================================================

-- Admin Kullanıcısı (Şifre: admin - bcrypt hash)
INSERT INTO users (username, email, password, full_name, role) VALUES
('admin', 'admin@egeselperakende.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Site Yöneticisi', 'admin');

-- Örnek Kategoriler
INSERT INTO categories (name, slug, description, sort_order) VALUES
('Mobilya Kulpları', 'mobilya-kulplari', 'Dolap ve çekmece kulpları', 1),
('Menteşeler', 'menteseler', 'Kapak ve kapı menteşeleri', 2),
('Raylar ve Mekanizmalar', 'raylar-mekanizmalar', 'Çekmece rayları ve mekanizmaları', 3),
('Vidalar ve Bağlantı', 'vidalar-baglanti', 'Vida, dübel ve bağlantı elemanları', 4),
('Kilit Sistemleri', 'kilit-sistemleri', 'Mobilya kilitleri ve kilit aksesuarları', 5),
('Ayaklar ve Tekerlekler', 'ayaklar-tekerlekler', 'Mobilya ayakları ve tekerlekleri', 6);

-- Örnek Ürünler
INSERT INTO products (name, slug, description, short_description, price, old_price, sku, stock, category_id, is_featured) VALUES
('Modern Mat Siyah Kulp 128mm', 'modern-mat-siyah-kulp-128mm', 'Yüksek kaliteli mat siyah kaplama, 128mm delik aralığı. Mutfak ve banyo dolapları için ideal. Paslanmaz çelik malzeme, uzun ömürlü kullanım.', 'Mat siyah modern tasarım kulp', 45.00, 55.00, 'KLP-001', 150, 1, 1),
('Paslanmaz Çelik T-Bar Kulp 160mm', 'paslanmaz-celik-t-bar-kulp-160mm', 'Premium paslanmaz çelik T-bar kulp. 160mm delik aralığı. Modern mutfaklar için mükemmel seçim.', 'T-Bar paslanmaz çelik kulp', 65.00, NULL, 'KLP-002', 200, 1, 1),
('Soft Close Menteşe 35mm', 'soft-close-mentese-35mm', 'Sessiz kapanış özellikli 35mm kap menteşesi. 110 derece açılım. Kolay montaj.', 'Soft close dolap menteşesi', 25.00, 30.00, 'MNT-001', 500, 2, 1),
('Tam Açılım Çekmece Rayı 45cm', 'tam-acilim-cekmece-rayi-45cm', 'Tam açılım özellikli ball bearing çekmece rayı. 45cm uzunluk, 45kg taşıma kapasitesi.', 'Ball bearing çekmece rayı', 85.00, NULL, 'RAY-001', 100, 3, 0),
('Mobilya Vidası Seti 500 Parça', 'mobilya-vidasi-seti-500-parca', '500 parçalık mobilya vida seti. Farklı boyutlarda vidalar, dübellerve konfirmat vidaları içerir.', 'Kapsamlı vida seti', 120.00, 150.00, 'VDA-001', 75, 4, 1),
('Push-Lock Mobilya Kilidi', 'push-lock-mobilya-kilidi', 'Basmalı açılış sistemi. Anahtarsız kullanım. Modern mobilyalar için ideal.', 'Push-lock kilit sistemi', 35.00, NULL, 'KLT-001', 200, 5, 0),
('Ayarlanabilir Mobilya Ayağı 10cm', 'ayarlanabilir-mobilya-ayagi-10cm', 'Yükseklik ayarlı krom mobilya ayağı. 8-12cm arası ayarlanabilir. 4lü set.', 'Ayarlanabilir krom ayak seti', 95.00, 110.00, 'AYK-001', 120, 6, 1),
('Gizli Menteşe 35mm Nikel', 'gizli-mentese-35mm-nikel', 'Gizli montaj menteşesi nikel kaplama. Şık görünüm için ideal.', 'Nikel gizli menteşe', 32.00, NULL, 'MNT-002', 300, 2, 0);

-- Site Ayarları
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'Egesel Perakende'),
('site_description', 'Mobilya Aksesuarları ve Hırdavat'),
('whatsapp_number', '905318741028'),
('phone', '+90 531 874 10 28'),
('email', 'info@egeselperakende.com'),
('address', 'Ege Bölgesi, Türkiye'),
('footer_text', '© 2024 Egesel Perakende. Tüm hakları saklıdır.');

-- =====================================================
-- İNDEKSLER
-- =====================================================

CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_featured ON products(is_featured);
CREATE INDEX idx_products_active ON products(is_active);
CREATE INDEX idx_cart_user ON cart_items(user_id);
CREATE INDEX idx_favorites_user ON favorites(user_id);
CREATE INDEX idx_categories_parent ON categories(parent_id);
