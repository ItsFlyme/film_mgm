<?php
require 'config.php';
login_required();
$judul = 'Tiket Saya';
$uid = $_SESSION['user']['id'];

// Pembatalan pesanan (hanya jika event belum berlangsung)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batal'])) {
    try {
        $pdo->beginTransaction();
        $s = $pdo->prepare('SELECT o.*, e.tanggal FROM orders o JOIN events e ON e.id=o.event_id
                            WHERE o.id=? AND o.user_id=? FOR UPDATE');
        $s->execute([(int)$_POST['batal'], $uid]);
        $o = $s->fetch();
        if (!$o || $o['status'] !== 'lunas') throw new Exception('Pesanan tidak dapat dibatalkan.');
        if (strtotime($o['tanggal']) < time()) throw new Exception('Event sudah berlalu.');
        $pdo->prepare("UPDATE orders SET status='batal' WHERE id=?")->execute([$o['id']]);
        $pdo->prepare('UPDATE events SET kuota = kuota + ? WHERE id=?')->execute([$o['jumlah'], $o['event_id']]);
        $pdo->commit();
        flash('Pesanan dibatalkan.');
    } catch (Exception $ex) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        flash($ex->getMessage(), 'err');
    }
    header('Location: riwayat.php'); exit;
}

$st = $pdo->prepare('SELECT o.*, e.nama AS event, e.tanggal FROM orders o
                     JOIN events e ON e.id=o.event_id WHERE o.user_id=? ORDER BY o.created_at DESC');
$st->execute([$uid]);
$orders = $st->fetchAll();
include 'includes/header.php';
?>
<h1>Tiket Saya</h1>
<?php if (!$orders): ?><p class="muted">Belum ada pesanan. <a href="index.php">Cari event</a></p><?php else: ?>
<div class="table-wrap"><table>
  <tr><th>Kode</th><th>Event</th><th>Tanggal</th><th>Jml</th><th>Total</th><th>Status</th><th></th></tr>
  <?php foreach ($orders as $o): ?>
  <tr>
    <td><?= e($o['kode']) ?></td>
    <td><?= e($o['event']) ?></td>
    <td><?= tgl($o['tanggal']) ?></td>
    <td><?= $o['jumlah'] ?></td>
    <td><?= rupiah($o['total']) ?></td>
    <td><span class="badge <?= $o['status'] === 'lunas' ? 'badge-ok' : 'badge-err' ?>"><?= $o['status'] ?></span></td>
    <td class="actions">
      <a class="btn btn-sm" href="tiket.php?kode=<?= e($o['kode']) ?>">Lihat</a>
      <?php if ($o['status'] === 'lunas' && strtotime($o['tanggal']) > time()): ?>
      <form method="post" onsubmit="return confirm('Batalkan pesanan ini?')">
        <button class="btn btn-sm btn-danger" name="batal" value="<?= $o['id'] ?>">Batal</button>
      </form>
      <?php endif; ?>
    </td>
  </tr>
  <?php endforeach; ?>
</table></div>
<?php endif; ?>
<?php include 'includes/footer.php'; ?>
