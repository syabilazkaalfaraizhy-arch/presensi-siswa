<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$kelas = $db->query(
    'SELECT id_kelas, nama_kelas, jurusan
    FROM kelas
    ORDER BY id_kelas ASC'
)->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'gagal_hapus'): ?>
    <p>Kelas gagal dihapus. Coba lagi atau periksa data siswa terkait.</p>
<?php endif; ?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Kelas</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <h1>Kelola Kelas</h1>
    <a class="back-button" href="../../index.html">&larr; Kembali ke Dashboard</a>

    <p>
        <a class="action-button" href="create.php">Tambah Kelas</a>
    </p>

    <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'kelas_dipakai'): ?>
        <p>Kelas tidak dapat dihapus karena masih memiliki siswa.</p>
    <?php endif; ?>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>ID Kelas</th>
                <th>Nama Kelas</th>
                <th>Jurusan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($kelas) === 0): ?>
                <tr>
                    <td colspan="4">Belum ada data kelas.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($kelas as $baris): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $baris['id_kelas'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($baris['nama_kelas'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($baris['jurusan'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a class="action-button action-button-small" href="edit.php?id=<?= urlencode((string) $baris['id_kelas']) ?>">Ubah</a>

                            <form action="delete.php" method="post" style="display:inline">
                                <input
                                    type="hidden"
                                    name="id_kelas"
                                    value="<?= htmlspecialchars((string) $baris['id_kelas'], ENT_QUOTES, 'UTF-8') ?>"
                                >
                                <button
                                    type="submit"
                                    onclick="return confirm('Yakin ingin menghapus kelas ini?')"
                                >
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
