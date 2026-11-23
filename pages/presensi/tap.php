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

$riwayatTap = $db->query(
    'SELECT siswa.nama, kelas.nama_kelas, presensi.tanggal_waktu
    FROM presensi
    INNER JOIN siswa ON presensi.nisn = siswa.nisn
    INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
    ORDER BY presensi.tanggal_waktu DESC, presensi.id DESC
    LIMIT 30'
)->fetchAll(PDO::FETCH_ASSOC);

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
    <script src="../../script.js" defer></script>
</head>
<body>
    <main class="tap-page">
        <div class="tap-heading">
            <h1>Simulasi Tap Kartu</h1>
            <a class="back-button" href="../../index.php">&larr; Kembali ke Dashboard</a>
        </div>

        <div class="tap-live-clock" aria-label="Tanggal dan waktu saat ini">
            <span class="eyebrow">WAKTU TAP SEKARANG</span>
            <div class="tap-live-clock-values">
                <time id="live-date"></time>
                <time id="live-time"></time>
            </div>
        </div>

        <?php if (isset($_GET['berhasil'])): ?>
            <p class="tap-message tap-message-success">Presensi berhasil dicatat.</p>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <p class="tap-message tap-message-error"><?= escTap($error) ?></p>
        <?php endif; ?>

        <div class="tap-content">
            <form method="post" class="tap-form">
                <p class="eyebrow">SIMULASI KARTU RFID</p>
                <h2>Catat Presensi</h2>
                <p class="tap-description">Masukkan NISN siswa. Waktu presensi akan tercatat otomatis.</p>
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

            <section class="tap-history-card" aria-labelledby="tap-history-title">
                <div class="tap-history-heading">
                    <div>
                        <p class="eyebrow">AKTIVITAS TERBARU</p>
                        <h2 id="tap-history-title">Tap Terakhir</h2>
                    </div>
                    <span class="tap-history-limit">Maks. 30 catatan</span>
                </div>

                <?php if (!$riwayatTap): ?>
                    <p class="tap-empty">Belum ada presensi yang tercatat.</p>
                <?php else: ?>
                    <ul class="tap-history-list">
                        <?php foreach ($riwayatTap as $baris): ?>
                            <?php $waktuTap = new DateTimeImmutable($baris['tanggal_waktu']); ?>
                            <li class="tap-history-item">
                                <div class="tap-student-info">
                                    <strong><?= escTap($baris['nama']) ?></strong>
                                    <span><?= escTap($baris['nama_kelas']) ?></span>
                                </div>
                                <time datetime="<?= escTap($waktuTap->format('Y-m-d\TH:i:s')) ?>">
                                    <?= escTap($waktuTap->format('d/m/Y H:i')) ?>
                                </time>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </section>
        </div>
    </main>
</body>
</html>
