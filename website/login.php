<?php
require_once __DIR__ . '/config/auth.php';

if (!empty($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        unset($user['password']);
        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        header('Location: index.php');
        exit;
    }

    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — CampusFix Cloud</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body class="login-page">
<div class="login-card">
    <div class="logo">CF</div>
    <h1>CampusFix Cloud</h1>
    <p>Campus IT Incident Management System</p>

    <?php if ($error): ?><div class="alert danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>

    <form method="post">
        <input type="hidden" name="csrf" value="<?= htmlspecialchars(csrf_token()) ?>">
        <label>Username</label>
        <input name="username" required autocomplete="username">
        <label>Password</label>
        <input type="password" name="password" required autocomplete="current-password">
        <button class="btn primary full" type="submit">Sign In</button>
    </form>

    <div class="hint">Initial account: <b>admin</b> / <b>admin123</b></div>
</div>
</body>
</html>
