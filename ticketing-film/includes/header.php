<?php require_once __DIR__ . '/../config.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($judul ?? 'Tiketku') ?> - Tiketku</title>
<link rel="stylesheet" href="<?= BASE_URL ?>assets/style.css">
</head>
<body>
<header class="nav">
  <div class="container nav-in">
    <a class="brand" href="<?= BASE_URL ?>">🎟️ Tiketku</a>
    <nav>
      <a href="<?= BASE_URL ?>">Event</a>
      <?php if (!empty($_SESSION['user'])): ?>
        <a href="<?= BASE_URL ?>riwayat.php">Tiket Saya</a>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
          <a href="<?= BASE_URL ?>admin/">Admin</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>logout.php">Keluar (<?= e($_SESSION['user']['nama']) ?>)</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>login.php">Masuk</a>
        <a class="btn btn-sm" href="<?= BASE_URL ?>register.php">Daftar</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<main class="container">
<?php if (!empty($_SESSION['flash'])): $f = $_SESSION['flash']; unset($_SESSION['flash']); ?>
  <div class="alert <?= $f['type'] === 'err' ? 'alert-err' : 'alert-ok' ?>"><?= e($f['msg']) ?></div>
<?php endif; ?>
