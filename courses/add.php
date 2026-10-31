<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/auth.php';

requireLogin();

$error = '';
$course_name = '';
$course_description = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');
    $course_description = trim($_POST['course_description'] ?? '');

    // Validation
    if ($course_name === '') {
        $error = 'Course name is required.';
    } elseif (strlen($course_name) > 250) {
        $error = 'Course name must not exceed 250 characters.';
    } elseif (strlen($course_description) > 1000) {
        $error = 'Description must not exceed 1000 characters.';
    } else {
        // CREATE
        $sql = "INSERT INTO course (course_name, course_description, date_added) VALUES (?, ?, GETDATE())";
        $params = [$course_name, $course_description !== '' ? $course_description : null];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            $error = 'Insert failed.';
        } else {
            sqlsrv_free_stmt($stmt);
            header('Location: index.php?msg=' . urlencode('Course added successfully.'));
            exit;
        }
    }
}

$title = 'Add Course';
$active = 'courses';
require dirname(__DIR__) . '/includes/header.php';
?>

<h1>Add New Course</h1>
<p><a href="index.php">&larr; Back to list</a></p>

<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?><?php if ($error === 'Insert failed.') print_r(sqlsrv_errors()); ?></div>
<?php endif; ?>

<form method="post" action="add.php" class="form-narrow">
    <div class="mb-3">
        <label class="form-label" for="course_name">Course Name (required, max 250):</label>
        <input class="form-control" type="text" id="course_name" name="course_name" maxlength="250" required value="<?= e($course_name) ?>">
    </div>
    <div class="mb-3">
        <label class="form-label" for="course_description">Description (optional, max 1000):</label>
        <textarea class="form-control" id="course_description" name="course_description" rows="4" maxlength="1000"><?= e($course_description) ?></textarea>
    </div>
    <button class="btn btn-primary" type="submit">Save</button>
    <a class="btn btn-secondary" href="index.php">Cancel</a>
</form>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
