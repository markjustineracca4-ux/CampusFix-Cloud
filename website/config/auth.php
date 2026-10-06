<?php
session_start();

require_once __DIR__ . '/database.php';

function require_login(): void {
    if (empty($_SESSION['user'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin(): void {
    require_login();
    if ($_SESSION['user']['role'] !== 'admin') {
        http_response_code(403);
        exit('Access denied.');
    }
}

function csrf_token(): string {
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function verify_csrf(): void {
    if (
        empty($_POST['csrf']) ||
        empty($_SESSION['csrf']) ||
        !hash_equals($_SESSION['csrf'], $_POST['csrf'])
    ) {
        http_response_code(419);
        exit('Invalid security token.');
    }
}
