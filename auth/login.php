<?php
require_once dirname(__DIR__) . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /Activity_1/index.php');
    exit;
}

$error = '';
$username = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $user = findUserByUsername($username);
        if ($user && password_verify($password, $user['password_hash'])) {
            if (!empty($user['is_active']) && $user['is_active'] === '1') {
                $redirect = $_SESSION['redirect_after_login'] ?? '/Activity_1/index.php';
                unset($_SESSION['redirect_after_login']);
                loginUser($user);
                header('Location: ' . $redirect);
                exit;
            } else {
                $error = 'Account is deactivated.';
            }
        } else {
            $error = 'Invalid username or password.';
        }
    }
}

$title = 'Log In';
$active = '';
require dirname(__DIR__) . '/includes/header.php';
?>

<h1>Log In</h1>
<p><a href="/Activity_1/auth/signup.php">Don't have an account? Sign up</a></p>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" action="login.php" class="form-narrow">
    <div class="mb-3">
        <label class="form-label" for="username">Username:</label>
        <input class="form-control" type="text" id="username" name="username" required value="<?= e($username) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Password:</label>
        <input class="form-control" type="password" id="password" name="password" required>
    </div>
    <button class="btn btn-primary" type="submit">Log In</button>
    <a class="btn btn-secondary" href="/Activity_1/index.php">Cancel</a>
</form>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
