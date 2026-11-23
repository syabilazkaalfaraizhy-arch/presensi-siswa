<?php
declare(strict_types=1);

session_start();

if (isset($_SESSION['id_user'])) {
    header('Location: pages/dashboard.php');
    exit;
}

header('Location: pages/login.php');
exit;
