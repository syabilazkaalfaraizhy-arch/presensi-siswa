<?php
declare(strict_types=1);
require_once __DIR__ . '/../../includes/auth_check.php';
// TODO: CRUD siswa, validasi NISN 10 digit, dan pencarian nama/NISN.
$pageTitle = 'Kelola Siswa';
require_once __DIR__ . '/../../includes/header.php';
?>
<h2>Kelola Siswa</h2>
<p>Daftar siswa dan kelas akan ditampilkan di halaman ini.</p>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>