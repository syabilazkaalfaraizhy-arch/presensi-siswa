<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Siswa extends BaseModel
{
    public function getAll(): array
    {
        $sql = 'SELECT siswa.*, kelas.nama_kelas FROM siswa '
            . 'INNER JOIN kelas ON kelas.id_kelas = siswa.id_kelas '
            . 'ORDER BY siswa.nama';
        return $this->db->query($sql)->fetchAll();
    }
}
