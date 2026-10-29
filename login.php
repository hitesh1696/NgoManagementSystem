<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/config/db.php';

if (!empty($_SESSION['admin_id'])) {
    redirectTo('/NGOManagementSystem/dashboard.php');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrfToken();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($username === '' || $password === '') {
        $error = 'Enter both your username and password.';
    } else {
        $statement = $conn->prepare('SELECT id, username, password, full_name FROM admins WHERE username = ? LIMIT 1');
        $statement->bind_param('s', $username);
        $statement->execute();
        $admin = $statement->get_result()->fetch_assoc();

        if ($admin && password_verify($password, $admin['password'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $admin['id'];
            $_SESSION['admin_name'] = $admin['full_name'];
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            redirectTo('/NGOManagementSystem/dashboard.php');
        }

        $error = 'Invalid username or password.';
    }
}
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Login | NGO Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/NGOManagementSystem/assets/css/style.css" rel="stylesheet">
</head>
<body class="login-page">
<main class="card login-card">
    <div class="card-body p-4 p-md-5">
        <p class="eyebrow">HELPING HANDS</p>
        <h1 class="h3 mb-2">Admin login</h1>
        <p class="text-secondary mb-4">Sign in to manage your NGO records.</p>
        <?php if ($error !== ''): ?>
            <div class="alert alert-danger"><?= e($error) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="hidden" name="csrf_token" value="<?= e(csrfToken()) ?>">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input id="username" name="username" class="form-control" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
            </div>
            <div class="mb-4">
                <label for="password" class="form-label">Password</label>
                <input id="password" name="password" type="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </form>
    </div>
</main>
</body>
</html>
