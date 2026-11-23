<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Siswa.php';

$database = new Database();
$modelSiswa = new Siswa($database->getConnection());

$nisnLama = trim($_GET['nisn'] ?? '');

if ($nisnLama === '') {
    header('Location: index.php');
    exit;
}

$siswa = $modelSiswa->findByNisn($nisnLama);

if (!$siswa) {
    header('Location: index.php');
    exit;
}

$kelas = $modelSiswa->getKelas();

$errors = ['nisn' => '', 'nama' => '', 'id_kelas' => '', 'form' => ''];
$nisn = $siswa['nisn'];
$nama = $siswa['nama'];
$alamat = $siswa['alamat'] ?? '';
$idKelas = (string) $siswa['id_kelas'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nisn = trim($_POST['nisn'] ?? '');
    $nama = trim($_POST['nama'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $idKelas = trim($_POST['id_kelas'] ?? '');

    if (!preg_match('/^[0-9]{10}$/', $nisn)) {
        $errors['nisn'] = 'NISN harus terdiri tepat dari 10 angka.';
    } elseif ($modelSiswa->nisnExists($nisn, $nisnLama)) {
        $errors['nisn'] = 'NISN sudah digunakan siswa lain.';
    }

    if ($nama === '') {
        $errors['nama'] = 'Nama siswa wajib diisi.';
    }

    if ($idKelas === '' || !ctype_digit($idKelas)) {
        $errors['id_kelas'] = 'Pilih kelas siswa.';
    } elseif (!$modelSiswa->kelasExists((int) $idKelas)) {
        $errors['id_kelas'] = 'Kelas yang dipilih tidak tersedia.';
    }

    if ($errors['nisn'] === '' && $errors['nama'] === '' && $errors['id_kelas'] === '') {
        try {
            $modelSiswa->update($nisnLama, $nisn, $nama, $alamat === '' ? null : $alamat, (int) $idKelas);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors['form'] = 'Data gagal diperbarui. NISN mungkin masih dipakai pada log presensi.';
        }
    }
}

function escEditSiswa(string|null $nilai): string
{
    return htmlspecialchars($nilai ?? '', ENT_QUOTES, 'UTF-8');
}

$pageTitle = 'Ubah Siswa';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

    <h1>Ubah Siswa</h1>

    <?php if ($errors['form'] !== ''): ?>
        <p class="form-error"><?= escEditSiswa($errors['form']) ?></p>
    <?php endif; ?>

    <form method="post" novalidate>
        <label for="nisn">NISN (10 angka)</label>
        <input
            type="text"
            id="nisn"
            name="nisn"
            inputmode="numeric"
            pattern="[0-9]{10}"
            maxlength="10"
            placeholder="Contoh: 0123456789"
            value="<?= escEditSiswa($nisn) ?>"
            required
        >
        <?php if ($errors['nisn'] !== ''): ?>
            <small class="field-error"><?= escEditSiswa($errors['nisn']) ?></small>
        <?php endif; ?>

        <br><br>

        <label for="nama">Nama</label>
        <input
            type="text"
            id="nama"
            name="nama"
            maxlength="100"
            placeholder="Contoh: Andi Pratama"
            value="<?= escEditSiswa($nama) ?>"
            required
        >
        <?php if ($errors['nama'] !== ''): ?>
            <small class="field-error"><?= escEditSiswa($errors['nama']) ?></small>
        <?php endif; ?>

        <br><br>

        <label for="alamat">Alamat (boleh kosong)</label>
        <input
            type="text"
            id="alamat"
            name="alamat"
            maxlength="255"
            placeholder="Contoh: Jl. Merdeka No. 10"
            value="<?= escEditSiswa($alamat) ?>"
        >

        <br><br>

        <label for="id_kelas">Kelas</label>
        <select id="id_kelas" name="id_kelas" required>
            <option value="">Pilih kelas</option>

            <?php foreach ($kelas as $barisKelas): ?>
                <option
                    value="<?= escEditSiswa((string) $barisKelas['id_kelas']) ?>"
                    <?= $idKelas === (string) $barisKelas['id_kelas'] ? 'selected' : '' ?>
                >
                    <?= escEditSiswa($barisKelas['nama_kelas']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if ($errors['id_kelas'] !== ''): ?>
            <small class="field-error"><?= escEditSiswa($errors['id_kelas']) ?></small>
        <?php endif; ?>

        <br><br>

        <button type="submit">Simpan Perubahan</button>
        <a class="back-button" href="index.php">Kembali ke Daftar Siswa</a>
    </form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
