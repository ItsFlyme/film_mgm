<?php
require 'config.php';
$judul = 'Daftar Event';
$q = trim($_GET['q'] ?? '');
$st = $pdo->prepare("SELECT * FROM events WHERE tanggal >= NOW() AND (nama LIKE ? OR lokasi LIKE ?) ORDER BY tanggal ASC");
$st->execute(["%$q%", "%$q%"]);
$events = $st->fetchAll();
include 'includes/header.php';
?>
<h1>Event Mendatang</h1>
<form class="search" method="get">
  <input type="text" name="q" value="<?= e($q) ?>" placeholder="Cari event atau lokasi...">
  <button class="btn">Cari</button>
</form>

<?php if (!$events): ?>
  <p class="muted">Belum ada event yang tersedia.</p>
<?php endif; ?>

<div class="grid">
<?php foreach ($events as $ev): ?>
  <div class="card">
    <h3><?= e($ev['nama']) ?></h3>
    <p class="muted">📅 <?= tgl($ev['tanggal']) ?><br>📍 <?= e($ev['lokasi']) ?></p>
    <p><?= e(mb_strimwidth($ev['deskripsi'], 0, 110, '...')) ?></p>
    <div class="card-foot">
      <strong><?= $ev['harga'] > 0 ? rupiah($ev['harga']) : 'Gratis' ?></strong>
      <?php if ($ev['kuota'] > 0): ?>
        <a class="btn btn-sm" href="pesan.php?id=<?= $ev['id'] ?>">Pesan</a>
      <?php else: ?>
        <span class="badge badge-err">Habis</span>
      <?php endif; ?>
    </div>
    <small class="muted">Sisa kuota: <?= $ev['kuota'] ?></small>
  </div>
<?php endforeach; ?>
</div>
<?php include 'includes/footer.php'; ?>
