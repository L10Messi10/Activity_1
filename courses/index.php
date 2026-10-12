<?php
require_once dirname(__DIR__) . '/config/db.php';
require_once dirname(__DIR__) . '/includes/functions.php';

$message = $_GET['msg'] ?? '';
$error = '';
$courses = [];
$sql = "SELECT course_id, course_name, course_description, date_added FROM course ORDER BY course_id DESC";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    $error = 'Failed to load courses.';
} else {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $courses[] = $row;
    }
    sqlsrv_free_stmt($stmt);
}

$title = 'Course List';
$active = 'courses';
require dirname(__DIR__) . '/includes/header.php';
?>

<h1>Course List</h1>
<p><a href="../index.php">&larr; Back to Home</a></p>

<?php if ($message): ?>
    <div class="alert alert-success"><?= e($message) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= e($error) ?><?php print_r(sqlsrv_errors()); ?></div>
<?php endif; ?>

<p><a class="btn btn-primary" href="add.php">+ Add New Course</a></p>

<div class="table-responsive">
<table class="table table-striped table-bordered align-middle">
    <thead class="table-light">
    <thead>
        <tr>
            <th>ID</th>
            <th>Course Name</th>
            <th>Description</th>
            <th>Date Added</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($courses)): ?>
            <tr>
                <td colspan="5">No courses found.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($courses as $c): ?>
                <tr>
                    <td><?= e($c['course_id']) ?></td>
                    <td><?= e($c['course_name']) ?></td>
                    <td><?= e($c['course_description']) ?></td>
                    <td><?= fmtDate($c['date_added']) ?></td>
                    <td class="actions">
                        <a class="btn btn-sm btn-outline-primary" href="edit.php?id=<?= e($c['course_id']) ?>">Edit</a>
                        <a class="btn btn-sm btn-outline-danger" href="delete.php?id=<?= e($c['course_id']) ?>"
                            onclick="return confirm('Delete this course?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>
</div>

<?php require dirname(__DIR__) . '/includes/footer.php'; ?>
