<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Presensi extends BaseModel
{
    private ?int $id = null;
    private string $nisn = '';
    private ?string $tanggalWaktu = null;

    public function __construct(PDO $db) { parent::__construct($db); }
    public function getId(): ?int { return $this->id; }
    public function setId(?int $id): void { $this->id = $id; }
    public function getNisn(): string { return $this->nisn; }
    public function setNisn(string $nisn): void { $this->nisn = $nisn; }
    public function getTanggalWaktu(): ?string { return $this->tanggalWaktu; }
    public function setTanggalWaktu(?string $tanggalWaktu): void { $this->tanggalWaktu = $tanggalWaktu; }

    public function getAll(): array { return $this->getByFilter(date('Y-m-d')); }

    public function getKelas(): array
    {
        return $this->db->query('SELECT id_kelas, nama_kelas FROM kelas ORDER BY nama_kelas')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByFilter(string $tanggal, string $idKelas = '', string $keyword = ''): array
    {
        $sql = 'SELECT presensi.id, presensi.nisn, presensi.tanggal_waktu, siswa.nama, kelas.nama_kelas
                FROM presensi INNER JOIN siswa ON presensi.nisn = siswa.nisn
                INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
                WHERE DATE(presensi.tanggal_waktu) = :tanggal';
        $params = ['tanggal' => $tanggal];
        if ($idKelas !== '') { $sql .= ' AND kelas.id_kelas = :id_kelas'; $params['id_kelas'] = $idKelas; }
        if ($keyword !== '') {
            $sql .= ' AND (siswa.nama LIKE :nama OR siswa.nisn LIKE :nisn)';
            $params['nama'] = '%' . $keyword . '%'; $params['nisn'] = '%' . $keyword . '%';
        }
        $sql .= ' ORDER BY presensi.tanggal_waktu DESC';
        $stmt = $this->db->prepare($sql); $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecent(int $limit = 30): array
    {
        $limit = max(1, min($limit, 100));
        $stmt = $this->db->query('SELECT siswa.nama, kelas.nama_kelas, presensi.tanggal_waktu FROM presensi
            INNER JOIN siswa ON presensi.nisn = siswa.nisn INNER JOIN kelas ON siswa.id_kelas = kelas.id_kelas
            ORDER BY presensi.tanggal_waktu DESC, presensi.id DESC LIMIT ' . $limit);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function studentExists(string $nisn): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM siswa WHERE nisn = :nisn');
        $stmt->execute(['nisn' => $nisn]); return (int) $stmt->fetchColumn() > 0;
    }

    public function createTap(string $nisn): void
    {
        $stmt = $this->db->prepare('INSERT INTO presensi (nisn) VALUES (:nisn)');
        $stmt->execute(['nisn' => $nisn]);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM presensi WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function getLeaderboard(): array
    {
        $kelas = $this->db->query('SELECT kelas.id_kelas, kelas.nama_kelas, COUNT(presensi.id) AS total_presensi
            FROM kelas LEFT JOIN siswa ON siswa.id_kelas = kelas.id_kelas
            LEFT JOIN presensi ON presensi.nisn = siswa.nisn
            GROUP BY kelas.id_kelas, kelas.nama_kelas
            ORDER BY total_presensi DESC, kelas.nama_kelas ASC')->fetchAll(PDO::FETCH_ASSOC);
        $topStudent = $this->db->prepare('SELECT siswa.nama, COUNT(presensi.id) AS total_presensi
            FROM siswa INNER JOIN presensi ON presensi.nisn = siswa.nisn
            WHERE siswa.id_kelas = :id_kelas GROUP BY siswa.nisn, siswa.nama
            ORDER BY total_presensi DESC, siswa.nama ASC LIMIT 1');
        foreach ($kelas as &$row) {
            $row['total_presensi'] = (int) $row['total_presensi'];
            $row['nama_siswa_teratas'] = null;
            $row['presensi_siswa_teratas'] = 0;
            if ($row['total_presensi'] > 0) {
                $topStudent->execute(['id_kelas' => $row['id_kelas']]);
                $student = $topStudent->fetch(PDO::FETCH_ASSOC);
                if ($student) {
                    $row['nama_siswa_teratas'] = $student['nama'];
                    $row['presensi_siswa_teratas'] = (int) $student['total_presensi'];
                }
            }
            unset($row['id_kelas']);
        }
        unset($row);
        return $kelas;
    }
}
