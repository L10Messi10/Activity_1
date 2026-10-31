<?php
require_once dirname(__DIR__) . '/includes/auth.php';

$title = $title ?? 'Activity 1 Home';
$active = $active ?? '';
$base = '/Activity_1';
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/Activity_1/assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="/Activity_1/assets/css/style.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $base ?>/index.php">Activity 1</a>
        <div class="navbar-nav me-auto mb-2 mb-lg-0">
            <a class="nav-link <?= $active === 'home' ? 'active' : '' ?>" href="<?= $base ?>/index.php">Home</a>
            <a class="nav-link <?= $active === 'courses' ? 'active' : '' ?>" href="<?= $base ?>/courses/index.php">Courses</a>
            <a class="nav-link <?= $active === 'students' ? 'active' : '' ?>" href="<?= $base ?>/students/index.php">Students</a>
        </div>
        <div class="navbar-nav">
            <?php if ($user): ?>
                <a class="nav-link" href="#"><?= e($user['username']) ?></a>
                <a class="nav-link" href="<?= $base ?>/auth/logout.php">Log out</a>
            <?php else: ?>
                <a class="nav-link" href="<?= $base ?>/auth/login.php">Log in</a>
                <a class="nav-link btn btn-outline-light btn-sm ms-2" href="<?= $base ?>/auth/signup.php">Sign up</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<main class="container py-4">
