<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Presensi.php';

$database = new Database();
$modelPresensi = new Presensi($database->getConnection());

$nisnError = '';
$nisn = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');

    if (!preg_match('/^[0-9]{10}$/', $nisn)) {
        $nisnError = 'NISN harus terdiri tepat dari 10 angka.';
    } else {
        if (!$modelPresensi->studentExists($nisn)) {
            $nisnError = 'NISN tidak terdaftar. Periksa kembali kartu siswa.';
        } else {
            // id dan tanggal_waktu diisi otomatis oleh database.
            $modelPresensi->createTap($nisn);

            header('Location: tap.php?berhasil=1');
            exit;
        }
    }
}

$riwayatTap = $modelPresensi->getRecent(30);

function escTap(string $nilai): string
{
    return htmlspecialchars($nilai, ENT_QUOTES, 'UTF-8');
}

$pageTitle = 'Simulasi Tap Kartu';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

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

        <div class="tap-content">
            <form method="post" class="tap-form" novalidate>
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
                <?php if ($nisnError !== ''): ?>
                    <small class="field-error"><?= escTap($nisnError) ?></small>
                <?php endif; ?>
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
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
