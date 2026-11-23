<?php
declare(strict_types=1);
$pageTitle = $pageTitle ?? 'Presensi Siswa';
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$pagesPosition = strpos($scriptName, '/pages/');
$appBase = $pagesPosition === false ? '' : substr($scriptName, 0, $pagesPosition);
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?>/assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="header-inner">
        <h1>Presensi Siswa</h1>
        <nav aria-label="Navigasi utama">
            <a href="<?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?>/pages/dashboard.php">Dashboard</a>
            <a href="<?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?>/pages/kelas/index.php">Data Kelas</a>
            <a href="<?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?>/pages/siswa/index.php">Data Siswa</a>
            <a href="<?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?>/pages/presensi/index.php">Rekap Presensi</a>
            <a href="<?= htmlspecialchars($appBase, ENT_QUOTES, 'UTF-8') ?>/pages/logout.php">Logout</a>
        </nav>
    </div>
</header>
<main class="page-content">
