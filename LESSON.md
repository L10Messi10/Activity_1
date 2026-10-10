# Activity 1 Lesson: PHP + SQL Server Course CRUD

Simple lesson for beginners. You will learn how to Connect, Create, Read, Update, and Delete.

## 1. What is CRUD?

CRUD = 4 basic jobs for any data:

- **C** = Create — add new row. Example: add `BSIT`
- **R** = Read — show rows. Example: list all courses
- **U** = Update — change a row. Example: rename `BSBA` to `BSBA-MM`
- **D** = Delete — remove a row. Example: delete a test course

Our table is `course`:

| Column | Type | Rule |
|---|---|---|
| `course_id` | int, auto-number | Primary key, you do NOT type it |
| `course_name` | varchar(250) | Required |
| `course_description` | varchar(1000) | Optional |
| `date_added` | datetime | Auto-filled with `GETDATE()` |

## 2. What you need

1. XAMPP with PHP + `sqlsrv` extension
2. SQL Server running
3. Database: `StudentDB_BSIT_A`
4. Table: `course` (above)
5. Project folder: `C:\xampp\htdocs\Activity_1`

Start Apache in XAMPP, then open:

```
http://localhost/Activity_1/index.php
```

## 3. Folder structure

We keep files grouped so it is easy to find things:

```
Activity_1/
  .env                  Secret settings, DO NOT share
  index.php             R = list all courses
  config/
    db.php              Connect to SQL Server using .env
  courses/
    add.php             C = add form + INSERT
    edit.php            U = edit form + UPDATE
    delete.php          D = confirm + DELETE
  includes/
    header.php          Top HTML + CSS link
    footer.php          Bottom HTML
    functions.php       Helper e() and fmtDate()
  assets/
    css/style.css       All design in one place
  LESSON.md             This lesson
```

Rule to remember:

- `config/` = connection only
- `courses/` = one file per job
- `includes/` = reused parts
- `assets/` = design only

## 4. Step 1: Connect with `.env`

`.env` holds secrets outside your code. Example only — use your own values:

```
DB_SERVER=YOUR_SERVER_NAME
DB_DATABASE=StudentDB_BSIT_A
DB_UID=sa
DB_PWD=YOUR_PASSWORD
DB_TRUST_CERT=true
```

`config/db.php` does 3 things:

1. Read `.env` line by line
2. Build `$connectionOptions`
3. Connect:

```php
require_once __DIR__ . '/config/db.php';
// Now you have $conn ready to use
$conn = sqlsrv_connect($serverName, $connectionOptions);
```

Every page starts with this line:

```php
// in index.php (root folder)
require_once __DIR__ . '/config/db.php';

// in courses/add.php (one folder deeper)
require_once dirname(__DIR__) . '/config/db.php';
```

If you see `.env file not found`, your path to `.env` is wrong.
If you see login error, check `DB_SERVER`, `DB_UID`, `DB_PWD`.

## 5. Step 2: Read — `index.php`

Read = `SELECT`. This lists newest first:

```php
$sql = "SELECT course_id, course_name, course_description, date_added
        FROM course ORDER BY course_id DESC";
$stmt = sqlsrv_query($conn, $sql);
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
  $courses[] = $row;
}
```

Then we loop and print a table:

```php
<?php foreach ($courses as $c): ?>
  <?= e($c['course_name']) ?>
<?php endforeach; ?>
```

`e()` means `htmlspecialchars()`. It stops HTML injection. Always use `e()` when printing data.

Links in the table:

- Edit: `courses/edit.php?id=1`
- Delete: `courses/delete.php?id=1`
- Add: `courses/add.php`

## 6. Step 3: Create — `courses/add.php`

Create = `INSERT`. Flow:

1. Show empty form (GET)
2. User clicks Save (POST)
3. Check: name not empty, name <= 250, description <= 1000
4. Insert with `?` placeholders:

```php
$sql = "INSERT INTO course (course_name, course_description, date_added)
        VALUES (?, ?, GETDATE())";
$params = [$course_name, $course_description];
$stmt = sqlsrv_query($conn, $sql, $params);
header('Location: ../index.php?msg=Course added successfully.');
```

`?` = prepared statement. It blocks SQL injection. Never put `$_POST` directly inside SQL text.

Form basics:

```html
<form method="post" action="add.php">
  <input type="text" name="course_name" required maxlength="250">
  <textarea name="course_description" maxlength="1000"></textarea>
  <button type="submit">Save</button>
</form>
```

## 7. Step 4: Update — `courses/edit.php`

Update = `UPDATE ... WHERE`. Flow:

1. Open `edit.php?id=2` — load that one row:

```php
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$sql = "SELECT course_id, course_name, course_description
        FROM course WHERE course_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
```

2. Show form filled with old values
3. User clicks Update (POST) — save:

```php
$sql = "UPDATE course SET course_name = ?, course_description = ?
        WHERE course_id = ?";
$params = [$course_name, $course_description, $id];
$stmt = sqlsrv_query($conn, $sql, $params);
```

Always keep `WHERE course_id = ?`. Without `WHERE`, you update ALL rows.

## 8. Step 5: Delete — `courses/delete.php`

Delete = `DELETE ... WHERE`. Flow:

1. Open `delete.php?id=3` — show name to confirm
2. User clicks Yes, Delete (POST):

```php
$sql = "DELETE FROM course WHERE course_id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
header('Location: ../index.php?msg=Course deleted successfully.');
```

Same rule: always keep `WHERE`. Test delete with a test row first.

## 9. How to use this project

1. Start XAMPP Apache
2. Open `http://localhost/Activity_1/index.php`
3. You see `Connected to SQL Server successfully` + table
4. Click **+ Add New Course**, type:
   - Name: `BSCS`
   - Description: `Test only`
   - Save — you go back to list with message
5. Click **Edit** on that row, change description, Update
6. Click **Delete** on that test row, confirm Yes
7. Done — you just did full CRUD

## 10. Common errors

| Error | Fix |
|---|---|
| `.env file not found` | Check `.env` is in `Activity_1/` root |
| `Login failed for user sa` | Wrong `DB_UID` / `DB_PWD` / `DB_SERVER` in `.env` |
| `Invalid course ID` | Link missing `?id=number`, example `edit.php?id=1` |
| Blank page | Run `php -l index.php` to check syntax |
| CSS not loading | Keep `<link href="/Activity_1/assets/css/style.css">` |
| Updated all rows | You forgot `WHERE course_id = ?` |

## 11. Safety checklist

- Use `?` params in every SQL with user input
- Use `e()` when echoing database or `$_POST`/`$_GET`
- Check `strlen()` limits: 250 for name, 1000 for description
- Check `id` with `FILTER_VALIDATE_INT`
- Never commit real `.env` password to GitHub

## 12. Try yourself (5 min each)

1. Add course `BEED` with description, find it in list
2. Edit `BEED` description, confirm change stays after refresh
3. Add empty name — what error do you get? Why is that good?
4. Look at `includes/functions.php` — where is `e()` used?
5. Bonus: sort list A-Z by changing `ORDER BY course_name ASC`

You now know the full cycle: `.env` -> `db.php` -> `SELECT` / `INSERT` / `UPDATE` / `DELETE`.
