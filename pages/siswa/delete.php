<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

function escHapusSiswa(string $nilai): string
{
    return htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8');
}

function tampilkanPesan(string $pesan): void
{
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Hapus Siswa</title>
        <link rel="stylesheet" href="../../style.css">
    </head>
    <body>
        <h1>Hapus Siswa</h1>
        <p><?= escHapusSiswa($pesan) ?></p>
        <a href="index.php">Kembali ke daftar siswa</a>
    </body>
    </html>
    <?php
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
$db = $database->getConnection();

$cek = $db->prepare(
    'SELECT COUNT(*) FROM presensi WHERE nisn = :nisn'
);
$cek->execute(['nisn' => $nisn]);

if ((int) $cek->fetchColumn() > 0) {
    tampilkanPesan(
        'Siswa tidak dapat dihapus karena masih memiliki log presensi. Hapus log presensinya terlebih dahulu.'
    );
}

try {
    $hapus = $db->prepare(
        'DELETE FROM siswa WHERE nisn = :nisn'
    );
    $hapus->execute(['nisn' => $nisn]);
} catch (PDOException $e) {
    tampilkanPesan('Data siswa gagal dihapus. Silakan coba lagi.');
}

header('Location: index.php');
exit;
