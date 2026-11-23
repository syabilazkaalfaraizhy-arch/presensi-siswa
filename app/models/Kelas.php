<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Kelas extends BaseModel
{
    private ?int $idKelas = null;
    private string $namaKelas = '';
    private string $jurusan = '';

    public function __construct(PDO $db)
    {
        parent::__construct($db);
    }

    public function getIdKelas(): ?int
    {
        return $this->idKelas;
    }

    public function setIdKelas(?int $idKelas): void
    {
        $this->idKelas = $idKelas;
    }

    public function getNamaKelas(): string
    {
        return $this->namaKelas;
    }

    public function setNamaKelas(string $namaKelas): void
    {
        $this->namaKelas = $namaKelas;
    }

    public function getJurusan(): string
    {
        return $this->jurusan;
    }

    public function setJurusan(string $jurusan): void
    {
        $this->jurusan = $jurusan;
    }

    public function getAll(): array
    {
        $query = $this->db->query(
            'SELECT id_kelas, nama_kelas, jurusan
            FROM kelas
            ORDER BY id_kelas ASC'
        );

        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id_kelas, nama_kelas, jurusan FROM kelas WHERE id_kelas = :id');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function nameExists(string $nama, ?int $excludeId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM kelas WHERE nama_kelas = :nama';
        $params = ['nama' => $nama];
        if ($excludeId !== null) {
            $sql .= ' AND id_kelas <> :exclude_id';
            $params['exclude_id'] = $excludeId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function hasStudents(int $id): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM siswa WHERE id_kelas = :id');
        $stmt->execute(['id' => $id]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(string $nama, string $jurusan): void
    {
        $stmt = $this->db->prepare('INSERT INTO kelas (nama_kelas, jurusan) VALUES (:nama, :jurusan)');
        $stmt->execute(['nama' => $nama, 'jurusan' => $jurusan]);
    }

    public function update(int $id, string $nama, string $jurusan): void
    {
        $stmt = $this->db->prepare('UPDATE kelas SET nama_kelas = :nama, jurusan = :jurusan WHERE id_kelas = :id');
        $stmt->execute(['nama' => $nama, 'jurusan' => $jurusan, 'id' => $id]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM kelas WHERE id_kelas = :id');
        $stmt->execute(['id' => $id]);
    }
}
