<?php
require 'config.php';
$judul = 'Masuk';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = $pdo->prepare('SELECT * FROM users WHERE email=?');
    $st->execute([trim($_POST['email'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id' => $u['id'], 'nama' => $u['nama'], 'role' => $u['role']];
        header('Location: ' . ($u['role'] === 'admin' ? 'admin/' : 'index.php')); exit;
    }
    $error = 'Email atau password salah.';
}
include 'includes/header.php';
?>
<div class="box">
  <h2>Masuk</h2>
  <?php if ($error): ?><div class="alert alert-err"><?= e($error) ?></div><?php endif; ?>
  <form method="post">
    <label>Email</label><input type="email" name="email" required>
    <label>Password</label><input type="password" name="password" required>
    <button class="btn btn-block">Masuk</button>
  </form>
  <p class="muted">Belum punya akun? <a href="register.php">Daftar</a></p>
</div>
<?php include 'includes/footer.php'; ?>
