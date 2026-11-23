<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Kelas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$id = filter_var($_POST['id_kelas'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$database = new Database();
$modelKelas = new Kelas($database->getConnection());

// Pastikan kelas tidak masih memiliki siswa.
if ($modelKelas->hasStudents($id)) {
    header('Location: index.php?pesan=kelas_dipakai');
    exit;
}

try {
    $modelKelas->delete($id);
} catch (PDOException $e) {
    // Jangan tampilkan pesan error database mentah.
    header('Location: index.php?pesan=gagal_hapus');
    exit;
}

header('Location: index.php');
exit;
