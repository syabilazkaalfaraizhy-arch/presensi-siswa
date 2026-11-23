<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Siswa extends BaseModel
{
    private string $nisn = '';
    private string $nama = '';
    private ?string $alamat = null;
    private ?int $idKelas = null;

    public function __construct(PDO $db) { parent::__construct($db); }
    public function getNisn(): string { return $this->nisn; }
    public function setNisn(string $nisn): void { $this->nisn = $nisn; }
    public function getNama(): string { return $this->nama; }
    public function setNama(string $nama): void { $this->nama = $nama; }
    public function getAlamat(): ?string { return $this->alamat; }
    public function setAlamat(?string $alamat): void { $this->alamat = $alamat; }
    public function getIdKelas(): ?int { return $this->idKelas; }
    public function setIdKelas(?int $idKelas): void { $this->idKelas = $idKelas; }

    public function getAll(): array { return $this->search(''); }

    public function search(string $keyword): array
    {
        $sql = 'SELECT siswa.nisn, siswa.nama, siswa.alamat, siswa.id_kelas, kelas.nama_kelas
                FROM siswa INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas';
        $params = [];
        if ($keyword !== '') {
            $sql .= ' WHERE siswa.nama LIKE :nama OR siswa.nisn LIKE :nisn';
            $params = ['nama' => '%' . $keyword . '%', 'nisn' => '%' . $keyword . '%'];
        }
        $sql .= ' ORDER BY siswa.nama ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getKelas(): array
    {
        return $this->db->query('SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByNisn(string $nisn): ?array
    {
        $stmt = $this->db->prepare('SELECT nisn, nama, alamat, id_kelas FROM siswa WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public function nisnExists(string $nisn, ?string $excludeNisn = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM siswa WHERE nisn = :nisn';
        $params = ['nisn' => $nisn];
        if ($excludeNisn !== null) {
            $sql .= ' AND nisn <> :exclude_nisn';
            $params['exclude_nisn'] = $excludeNisn;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function kelasExists(int $idKelas): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM kelas WHERE id_kelas = :id');
        $stmt->execute(['id' => $idKelas]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function hasPresensi(string $nisn): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM presensi WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]);
        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(string $nisn, string $nama, ?string $alamat, int $idKelas): void
    {
        $stmt = $this->db->prepare('INSERT INTO siswa (nisn, nama, alamat, id_kelas) VALUES (:nisn, :nama, :alamat, :id_kelas)');
        $stmt->execute(['nisn' => $nisn, 'nama' => $nama, 'alamat' => $alamat, 'id_kelas' => $idKelas]);
    }

    public function update(string $nisnLama, string $nisn, string $nama, ?string $alamat, int $idKelas): void
    {
        $stmt = $this->db->prepare('UPDATE siswa SET nisn = :nisn, nama = :nama, alamat = :alamat, id_kelas = :id_kelas WHERE nisn = :nisn_lama');
        $stmt->execute(['nisn' => $nisn, 'nama' => $nama, 'alamat' => $alamat, 'id_kelas' => $idKelas, 'nisn_lama' => $nisnLama]);
    }

    public function delete(string $nisn): void
    {
        $stmt = $this->db->prepare('DELETE FROM siswa WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]);
    }
}
