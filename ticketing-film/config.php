<?php
session_start();

// --- Konfigurasi database XAMPP (default) ---
$DB_HOST = 'localhost';
$DB_NAME = 'db_ticketing';
$DB_USER = 'root';
$DB_PASS = '';

// Folder project di htdocs (ubah jika nama folder berbeda)
define('BASE_URL', '/ticketing/');

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Koneksi database gagal. Pastikan MySQL di XAMPP aktif dan database sudah di-import. (' . $e->getMessage() . ')');
}

function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function rupiah($n) { return 'Rp ' . number_format((int)$n, 0, ',', '.'); }
function tgl($d) { return date('d M Y, H:i', strtotime($d)); }

function flash($msg, $type = 'ok') { $_SESSION['flash'] = ['msg' => $msg, 'type' => $type]; }

function login_required() {
    if (empty($_SESSION['user'])) { header('Location: ' . BASE_URL . 'login.php'); exit; }
}
function admin_required() {
    if (empty($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: ' . BASE_URL . 'login.php'); exit;
    }
}
