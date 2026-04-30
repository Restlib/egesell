<?php
session_start();

// Admin oturumunu sonlandır
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_name']);

// Tüm oturumu temizle
session_destroy();

// Giriş sayfasına yönlendir
header('Location: giris.php');
exit;
