<?php
require 'config.php';
login_required();
$judul = 'E-Tiket';
$st = $pdo->prepare('SELECT o.*, e.nama AS event, e.lokasi, e.tanggal, u.nama AS pemesan
    FROM orders o JOIN events e ON e.id=o.event_id JOIN users u ON u.id=o.user_id
    WHERE o.kode=? AND (o.user_id=? OR ?="admin")');
$st->execute([$_GET['kode'] ?? '', $_SESSION['user']['id'], $_SESSION['user']['role']]);
$t = $st->fetch();
if (!$t) { http_response_code(404); die('Tiket tidak ditemukan.'); }
include 'includes/header.php';
?>
<div class="ticket">
  <div class="ticket-head">E-TIKET <span class="badge <?= $t['status'] === 'lunas' ? 'badge-ok' : 'badge-err' ?>"><?= strtoupper($t['status']) ?></span></div>
  <h2><?= e($t['event']) ?></h2>
  <table class="info">
    <tr><td>Kode Booking</td><td><strong class="kode"><?= e($t['kode']) ?></strong></td></tr>
    <tr><td>Pemesan</td><td><?= e($t['pemesan']) ?></td></tr>
    <tr><td>Tanggal</td><td><?= tgl($t['tanggal']) ?></td></tr>
    <tr><td>Lokasi</td><td><?= e($t['lokasi']) ?></td></tr>
    <tr><td>Jumlah</td><td><?= $t['jumlah'] ?> tiket</td></tr>
    <tr><td>Total</td><td><?= rupiah($t['total']) ?></td></tr>
  </table>
  <p class="muted">Tunjukkan kode booking ini kepada petugas saat masuk.</p>
  <button class="btn no-print" onclick="window.print()">🖨️ Cetak Tiket</button>
  <a class="btn btn-ghost no-print" href="riwayat.php">Kembali</a>
</div>
<?php include 'includes/footer.php'; ?>
