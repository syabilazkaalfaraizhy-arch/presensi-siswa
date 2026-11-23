<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Presensi extends BaseModel
{
    public function getAll(): array
    {
        $sql = 'SELECT presensi.*, siswa.nama, kelas.nama_kelas FROM presensi '
            . 'INNER JOIN siswa ON siswa.nisn = presensi.nisn '
            . 'INNER JOIN kelas ON kelas.id_kelas = siswa.id_kelas '
            . 'ORDER BY presensi.tanggal_waktu DESC';
        return $this->db->query($sql)->fetchAll();
    }
}
