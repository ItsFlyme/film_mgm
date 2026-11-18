<?php
require '../config.php';
admin_required();
$judul = 'Dashboard Admin';

$stat = [
    'event' => $pdo->query('SELECT COUNT(*) FROM events')->fetchColumn(),
    'tiket' => $pdo->query("SELECT COALESCE(SUM(jumlah),0) FROM orders WHERE status='lunas'")->fetchColumn(),
    'omzet' => $pdo->query("SELECT COALESCE(SUM(total),0) FROM orders WHERE status='lunas'")->fetchColumn(),
    'user'  => $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'")->fetchColumn(),
];
$events = $pdo->query('SELECT * FROM events ORDER BY tanggal DESC')->fetchAll();
$orders = $pdo->query('SELECT o.*, e.nama AS event, u.nama AS pemesan FROM orders o
    JOIN events e ON e.id=o.event_id JOIN users u ON u.id=o.user_id ORDER BY o.created_at DESC LIMIT 50')->fetchAll();
include '../includes/header.php';
?>
<h1>Dashboard Admin</h1>
<div class="stats">
  <div class="stat"><span><?= $stat['event'] ?></span>Event</div>
  <div class="stat"><span><?= $stat['tiket'] ?></span>Tiket Terjual</div>
  <div class="stat"><span><?= rupiah($stat['omzet']) ?></span>Pendapatan</div>
  <div class="stat"><span><?= $stat['user'] ?></span>Pengguna</div>
</div>

<div class="row-between"><h2>Kelola Event</h2><a class="btn btn-sm" href="event_form.php">+ Tambah Event</a></div>
<div class="table-wrap"><table>
  <tr><th>Nama</th><th>Tanggal</th><th>Lokasi</th><th>Harga</th><th>Kuota</th><th></th></tr>
  <?php foreach ($events as $ev): ?>
  <tr>
    <td><?= e($ev['nama']) ?></td>
    <td><?= tgl($ev['tanggal']) ?></td>
    <td><?= e($ev['lokasi']) ?></td>
    <td><?= rupiah($ev['harga']) ?></td>
    <td><?= $ev['kuota'] ?></td>
    <td class="actions">
      <a class="btn btn-sm" href="event_form.php?id=<?= $ev['id'] ?>">Edit</a>
      <form method="post" action="event_hapus.php" onsubmit="return confirm('Hapus event beserta semua pesanannya?')">
        <button class="btn btn-sm btn-danger" name="id" value="<?= $ev['id'] ?>">Hapus</button>
      </form>
    </td>
  </tr>
  <?php endforeach; ?>
</table></div>

<h2>Pesanan Terbaru</h2>
<div class="table-wrap"><table>
  <tr><th>Kode</th><th>Pemesan</th><th>Event</th><th>Jml</th><th>Total</th><th>Status</th><th>Waktu</th></tr>
  <?php foreach ($orders as $o): ?>
  <tr>
    <td><?= e($o['kode']) ?></td><td><?= e($o['pemesan']) ?></td><td><?= e($o['event']) ?></td>
    <td><?= $o['jumlah'] ?></td><td><?= rupiah($o['total']) ?></td>
    <td><span class="badge <?= $o['status'] === 'lunas' ? 'badge-ok' : 'badge-err' ?>"><?= $o['status'] ?></span></td>
    <td><?= tgl($o['created_at']) ?></td>
  </tr>
  <?php endforeach; ?>
  <?php if (!$orders): ?><tr><td colspan="7" class="muted">Belum ada pesanan.</td></tr><?php endif; ?>
</table></div>
<?php include '../includes/footer.php'; ?>
