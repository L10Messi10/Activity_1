<?php
$title = $title ?? 'Activity 1 Home';
// $active can be set by pages: 'home', 'courses', 'students'
$active = $active ?? '';
$base = '/Activity_1';
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
        <div class="navbar-nav">
            <a class="nav-link <?= $active === 'home' ? 'active' : '' ?>" href="<?= $base ?>/index.php">Home</a>
            <a class="nav-link <?= $active === 'courses' ? 'active' : '' ?>" href="<?= $base ?>/courses/index.php">Courses</a>
            <a class="nav-link <?= $active === 'students' ? 'active' : '' ?>" href="<?= $base ?>/students/index.php">Students</a>
        </div>
    </div>
</nav>
<main class="container py-4">
