<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    private ?int $idUser = null;
    private string $username = '';
    private string $password = '';

    public function __construct(PDO $db) { parent::__construct($db); }
    public function getIdUser(): ?int { return $this->idUser; }
    public function setIdUser(?int $idUser): void { $this->idUser = $idUser; }
    public function getUsername(): string { return $this->username; }
    public function setUsername(string $username): void { $this->username = $username; }
    public function getPassword(): string { return $this->password; }
    public function setPassword(string $password): void { $this->password = $password; }

    public function getAll(): array
    {
        return $this->db->query('SELECT id_user, username FROM users ORDER BY username')->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT id_user, username, password FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }
}
