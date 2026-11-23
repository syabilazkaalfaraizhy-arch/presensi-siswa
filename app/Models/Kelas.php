<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Kelas extends BaseModel
{
    public function getAll(): array
    {
        return $this->db->query('SELECT * FROM kelas ORDER BY nama_kelas')->fetchAll();
    }
}
