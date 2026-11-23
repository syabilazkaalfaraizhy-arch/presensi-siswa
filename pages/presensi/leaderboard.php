<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $database = new Database();
    $db = $database->getConnection();

    $kelas = $db->query(
        'SELECT kelas.id_kelas, kelas.nama_kelas, COUNT(presensi.id) AS total_presensi
        FROM kelas
        LEFT JOIN siswa ON siswa.id_kelas = kelas.id_kelas
        LEFT JOIN presensi ON presensi.nisn = siswa.nisn
        GROUP BY kelas.id_kelas, kelas.nama_kelas
        ORDER BY total_presensi DESC, kelas.nama_kelas ASC'
    )->fetchAll(PDO::FETCH_ASSOC);

    $siswaTeratas = $db->prepare(
        'SELECT siswa.nama, COUNT(presensi.id) AS total_presensi
        FROM siswa
        INNER JOIN presensi ON presensi.nisn = siswa.nisn
        WHERE siswa.id_kelas = :id_kelas
        GROUP BY siswa.nisn, siswa.nama
        ORDER BY total_presensi DESC, siswa.nama ASC
        LIMIT 1'
    );

    foreach ($kelas as &$barisKelas) {
        $barisKelas['total_presensi'] = (int) $barisKelas['total_presensi'];
        $barisKelas['nama_siswa_teratas'] = null;
        $barisKelas['presensi_siswa_teratas'] = 0;

        if ($barisKelas['total_presensi'] > 0) {
            $siswaTeratas->execute(['id_kelas' => $barisKelas['id_kelas']]);
            $siswa = $siswaTeratas->fetch(PDO::FETCH_ASSOC);

            if ($siswa) {
                $barisKelas['nama_siswa_teratas'] = $siswa['nama'];
                $barisKelas['presensi_siswa_teratas'] = (int) $siswa['total_presensi'];
            }
        }

        unset($barisKelas['id_kelas']);
    }
    unset($barisKelas);

    echo json_encode($kelas, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Data leaderboard belum dapat dimuat.']);
}
