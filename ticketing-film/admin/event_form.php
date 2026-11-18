<?php
require '../config.php';
admin_required();

$id = (int)($_GET['id'] ?? 0);
$ev = ['nama' => '', 'deskripsi' => '', 'lokasi' => '', 'tanggal' => '', 'harga' => 0, 'kuota' => 0];
if ($id) {
    $st = $pdo->prepare('SELECT * FROM events WHERE id=?');
    $st->execute([$id]);
    $row = $st->fetch();
    if (!$row) die('Event tidak ditemukan.');
    $ev = $row;
}
$judul = $id ? 'Edit Event' : 'Tambah Event';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d = [
        trim($_POST['nama'] ?? ''), trim($_POST['deskripsi'] ?? ''), trim($_POST['lokasi'] ?? ''),
        str_replace('T', ' ', $_POST['tanggal'] ?? ''), max(0, (int)($_POST['harga'] ?? 0)), max(0, (int)($_POST['kuota'] ?? 0)),
    ];
    if ($d[0] === '' || $d[2] === '' || $d[3] === '') {
        $error = 'Nama, lokasi, dan tanggal wajib diisi.';
        $ev = array_combine(['nama', 'deskripsi', 'lokasi', 'tanggal', 'harga', 'kuota'], $d);
    } else {
        if ($id) {
            $pdo->prepare('UPDATE events SET nama=?,deskripsi=?,lokasi=?,tanggal=?,harga=?,kuota=? WHERE id=?')
                ->execute(array_merge($d, [$id]));
        } else {
            $pdo->prepare('INSERT INTO events(nama,deskripsi,lokasi,tanggal,harga,kuota) VALUES(?,?,?,?,?,?)')
                ->execute($d);
        }
        flash('Event berhasil disimpan.');
        header('Location: index.php'); exit;
    }
}
include '../includes/header.php';
?>
<div class="box box-wide">
  <h2><?= $judul ?></h2>
  <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Nama event</label><input type="text" name="nama" value="<?= e($ev['nama']) ?>" required>
    <label>Deskripsi</label><textarea name="deskripsi" rows="4"><?= e($ev['deskripsi']) ?></textarea>
    <label>Lokasi</label><input type="text" name="lokasi" value="<?= e($ev['lokasi']) ?>" required>
    <label>Tanggal &amp; waktu</label>
    <input type="datetime-local" name="tanggal" value="<?= $ev['tanggal'] ? date('Y-m-d\TH:i', strtotime($ev['tanggal'])) : '' ?>" required>
    <label>Harga (Rp, isi 0 untuk gratis)</label><input type="number" name="harga" min="0" value="<?= (int)$ev['harga'] ?>">
    <label>Kuota tiket tersedia</label><input type="number" name="kuota" min="0" value="<?= (int)$ev['kuota'] ?>">
    <button class="btn">Simpan</button>
    <a class="btn btn-ghost" href="index.php">Batal</a>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
