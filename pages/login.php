<?php
declare(strict_types=1);
session_start();

if (isset($_SESSION['id_user'])) {
    header('Location: dashboard.php');
    exit;
}

$pageTitle = 'Login Administrator';
require_once __DIR__ . '/../includes/header.php';
?>
<section class="card">
    <h2>Login Administrator</h2>
    <p>Form login akan diproses dengan password_verify().</p>
    <!-- TODO: tambahkan validasi server-side dan prepared statement. -->
    <form method="post" action="">
        <label for="username">Username</label>
        <input id="username" name="username" type="text" autocomplete="username" required>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
        <button type="submit">Masuk</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>