<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$nisnLama = trim($_GET['nisn'] ?? '');

if ($nisnLama === '') {
    header('Location: index.php');
    exit;
}

$cari = $db->prepare(
    'SELECT nisn, nama, alamat, id_kelas
    FROM siswa
    WHERE nisn = :nisn'
);
$cari->execute(['nisn' => $nisnLama]);
$siswa = $cari->fetch(PDO::FETCH_ASSOC);

if (!$siswa) {
    header('Location: index.php');
    exit;
}

$kelas = $db->query(
    'SELECT id_kelas, nama_kelas
    FROM kelas
    ORDER BY nama_kelas'
)->fetchAll(PDO::FETCH_ASSOC);

$error = '';
$nisn = $siswa['nisn'];
$nama = $siswa['nama'];
$alamat = $siswa['alamat'] ?? '';
$idKelas = (string) $siswa['id_kelas'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $idKelas = trim($_POST['id_kelas'] ?? '');

    if (!preg_match('/^[0-9]{10}$/', $nisn)) {
        $error = 'NISN harus terdiri tepat dari 10 angka.';
    } elseif ($nama === '') {
        $error = 'Nama siswa wajib diisi.';
    } elseif ($idKelas === '') {
        $error = 'Pilih kelas siswa.';
    } else {
        $cekKelas = $db->prepare(
            'SELECT COUNT(*) FROM kelas WHERE id_kelas = :id_kelas'
        );
        $cekKelas->execute(['id_kelas' => $idKelas]);

        if ((int) $cekKelas->fetchColumn() === 0) {
            $error = 'Kelas yang dipilih tidak tersedia.';
        } else {
            $cekNisn = $db->prepare(
                'SELECT COUNT(*)
                FROM siswa
                WHERE nisn = :nisn
                AND nisn <> :nisn_lama'
            );
            $cekNisn->execute([
                'nisn' => $nisn,
                'nisn_lama' => $nisnLama,
            ]);

            if ((int) $cekNisn->fetchColumn() > 0) {
                $error = 'NISN sudah digunakan siswa lain.';
            } else {
                try {
                    $ubah = $db->prepare(
                        'UPDATE siswa
                        SET nisn = :nisn,
                            nama = :nama,
                            alamat = :alamat,
                            id_kelas = :id_kelas
                        WHERE nisn = :nisn_lama'
                    );
                    $ubah->execute([
                        'nisn' => $nisn,
                        'nama' => $nama,
                        'alamat' => $alamat === '' ? null : $alamat,
                        'id_kelas' => $idKelas,
                        'nisn_lama' => $nisnLama,
                    ]);

                    header('Location: index.php');
                    exit;
                } catch (PDOException $e) {
                    $error = 'Data gagal diperbarui. NISN mungkin masih dipakai pada log presensi.';
                }
            }
        }
    }
}

function escEditSiswa(string|null $nilai): string
{
    return htmlspecialchars($nilai ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Siswa</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <h1>Ubah Siswa</h1>

    <?php if ($error !== ''): ?>
        <p style="color: red;">
            <?= escEditSiswa($error) ?>
        </p>
    <?php endif; ?>

    <form method="post">
        <label for="nisn">NISN (10 angka)</label>
        <input
            type="text"
            id="nisn"
            name="nisn"
            inputmode="numeric"
            pattern="[0-9]{10}"
            maxlength="10"
            placeholder="Contoh: 0123456789"
            value="<?= escEditSiswa($nisn) ?>"
            required
        >

        <br><br>

        <label for="nama">Nama</label>
        <input
            type="text"
            id="nama"
            name="nama"
            maxlength="100"
            placeholder="Contoh: Andi Pratama"
            value="<?= escEditSiswa($nama) ?>"
            required
        >

        <br><br>

        <label for="alamat">Alamat (boleh kosong)</label>
        <input
            type="text"
            id="alamat"
            name="alamat"
            maxlength="255"
            placeholder="Contoh: Jl. Merdeka No. 10"
            value="<?= escEditSiswa($alamat) ?>"
        >

        <br><br>

        <label for="id_kelas">Kelas</label>
        <select id="id_kelas" name="id_kelas" required>
            <option value="">Pilih kelas</option>

            <?php foreach ($kelas as $barisKelas): ?>
                <option
                    value="<?= escEditSiswa((string) $barisKelas['id_kelas']) ?>"
                    <?= $idKelas === (string) $barisKelas['id_kelas'] ? 'selected' : '' ?>
                >
                    <?= escEditSiswa($barisKelas['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <br><br>

        <button type="submit">Simpan Perubahan</button>
        <a class="back-button" href="index.php">Kembali ke Daftar Siswa</a>
    </form>
</body>
</html>
