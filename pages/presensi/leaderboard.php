<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../app/core/Database.php';
require_once __DIR__ . '/../../app/models/Presensi.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $database = new Database();
    $modelPresensi = new Presensi($database->getConnection());
    echo json_encode($modelPresensi->getLeaderboard(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
} catch (Throwable $error) {
    http_response_code(500);
    echo json_encode(['error' => 'Data leaderboard belum dapat dimuat.']);
}
