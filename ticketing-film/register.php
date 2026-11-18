<?php
require 'config.php';
$judul = 'Daftar';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    if ($nama === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($pass) < 6) {
        $error = 'Isi nama, email valid, dan password minimal 6 karakter.';
    } else {
        $cek = $pdo->prepare('SELECT id FROM users WHERE email=?');
        $cek->execute([$email]);
        if ($cek->fetch()) {
            $error = 'Email sudah terdaftar.';
        } else {
            $pdo->prepare('INSERT INTO users(nama,email,password) VALUES(?,?,?)')
                ->execute([$nama, $email, password_hash($pass, PASSWORD_DEFAULT)]);
            flash('Pendaftaran berhasil, silakan masuk.');
            header('Location: login.php'); exit;
        }
    }
}
include 'includes/header.php';
?>
<div class="box">
  <h2>Daftar Akun</h2>
  <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Nama lengkap</label><input type="text" name="nama" value="<?= e($_POST['nama'] ?? '') ?>" required>
    <label>Email</label><input type="email" name="email" value="<?= e($_POST['email'] ?? '') ?>" required>
    <label>Password</label><input type="password" name="password" required>
    <button class="btn btn-block">Daftar</button>
  </form>
  <p class="muted">Sudah punya akun? <a href="login.php">Masuk</a></p>
</div>
<?php include 'includes/footer.php'; ?>
