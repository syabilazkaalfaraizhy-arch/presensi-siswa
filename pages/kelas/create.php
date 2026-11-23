<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';
$namaKelas = '';
$jurusan = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaKelas = trim($_POST['nama_kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if ($namaKelas === '' || $jurusan === '') {
        $error = 'Nama kelas dan jurusan wajib diisi.';
    } else {
        // Cek supaya nama kelas tidak kembar.
        $cek = $db->prepare(
            'SELECT COUNT(*) FROM kelas WHERE nama_kelas = :nama_kelas'
        );
        $cek->execute(['nama_kelas' => $namaKelas]);

        if ((int) $cek->fetchColumn() > 0) {
            $error = 'Nama kelas sudah digunakan. Masukkan nama lain.';
        } else {
            $simpan = $db->prepare(
                'INSERT INTO kelas (nama_kelas, jurusan)
                VALUES (:nama_kelas, :jurusan)'
            );
            $simpan->execute([
                'nama_kelas' => $namaKelas,
                'jurusan' => $jurusan,
            ]);

            header('Location: index.php');
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Kelas</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <h1>Tambah Kelas</h1>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <label for="nama_kelas">Nama Kelas</label>
        <input
            type="text"
            id="nama_kelas"
            name="nama_kelas"
            maxlength="20"
            placeholder="Contoh: XI PPLG"
            value="<?= htmlspecialchars($namaKelas, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <br><br>

        <label for="jurusan">Jurusan</label>
        <input
            type="text"
            id="jurusan"
            name="jurusan"
            maxlength="50"
            placeholder="Contoh: PPLG"
            value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <br><br>

        <button type="submit">Simpan</button>
        <a class="back-button" href="index.php">Kembali ke Daftar Kelas</a>
    </form>
</body>
</html>
