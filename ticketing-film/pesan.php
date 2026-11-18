<?php
require 'config.php';
login_required();
$judul = 'Pesan Tiket';

$id = (int)($_GET['id'] ?? 0);
$st = $pdo->prepare('SELECT * FROM events WHERE id=?');
$st->execute([$id]);
$ev = $st->fetch();
if (!$ev) { http_response_code(404); die('Event tidak ditemukan.'); }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $jml = (int)($_POST['jumlah'] ?? 0);
    if ($jml < 1 || $jml > 5) {
        $error = 'Jumlah tiket harus 1 sampai 5.';
    } else {
        try {
            $pdo->beginTransaction();
            // Kunci baris event agar kuota aman saat dipesan bersamaan
            $s = $pdo->prepare('SELECT kuota, harga, tanggal FROM events WHERE id=? FOR UPDATE');
            $s->execute([$id]);
            $cur = $s->fetch();
            if (strtotime($cur['tanggal']) < time()) throw new Exception('Event sudah berlalu.');
            if ($cur['kuota'] < $jml) throw new Exception('Kuota tidak mencukupi.');

            $kode = 'TKT-' . strtoupper(bin2hex(random_bytes(4)));
            $pdo->prepare('UPDATE events SET kuota = kuota - ? WHERE id=?')->execute([$jml, $id]);
            $pdo->prepare('INSERT INTO orders(kode,user_id,event_id,jumlah,total) VALUES(?,?,?,?,?)')
                ->execute([$kode, $_SESSION['user']['id'], $id, $jml, $jml * $cur['harga']]);
            $pdo->commit();
            flash('Pemesanan berhasil!');
            header('Location: tiket.php?kode=' . $kode); exit;
        } catch (Exception $ex) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            $error = $ex->getMessage();
        }
    }
}
include 'includes/header.php';
?>
<div class="box">
  <h2><?= e($ev['nama']) ?></h2>
  <p class="muted">📅 <?= tgl($ev['tanggal']) ?><br>📍 <?= e($ev['lokasi']) ?></p>
  <p><?= nl2br(e($ev['deskripsi'])) ?></p>
  <p>Harga: <strong><?= rupiah($ev['harga']) ?></strong> / tiket &middot; Sisa kuota: <strong><?= $ev['kuota'] ?></strong></p>
  <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Jumlah tiket (maks. 5)</label>
    <input type="number" name="jumlah" min="1" max="5" value="1" required>
    <button class="btn btn-block">Pesan Sekarang</button>
  </form>
</div>
<?php include 'includes/footer.php'; ?>
