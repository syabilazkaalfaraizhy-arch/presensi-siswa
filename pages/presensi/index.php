<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

$database = new Database();
$db = $database->getConnection();

$tanggal = trim($_GET['tanggal'] ?? date('Y-m-d'));
if (
    !preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)
    || !checkdate(
        (int) substr($tanggal, 5, 2),
        (int) substr($tanggal, 8, 2),
        (int) substr($tanggal, 0, 4)
    )
) {
    $tanggal = date('Y-m-d');
}

$idKelas = trim($_GET['id_kelas'] ?? '');
if ($idKelas !== '' && !ctype_digit($idKelas)) {
    $idKelas = '';
}

$cari = trim($_GET['q'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idPresensi = filter_var(
        $_POST['id_presensi'] ?? null,
        FILTER_VALIDATE_INT
    );

    if ($idPresensi) {
        $hapus = $db->prepare(
            'DELETE FROM presensi WHERE id = :id'
        );
        $hapus->execute(['id' => $idPresensi]);
    }

    $filter = [
        'tanggal' => $_POST['tanggal'] ?? date('Y-m-d'),
        'id_kelas' => $_POST['id_kelas'] ?? '',
        'q' => $_POST['q'] ?? '',
    ];

    header('Location: index.php?' . http_build_query($filter));
    exit;
}

$kelas = $db->query(
    'SELECT id_kelas, nama_kelas
    FROM kelas
    ORDER BY nama_kelas'
)->fetchAll(PDO::FETCH_ASSOC);

$sql = 'SELECT presensi.id, presensi.nisn, presensi.tanggal_waktu,
            siswa.nama, kelas.nama_kelas
        FROM presensi
        INNER JOIN siswa ON presensi.nisn = siswa.nisn
        INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
        WHERE DATE(presensi.tanggal_waktu) = :tanggal';

$parameter = ['tanggal' => $tanggal];

if ($idKelas !== '') {
    $sql .= ' AND kelas.id_kelas = :id_kelas';
    $parameter['id_kelas'] = $idKelas;
}

if ($cari !== '') {
    $sql .= ' AND (siswa.nama LIKE :nama OR siswa.nisn LIKE :nisn)';
    $parameter['nama'] = '%' . $cari . '%';
    $parameter['nisn'] = '%' . $cari . '%';
}

$sql .= ' ORDER BY presensi.tanggal_waktu DESC';

$stmt = $db->prepare($sql);
$stmt->execute($parameter);
$presensi = $stmt->fetchAll(PDO::FETCH_ASSOC);

function escPresensi(string|null $nilai): string
{
    return htmlspecialchars($nilai ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Presensi</title>
    <link rel="stylesheet" href="../../style.css">
</head>
<body>
    <h1>Rekap Presensi</h1>
    <a class="back-button" href="../../index.html">&larr; Kembali ke Dashboard</a>

    <form method="get" class="data-filter">
        <label for="q">Cari nama atau NISN</label>
        <input
            type="search"
            id="q"
            name="q"
            value="<?= escPresensi($cari) ?>"
            placeholder="Contoh: Andi atau 0123456789"
        >

        <label for="tanggal">Tanggal</label>
        <input
            type="date"
            id="tanggal"
            name="tanggal"
            value="<?= escPresensi($tanggal) ?>"
            required
        >

        <label for="id_kelas">Kelas</label>
        <select id="id_kelas" name="id_kelas">
            <option value="">Semua kelas</option>
            <?php foreach ($kelas as $barisKelas): ?>
                <option
                    value="<?= escPresensi((string) $barisKelas['id_kelas']) ?>"
                    <?= $idKelas === (string) $barisKelas['id_kelas'] ? 'selected' : '' ?>
                >
                    <?= escPresensi($barisKelas['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Terapkan</button>
        <a href="index.php">Hari Ini</a>
    </form>

    <table border="1" cellpadding="8" cellspacing="0">
        <thead>
            <tr>
                <th>NISN</th>
                <th>Nama Siswa</th>
                <th>Kelas</th>
                <th>Tanggal dan Waktu</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!$presensi): ?>
                <tr>
                    <td colspan="5">Tidak ada data presensi untuk filter tersebut.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($presensi as $baris): ?>
                    <tr>
                        <td><?= escPresensi($baris['nisn']) ?></td>
                        <td><?= escPresensi($baris['nama']) ?></td>
                        <td><?= escPresensi($baris['nama_kelas']) ?></td>
                        <td><?= escPresensi($baris['tanggal_waktu']) ?></td>
                        <td>
                            <form method="post">
                                <input
                                    type="hidden"
                                    name="id_presensi"
                                    value="<?= escPresensi((string) $baris['id']) ?>"
                                >
                                <input type="hidden" name="tanggal" value="<?= escPresensi($tanggal) ?>">
                                <input type="hidden" name="id_kelas" value="<?= escPresensi($idKelas) ?>">
                                <input type="hidden" name="q" value="<?= escPresensi($cari) ?>">
                                <button
                                    type="submit"
                                    onclick="return confirm('Hapus log presensi ini?')"
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
