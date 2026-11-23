<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth_check.php';
// TODO: rekap dengan JOIN, filter tanggal/kelas, pencarian, dan hapus log.
$pageTitle = 'Rekap Presensi';
require_once __DIR__ . '/../../includes/header.php';
?>
<h2>Rekap Presensi</h2>
<p>Rekap presensi dengan filter tanggal dan kelas akan ditampilkan di sini.</p>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
