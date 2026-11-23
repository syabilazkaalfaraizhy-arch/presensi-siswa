<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Siswa.php';

$database = new Database();
$modelSiswa = new Siswa($database->getConnection());

$cari = trim($_GET['q'] ?? '');
$siswa = $modelSiswa->search($cari);

function h(string|null $teks): string
{
    return htmlspecialchars($teks ?? '', ENT_QUOTES, 'UTF-8');
}

$pageTitle = 'Kelola Siswa';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

<h1>Kelola Siswa</h1>
<a class="back-button" href="../../index.php">&larr; Kembali ke Dashboard</a>

<p><a class="action-button" href="create.php">Tambah Siswa</a></p>

<form method="get" class="data-filter">
    <label for="q">Cari nama atau NISN</label>
    <input
        type="search"
        id="q"
        name="q"
        value="<?= h($cari) ?>"
        placeholder="Contoh: Andi atau 0123456789"
    >
    <button type="submit">Cari</button>
    <a class="action-button action-button-secondary" href="index.php">Reset</a>
</form>

<table>
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
                        <a
                            class="action-button action-button-small"
                            href="edit.php?nisn=<?= urlencode($baris['nisn']) ?>"
                        >Ubah</a>

                        <form action="delete.php" method="post" style="display:inline">
                            <input
                                type="hidden"
                                name="nisn"
                                value="<?= h($baris['nisn']) ?>"
                            >
                            <button
                                type="submit"
                                onclick="return confirm('Yakin ingin menghapus siswa ini?')"
                            >Hapus</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
