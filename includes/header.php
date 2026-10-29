<?php
$pageTitle = $pageTitle ?? 'NGO Management System';
$flashMessage = getFlash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle) ?> | NGO Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/NGOManagementSystem/assets/css/style.css" rel="stylesheet">
</head>
<body>
<div class="app-shell">
    <?php require __DIR__ . '/sidebar.php'; ?>
    <main class="main-content">
        <div class="content-heading">
            <div>
                <p class="eyebrow mb-1">NGO MANAGEMENT SYSTEM</p>
                <h1 class="h3 mb-0"><?= e($pageTitle) ?></h1>
            </div>
            <span class="admin-label">Signed in as <?= e($_SESSION['admin_name'] ?? 'Admin') ?></span>
        </div>
        <?php if ($flashMessage !== null): ?>
            <div class="alert alert-<?= e($flashMessage['type']) ?> alert-dismissible fade show" role="alert">
                <?= e($flashMessage['message']) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
