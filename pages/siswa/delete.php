<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Siswa.php';

function escHapusSiswa(string $nilai): string
{
    return htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8');
}

function tampilkanPesan(string $pesan): void
{
    $pageTitle = 'Hapus Siswa';
    require_once __DIR__ . '/../../includes/header.php';
    require_once __DIR__ . '/../../includes/navbar.php';
    ?>
    <h1>Hapus Siswa</h1>
    <p><?= escHapusSiswa($pesan) ?></p>
    <a class="back-button" href="index.php">Kembali ke Daftar Siswa</a>
    <?php
    require_once __DIR__ . '/../../includes/footer.php';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$nisn = trim($_POST['nisn'] ?? '');

if (!preg_match('/^[0-9]{10}$/', $nisn)) {
    tampilkanPesan('NISN tidak valid.');
}

$database = new Database();
$modelSiswa = new Siswa($database->getConnection());

if ($modelSiswa->hasPresensi($nisn)) {
    tampilkanPesan(
        'Siswa tidak dapat dihapus karena masih memiliki log presensi. Hapus log presensinya terlebih dahulu.'
    );
}

try {
    $modelSiswa->delete($nisn);
} catch (PDOException $e) {
    tampilkanPesan('Data siswa gagal dihapus. Silakan coba lagi.');
}

header('Location: index.php');
exit;
