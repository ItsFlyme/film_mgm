<?php
require '../config.php';
admin_required();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->prepare('DELETE FROM events WHERE id=?')->execute([(int)($_POST['id'] ?? 0)]);
    flash('Event dihapus.');
}
header('Location: index.php');
