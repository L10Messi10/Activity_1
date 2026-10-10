<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    // Support POST-back of id as well
    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
}
if (!$id) {
    die('Invalid course ID.');
}

$error = '';

// Handle UPDATE on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_name = trim($_POST['course_name'] ?? '');
    $course_description = trim($_POST['course_description'] ?? '');

    if ($course_name === '') {
        $error = 'Course name is required.';
    } elseif (strlen($course_name) > 250) {
        $error = 'Course name must not exceed 250 characters.';
    } elseif (strlen($course_description) > 1000) {
        $error = 'Description must not exceed 1000 characters.';
    } else {
        $sql = "UPDATE course SET course_name = ?, course_description = ? WHERE course_id = ?";
        $params = [$course_name, $course_description !== '' ? $course_description : null, $id];
        $stmt = sqlsrv_query($conn, $sql, $params);
        if ($stmt === false) {
            $error = 'Update failed.';
        } else {
            sqlsrv_free_stmt($stmt);
            header('Location: ../index.php?msg=' . urlencode('Course updated successfully.'));
            exit;
        }
    }
    // Keep posted values for re-display on error
    $course = ['course_id' => $id, 'course_name' => $course_name ?? '', 'course_description' => $course_description ?? ''];
} else {
    // READ single row for the form
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
}

$title = 'Edit Course #' . $course['course_id'];
require dirname(__DIR__) . '/includes/header.php';
?>

<h1>Edit Course #<?= e($course['course_id']) ?></h1>
<p><a href="../index.php">&larr; Back to list</a></p>

<?php if ($error): ?>
    <div class="err"><?= e($error) ?><?php if ($error === 'Update failed.') print_r(sqlsrv_errors()); ?></div>
<?php endif; ?>

<form method="post" action="edit.php?id=<?= e($course['course_id']) ?>">
    <input type="hidden" name="id" value="<?= e($course['course_id']) ?>">
    <label>Course Name (required, max 250):
        <input type="text" name="course_name" maxlength="250" required value="<?= e($course['course_name']) ?>">
    </label>
    <label>Description (optional, max 1000):
        <textarea name="course_description" rows="4" maxlength="1000"><?= e($course['course_description']) ?></textarea>
    </label>
    <button class="btn btn-success mt" type="submit">Update</button>
    <a class="btn mt" href="../index.php">Cancel</a>
</form>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
