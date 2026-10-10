<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}
if (!$id) {
    die('Invalid course ID.');
}

$error = '';

// Confirm delete on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM course WHERE course_id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);
    if ($stmt === false) {
        $error = 'Delete failed.';
    } else {
        $rows = sqlsrv_rows_affected($stmt);
        sqlsrv_free_stmt($stmt);
        if ($rows === false || $rows === 0) {
            $error = 'Course not found or already deleted.';
        } else {
            header('Location: ../index.php?msg=' . urlencode('Course deleted successfully.'));
            exit;
        }
    }
}

// Load row for confirmation display
$sql = "SELECT course_id, course_name, course_description FROM course WHERE course_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
if ($stmt === false) {
    die('Failed to load course.');
}
$course = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
sqlsrv_free_stmt($stmt);
if (!$course) {
    die('Course not found.');
}

$title = 'Delete Course';
require dirname(__DIR__) . '/includes/header.php';
?>

<h1>Delete Course</h1>

<div class="box">
    <?php if ($error): ?>
        <div class="err"><?= e($error) ?></div>
    <?php endif; ?>
    <p>Are you sure you want to delete this course?</p>
    <p><strong>#<?= e($course['course_id']) ?> — <?= e($course['course_name']) ?></strong></p>
    <p><?= e($course['course_description']) ?></p>

    <form method="post" action="delete.php?id=<?= e($course['course_id']) ?>" onsubmit="return confirm('Confirm delete?');">
        <input type="hidden" name="id" value="<?= e($course['course_id']) ?>">
        <button class="btn btn-danger mt" type="submit">Yes, Delete</button>
        <a class="btn mt" href="../index.php">Cancel</a>
    </form>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
