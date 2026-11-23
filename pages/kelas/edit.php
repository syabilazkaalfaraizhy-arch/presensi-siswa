<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$cari = $db->prepare(
    'SELECT id_kelas, nama_kelas, jurusan
    FROM kelas
    WHERE id_kelas = :id'
);
$cari->execute(['id' => $id]);
$kelas = $cari->fetch();

if (!$kelas) {
    header('Location: index.php');
    exit;
}

$error = '';
$namaKelas = $kelas['nama_kelas'];
$jurusan = $kelas['jurusan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaKelas = trim($_POST['nama_kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if ($namaKelas === '' || $jurusan === '') {
        $error = 'Nama kelas dan jurusan wajib diisi.';
    } else {
        $cek = $db->prepare(
            'SELECT COUNT(*)
            FROM kelas
            WHERE nama_kelas = :nama_kelas
            AND id_kelas <> :id'
        );
        $cek->execute([
            'nama_kelas' => $namaKelas,
            'id' => $id,
        ]);

        if ((int) $cek->fetchColumn() > 0) {
            $error = 'Nama kelas sudah digunakan. Masukkan nama lain.';
        } else {
            try {
                $ubah = $db->prepare(
                    'UPDATE kelas
                    SET nama_kelas = :nama_kelas,
                    jurusan = :jurusan
                    WHERE id_kelas = :id'
                );
                $ubah->execute([
                    'nama_kelas' => $namaKelas,
                    'jurusan' => $jurusan,
                    'id' => $id,
                ]);

                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $error = 'Data kelas gagal diperbarui. Periksa kembali nama kelas.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Kelas</title>
</head>
<body>
    <h1>Ubah Kelas</h1>

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
            value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"
            required
        >

        <br><br>

        <button type="submit">Simpan Perubahan</button>
        <a href="index.php">Kembali</a>
    </form>
</body>
</html>