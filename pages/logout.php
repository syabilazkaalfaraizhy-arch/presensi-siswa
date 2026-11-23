<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/auth_check.php';

// Hapus data session dan akhiri session.
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;