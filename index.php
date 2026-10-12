<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Counts for the landing cards (fail-safe: show "-" if query fails)
$courseCount = '-';
$studentCount = '-';

$stmt = @sqlsrv_query($conn, "SELECT COUNT(*) AS c FROM course");
if ($stmt !== false && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
    $courseCount = $row['c'];
    sqlsrv_free_stmt($stmt);
}

$stmt = @sqlsrv_query($conn, "SELECT COUNT(*) AS c FROM Students");
if ($stmt !== false && ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC))) {
    $studentCount = $row['c'];
    sqlsrv_free_stmt($stmt);
}

$title = 'Activity 1 — Home';
$active = 'home';
require __DIR__ . '/includes/header.php';
?>

<div class="p-5 mb-4 bg-light rounded-3 hero">
    <h1 class="display-6">StudentDB Manager</h1>
    <p class="lead mb-0">Activity 1: PHP + SQL Server CRUD. Pick a module below to manage records.</p>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card h-100">
            <div class="card-body">
                <h2 class="card-title h4">Courses</h2>
                <p class="count"><?= e($courseCount) ?> records</p>
                <p class="card-text">Reference CRUD (already done). View, add, edit, and delete courses.</p>
                <a class="btn btn-primary" href="courses/index.php">Manage Courses</a>
                <p class="mt-3 mb-0"><small class="text-muted">Example: <code>courses/index.php</code> &rarr; <code>add.php</code>, <code>edit.php?id=…</code>, <code>delete.php?id=…</code></small></p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card h-100 border-warning card-todo">
            <div class="card-body">
                <h2 class="card-title h4">Students <span class="badge text-bg-warning">Your task</span></h2>
                <p class="count"><?= e($studentCount) ?> records</p>
                <p class="card-text"><strong>Students must provide the working code</strong> for the Students CRUD. Create the <code>students/</code> folder yourself (see LESSON.md §13).</p>
                <a class="btn btn-outline-secondary" href="students/index.php">Open Students Module</a>
                <p class="mt-3 mb-0"><small class="text-muted">Required: <code>students/index.php</code>, <code>add.php</code>, <code>edit.php</code>, <code>delete.php</code></small></p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body how">
        <h3 class="h5">How this project is organized</h3>
        <pre>Activity_1/
  index.php             Landing page (you are here)
  courses/              Reference CRUD — done
    index.php           R = list all courses
    add.php             C = add form + INSERT
    edit.php            U = edit form + UPDATE
    delete.php          D = confirm + DELETE
  students/             YOUR task — you must create this
    index.php           R = list all students (TODO)
    add.php             C (TODO)
    edit.php            U (TODO)
    delete.php          D (TODO)
  config/db.php         Connection only
  includes/             header, footer, functions
  assets/css/           bootstrap.min.css + style.css (custom extras)</pre>
        <p class="mb-0"><a href="courses/index.php">Go to Courses &rarr;</a> &nbsp;·&nbsp; <a href="LESSON.md">Read LESSON.md</a></p>
    </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
