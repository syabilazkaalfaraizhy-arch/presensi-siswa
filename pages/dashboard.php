<?php
declare(strict_types=1);
require_once __DIR__ . '/../includes/auth_check.php';
$pageTitle = 'Dashboard Presensi';
require_once __DIR__ . '/../includes/header.php';
?>
<h2>Dashboard</h2>
<p>Ringkasan presensi dan pintasan fitur aplikasi.</p>
<section class="card">
    <h3>Simulasi Tap Kartu</h3>
    <!-- TODO: simpan NISN ke tabel presensi menggunakan prepared statement. -->
    <form method="post" action="">
        <label for="nisn">NISN</label>
        <input id="nisn" name="nisn" type="text" inputmode="numeric" maxlength="10" required>
        <button type="submit">Catat Presensi</button>
    </form>
</section>
<?php require_once __DIR__ . '/../includes/footer.php'; ?>