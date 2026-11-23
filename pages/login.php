<?php
declare(strict_types=1);
session_start();

require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/models/User.php';

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
        $modelUser = new User($database->getConnection());
        $user = $modelUser->findByUsername($username);

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

$pageTitle = 'Login Administrator';
$bodyClass = 'login-page';
$showNavbar = false;
$showFooter = false;
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/navbar.php';
?>

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
<?php require_once __DIR__ . '/../includes/footer.php'; ?>
