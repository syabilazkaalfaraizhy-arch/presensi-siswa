<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$error = '';
$nisn = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');

    if (!preg_match('/^[0-9]{10}$/', $nisn)) {
        $error = 'NISN harus terdiri tepat dari 10 angka.';
    } else {
        $cariSiswa = $db->prepare(
            'SELECT nisn FROM siswa WHERE nisn = :nisn'
        );
        $cariSiswa->execute(['nisn' => $nisn]);

        if (!$cariSiswa->fetch()) {
            $error = 'NISN tidak terdaftar. Periksa kembali kartu siswa.';
        } else {
            // id dan tanggal_waktu diisi otomatis oleh database.
            $simpan = $db->prepare(
                'INSERT INTO presensi (nisn) VALUES (:nisn)'
            );
            $simpan->execute(['nisn' => $nisn]);

            header('Location: tap.php?berhasil=1');
            exit;
        }
    }
}

function escTap(string $nilai): string
{
    return htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Simulasi Tap Kartu</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <h1 class="compact-heading">Simulasi Tap Kartu</h1>
    <a class="back-button" href="../../index.html">&larr; Kembali ke Dashboard</a>

    <?php if (isset($_GET['berhasil'])): ?>
        <p>Presensi berhasil dicatat.</p>
    <?php endif; ?>

    <?php if ($error !== ''): ?>
        <p style="color: red;"><?= escTap($error) ?></p>
    <?php endif; ?>

    <form method="post" class="content-form">
        <label for="nisn">NISN</label>
        <input
            type="text"
            id="nisn"
            name="nisn"
            inputmode="numeric"
            pattern="[0-9]{10}"
            maxlength="10"
            placeholder="Contoh: 0123456789"
            value="<?= escTap($nisn) ?>"
            required
        >
        <button type="submit">Catat Presensi</button>
    </form>
</body>
</html>
