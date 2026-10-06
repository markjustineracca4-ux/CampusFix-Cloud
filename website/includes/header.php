<?php
require_once __DIR__ . '/../config/auth.php';
require_login();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CampusFix Cloud</title>
<link rel="stylesheet" href="css/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="index.php">CampusFix <span>Cloud</span></a>
    <div class="userbox">
        <?= htmlspecialchars($_SESSION['user']['full_name']) ?>
        <small><?= htmlspecialchars($_SESSION['user']['role']) ?></small>
        <a href="logout.php">Logout</a>
    </div>
</header>
<div class="layout">
<aside class="sidebar">
    <a href="index.php">Dashboard</a>
    <a href="incidents.php">Incidents</a>
    <a href="equipment.php">Equipment</a>
    <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="users.php">Users</a>
    <?php endif; ?>
</aside>
<main class="content">
