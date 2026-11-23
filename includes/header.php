<?php
$pageTitle = $pageTitle ?? 'Presensi Siswa';
$bodyClass = $bodyClass ?? '';

$scriptPath = parse_url($_SERVER['SCRIPT_NAME'] ?? '/', PHP_URL_PATH);
$scriptPath = str_replace('\\', '/', is_string($scriptPath) ? $scriptPath : '/');
$pagesPosition = strpos($scriptPath, '/pages/');

if ($pagesPosition !== false) {
    $baseUrl = substr($scriptPath, 0, $pagesPosition + 1);
} else {
    $baseDirectory = str_replace('\\', '/', dirname($scriptPath));
    $baseUrl = ($baseDirectory === '/' || $baseDirectory === '.')
        ? '/'
        : rtrim($baseDirectory, '/') . '/';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>style.css">
    <script src="<?= htmlspecialchars($baseUrl, ENT_QUOTES, 'UTF-8') ?>script.js" defer></script>
</head>
<body class="<?= htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8') ?>">
