<?php
require_once dirname(__DIR__) . '/includes/auth.php';

if (isLoggedIn()) {
    header('Location: /Activity_1/index.php');
    exit;
}

$error = '';
$username = '';
$email = '';
$full_name = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($username === '') {
        $error = 'Username is required.';
    } elseif (strlen($username) < 3) {
        $error = 'Username must be at least 3 characters.';
    } elseif (strlen($username) > 50) {
        $error = 'Username must not exceed 50 characters.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $error = 'Username may only contain letters, numbers, and underscores.';
    } elseif (usernameExists($username)) {
        $error = 'Username already taken.';
    } elseif ($email === '') {
        $error = 'Email is required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (emailExists($email)) {
        $error = 'Email already registered.';
    } elseif ($password === '') {
        $error = 'Password is required.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        if (createUser($username, $email, $password, $full_name)) {
            $_SESSION['msg'] = 'Account created successfully. Please log in.';
            header('Location: /Activity_1/auth/login.php');
            exit;
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}

$title = 'Sign Up';
$active = '';
require dirname(__DIR__) . '/includes/header.php';
?>

<h1>Create Account</h1>
<p><a href="/Activity_1/auth/login.php">Already have an account? Log in</a></p>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form method="post" action="signup.php" class="form-narrow">
    <div class="mb-3">
        <label class="form-label" for="username">Username (3-50 chars, letters/numbers/_):</label>
        <input class="form-control" type="text" id="username" name="username" maxlength="50" required value="<?= e($username) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="email">Email address:</label>
        <input class="form-control" type="email" id="email" name="email" maxlength="100" required value="<?= e($email) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="full_name">Full name (optional):</label>
        <input class="form-control" type="text" id="full_name" name="full_name" maxlength="100" value="<?= e($full_name) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Password (min 8 chars):</label>
        <input class="form-control" type="password" id="password" name="password" required>
    </div>
    <div class="mb-3">
        <label class="form-label" for="confirm_password">Confirm password:</label>
        <input class="form-control" type="password" id="confirm_password" name="confirm_password" required>
    </div>
    <button class="btn btn-primary" type="submit">Create Account</button>
    <a class="btn btn-secondary" href="/Activity_1/index.php">Cancel</a>
</form>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
