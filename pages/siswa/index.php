<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$cari = trim($_GET['q'] ?? '');

$sql = 'SELECT siswa.nisn, siswa.nama, siswa.alamat,
            kelas.nama_kelas
        FROM siswa
        INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas';

if ($cari !== '') {
    $sql .= ' WHERE siswa.nama LIKE :nama OR siswa.nisn LIKE :nisn';
}

$sql .= ' ORDER BY siswa.nama ASC';

$stmt = $db->prepare($sql);

if ($cari !== '') {
    $keyword = '%' . $cari . '%';
    $stmt->execute([
        'nama' => $keyword,
        'nisn' => $keyword,
    ]);
} else {
    $stmt->execute();
}

$siswa = $stmt->fetchAll(PDO::FETCH_ASSOC);

function h(string|null $teks): string
{
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Siswa</title>
</head>
<body>
    <h1>Kelola Siswa</h1>

    <p><a href="create.php">Tambah Siswa</a></p>

    <form method="get">
        <label for="q">Cari nama atau NISN</label>
        <input
            type="search"
            id="q"
            name="q"
            value="<?= h($cari) ?>"
            placeholder="Masukkan nama atau NISN"
        >
        <button type="submit">Cari</button>
        <a href="index.php">Reset</a>
    </form>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>NISN</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Kelas</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$siswa): ?>
                <tr>
                    <td colspan="5">Data siswa tidak ditemukan.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($siswa as $baris): ?>
                    <tr>
                        <td><?= h($baris['nisn']) ?></td>
                        <td><?= h($baris['nama']) ?></td>
                        <td><?= h($baris['alamat']) ?></td>
                        <td><?= h($baris['nama_kelas']) ?></td>
                        <td>
                            <a href="edit.php?nisn=<?= urlencode($baris['nisn']) ?>">Ubah</a>

                            <form action="delete.php" method="post" style="display:inline">
                                <input
                                    type="hidden"
                                    name="nisn"
                                    value="<?= h($baris['nisn']) ?>"
                                >
                                <button
                                    type="submit"
                                    onclick="return confirm('Yakin ingin menghapus siswa ini?')"
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