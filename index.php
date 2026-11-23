<?php
require_once __DIR__ . '/includes/auth_check.php';
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

    <main class="dashboard">
        <div class="dashboard-intro">
            <div>
                <p class="eyebrow">SISTEM PRESENSI SISWA</p>
                <h2>Selamat Datang</h2>
                <p>Aplikasi Presensi Siswa SMK TI Airlangga Samarinda.</p>
            </div>
            <a class="button" href="pages/presensi/tap.php">Mulai Simulasi Tap</a>
        </div>

        <h3 class="section-title">Akses Cepat</h3>
        <div class="dashboard-grid">
            <a class="dashboard-card" href="pages/siswa/index.php">
                <span class="dashboard-icon" aria-hidden="true">01</span>
                <span class="dashboard-card-title">Data Siswa</span>
                <span class="dashboard-card-text">Kelola siswa dan cari berdasarkan nama atau NISN.</span>
                <span class="dashboard-card-link">Buka data siswa &rarr;</span>
            </a>
            <a class="dashboard-card" href="pages/kelas/index.php">
                <span class="dashboard-icon" aria-hidden="true">02</span>
                <span class="dashboard-card-title">Data Kelas</span>
                <span class="dashboard-card-text">Lihat dan kelola kelas beserta jurusannya.</span>
                <span class="dashboard-card-link">Buka data kelas &rarr;</span>
            </a>
            <a class="dashboard-card" href="pages/presensi/index.php">
                <span class="dashboard-icon" aria-hidden="true">03</span>
                <span class="dashboard-card-title">Rekap Presensi</span>
                <span class="dashboard-card-text">Cari catatan kehadiran dan gunakan filter rekap.</span>
                <span class="dashboard-card-link">Buka rekap &rarr;</span>
            </a>
        </div>

        <section class="guide-card">
            <div>
                <p class="eyebrow">CARA MENGGUNAKAN</p>
                <h3>Simulasi tap kartu</h3>
                <p>Masukkan NISN 10 digit seperti yang tercetak pada kartu siswa. Sistem akan memeriksa data siswa dan mengisi waktu presensi secara otomatis.</p>
            </div>
            <a class="button" href="pages/presensi/tap.php">Buka Halaman Tap</a>
        </section>

        <section class="leaderboard-card" aria-labelledby="leaderboard-title">
            <div class="leaderboard-heading">
                <div>
                    <p class="eyebrow">RINGKASAN KEHADIRAN</p>
                    <h3 id="leaderboard-title">Leaderboard Presensi</h3>
                    <p class="leaderboard-note">Peringkat berdasarkan jumlah tap presensi yang tercatat. Data ini menunjukkan kehadiran, bukan jumlah siswa yang tidak hadir.</p>
                </div>
                <a class="action-button action-button-secondary" href="pages/presensi/index.php">Lihat Rekap</a>
            </div>
            <div id="leaderboard-content" aria-live="polite">
                <p class="leaderboard-status">Memuat peringkat kelas...</p>
            </div>
        </section>
    </main>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
