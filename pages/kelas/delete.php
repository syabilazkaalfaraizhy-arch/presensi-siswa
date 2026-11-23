<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

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
$db = $database->getConnection();

// Pastikan kelas tidak masih memiliki siswa.
$cek = $db->prepare(
    'SELECT COUNT(*)
    FROM siswa
    WHERE id_kelas = :id_kelas'
);
$cek->execute(['id_kelas' => $id]);

if ((int) $cek->fetchColumn() > 0) {
    header('Location: index.php?pesan=kelas_dipakai');
    exit;
}

try {
    $hapus = $db->prepare(
        'DELETE FROM kelas
        WHERE id_kelas = :id_kelas'
    );
    $hapus->execute(['id_kelas' => $id]);
} catch (PDOException $e) {
    // Jangan tampilkan pesan error database mentah.
    header('Location: index.php?pesan=gagal_hapus');
    exit;
}

header('Location: index.php');
exit;