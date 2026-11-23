<?php
declare(strict_types=1);

session_start();

// Hapus data session dan akhiri session.
$_SESSION = [];
session_destroy();

header('Location: login.php');
exit;