<?php
session_start();
require_once __DIR__ . '/config.php';

if (empty($_SESSION['user_email']) || empty($_SESSION['admin_authenticated'])) {
    header('Location: Login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    resetTicketQuota();
}

header('Location: admin_scan.php?quota_reset=1');
exit;
