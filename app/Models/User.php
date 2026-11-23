<?php
declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class User extends BaseModel
{
    public function getAll(): array
    {
        return $this->db->query('SELECT id_user, username FROM users ORDER BY username')->fetchAll();
    }
}
