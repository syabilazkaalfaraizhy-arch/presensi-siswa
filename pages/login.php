<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../app/Core/Database.php';

if (isset($_SESSION['id_user'])) {
    header('Location: presensi/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    try {
        $database = new Database();
        $db = $database->getConnection();

        $query = $db->prepare(
            'SELECT id_user, username, password
            FROM users
            WHERE username = :username
            LIMIT 1'
        );
        $query->execute(['username' => $username]);
        $user = $query->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];

            header('Location: presensi/index.php');
            exit;
        }

        // Pesan sama untuk username yang tidak ditemukan atau password keliru.
        $error = 'Username atau password tidak sesuai.';
    } catch (PDOException $e) {
        $error = 'Tidak bisa terhubung ke database. Periksa pengaturannya.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body class="login-page">
    <main class="login-wrapper">
      <form method="post" class="login-card">
        <h1>Login Administrator</h1>

        <?php if ($error !== ''): ?>
            <p class="login-error">
                <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>

        <label for="username">Username</label>
        <input
            type="text"
            id="username"
            name="username"
            autocomplete="username"
            placeholder="Masukkan username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            placeholder="Masukkan password"
            required
        >

        <button type="submit">Login</button>
      </form>
    </main>
</body>
</html>
