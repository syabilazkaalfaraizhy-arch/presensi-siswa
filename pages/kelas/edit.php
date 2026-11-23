<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Kelas.php';

$database = new Database();
$modelKelas = new Kelas($database->getConnection());

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    header('Location: index.php');
    exit;
}

$kelas = $modelKelas->findById($id);

if (!$kelas) {
    header('Location: index.php');
    exit;
}

$errors = ['nama_kelas' => '', 'jurusan' => ''];
$namaKelas = $kelas['nama_kelas'];
$jurusan = $kelas['jurusan'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaKelas = trim($_POST['nama_kelas'] ?? '');
    $jurusan = trim($_POST['jurusan'] ?? '');

    if ($namaKelas === '') {
        $errors['nama_kelas'] = 'Nama kelas wajib diisi.';
    } elseif ($modelKelas->nameExists($namaKelas, $id)) {
        $errors['nama_kelas'] = 'Nama kelas sudah digunakan. Masukkan nama lain.';
    }

    if ($jurusan === '') {
        $errors['jurusan'] = 'Jurusan wajib diisi.';
    }

    if ($errors['nama_kelas'] === '' && $errors['jurusan'] === '') {
        try {
            $modelKelas->update($id, $namaKelas, $jurusan);
            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $errors['nama_kelas'] = 'Data kelas gagal diperbarui. Periksa kembali nama kelas.';
        }
    }
}

$pageTitle = 'Ubah Kelas';
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/navbar.php';
?>

    <h1>Ubah Kelas</h1>

    <form method="post" novalidate>
        <label for="nama_kelas">Nama Kelas</label>
        <input
            type="text"
            id="nama_kelas"
            name="nama_kelas"
            maxlength="20"
            placeholder="Contoh: XI PPLG"
            value="<?= htmlspecialchars($namaKelas, ENT_QUOTES, 'UTF-8') ?>"
            required
        >
        <?php if ($errors['nama_kelas'] !== ''): ?>
            <small class="field-error"><?= htmlspecialchars($errors['nama_kelas'], ENT_QUOTES, 'UTF-8') ?></small>
        <?php endif; ?>

        <br><br>

        <label for="jurusan">Jurusan</label>
        <input
            type="text"
            id="jurusan"
            name="jurusan"
            maxlength="50"
            placeholder="Contoh: PPLG"
            value="<?= htmlspecialchars($jurusan, ENT_QUOTES, 'UTF-8') ?>"
            required
        >
        <?php if ($errors['jurusan'] !== ''): ?>
            <small class="field-error"><?= htmlspecialchars($errors['jurusan'], ENT_QUOTES, 'UTF-8') ?></small>
        <?php endif; ?>

        <br><br>

        <button type="submit">Simpan Perubahan</button>
        <a class="back-button" href="index.php">Kembali ke Daftar Kelas</a>
    </form>
<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
