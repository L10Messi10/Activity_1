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

We keep files grouped so it is easy to find things.
`index.php` is now a **landing page**. The Course list moved to `courses/index.php`:

```
Activity_1/
  .env                  Secret settings, DO NOT share
  index.php             Landing page (Home with links to Courses + Students)
  config/
    db.php              Connect to SQL Server using .env
  courses/              Reference CRUD — already done, use as pattern
    index.php           R = list all courses
    add.php             C = add form + INSERT
    edit.php            U = edit form + UPDATE
    delete.php          D = confirm + DELETE
  students/             NOT provided — YOU must create this (see §13)
  includes/
    header.php          Top HTML + navbar + CSS link
    footer.php          Bottom HTML + footer
    functions.php       Helper e() and fmtDate()
  assets/
    css/bootstrap.min.css  Bootstrap framework (loaded first)
    css/style.css       Custom extras on top of Bootstrap
  LESSON.md             This lesson
```

Rule to remember:

- `index.php` (root) = landing only, no CRUD logic except counts
- `config/` = connection only
- `courses/` = one file per job (reference implementation)
- `students/` = your implementation, mirror `courses/` pattern
- `includes/` = reused parts (header/footer auto-adds navbar)
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
// in index.php (root landing page)
require_once __DIR__ . '/config/db.php';

// in courses/index.php, courses/add.php (one folder deeper)
require_once dirname(__DIR__) . '/config/db.php';
```

If you see `.env file not found`, your path to `.env` is wrong.
If you see login error, check `DB_SERVER`, `DB_UID`, `DB_PWD`.

## 5. Step 2: Read — `courses/index.php`

Read = `SELECT`. This lists newest first (file lives in `courses/`, opened via landing page):

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

Links in the table (all inside `courses/` now):

- Add: `courses/add.php` (link `add.php` from `courses/index.php`)
- Edit: `courses/edit.php?id=1` (link `edit.php?id=1`)
- Delete: `courses/delete.php?id=1` (link `delete.php?id=1`)
- Landing: `index.php` root links to `courses/index.php` and `students/index.php`

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
header('Location: index.php?msg=Course added successfully.');
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
header('Location: index.php?msg=Course deleted successfully.');
```

Same rule: always keep `WHERE`. Test delete with a test row first.

## 9. How to use this project

1. Start XAMPP Apache
2. Open landing page `http://localhost/Activity_1/index.php`
3. You see `StudentDB Manager` with two cards: Courses + Students
4. Click **Manage Courses** → `courses/index.php` shows course table
5. Click **+ Add New Course**, type:
   - Name: `BSCS`
   - Description: `Test only`
   - Save — you go back to `courses/index.php` with message
6. Click **Edit** on that row, change description, Update
7. Click **Delete** on that test row, confirm Yes
8. Click **Home** in navbar to return to landing page
9. Done — you just did full Course CRUD. Students CRUD is your task (§13).

## 10. Common errors

| Error | Fix |
|---|---|
| `.env file not found` | Check `.env` is in `Activity_1/` root |
| `Login failed for user sa` | Wrong `DB_UID` / `DB_PWD` / `DB_SERVER` in `.env` |
| `Invalid course ID` | Link missing `?id=number`, example `edit.php?id=1` |
| Blank page | Run `php -l index.php` to check syntax |
| CSS not loading | Keep both `<link>`s in `includes/header.php`: `bootstrap.min.css` first, then `style.css` |
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

## 13. Assignment: Students CRUD — YOU must provide the working code

> **NOTE: No starter code is provided for `students/`. Students must provide the working code for the Students CRUD themselves. Copy the `courses/` pattern, do not copy-paste blindly — adapt it to the `Students` table.**

### 13.1 What to build

Create this folder yourself (landing page already links to it):

```
students/
  index.php   R = list all students (SELECT ... FROM Students)
  add.php     C = add form + INSERT INTO Students
  edit.php    U = edit form + UPDATE Students WHERE StudentID = ?
  delete.php  D = confirm + DELETE FROM Students WHERE StudentID = ?
```

Requirements:

1. `students/index.php` must list rows newest-first, with Edit/Delete links `edit.php?​id=…` / `delete.php?​id=…` and an `add.php` button, plus a `../index.php` Back to Home link.
2. Use `require_once dirname(__DIR__) . '/config/db.php'` + `includes/functions.php` + `includes/header.php` (set `$active = 'students'`) exactly like `courses/`.
3. Every SQL with user input must use `?` placeholders (`sqlsrv_query($conn, $sql, $params)`).
4. Every echo of DB / `$_GET` / `$_POST` must use `e()`. Validate `id` with `FILTER_VALIDATE_INT`.
5. After successful C/U/D, `header('Location: index.php?msg=...')` back to `students/index.php`.
6. Match the existing visual style (Bootstrap: `btn`, `card`, `alert`, `table`, `form-control` + `assets/css/style.css` extras).

### 13.2 Students table schema (SQL Server)

```sql
-- Table: Students
StudentID       int IDENTITY PRIMARY KEY,
FirstName       varchar(50)  NULL,
MiddleName      varchar(50)  NULL,
LastName        varchar(50)  NULL,
Gender          varchar(10)  NULL,
Age             int          NULL,
Section         char(1)      NULL,
Score           decimal      NULL,
EnrollmentDate  date         NULL,
IsActive        bit          NULL,
Email           varchar(100) NULL,
course_name     varchar(250) NULL
```

Inspect it live with:

```sql
SELECT COLUMN_NAME, DATA_TYPE, CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_NAME = 'Students'
ORDER BY ORDINAL_POSITION;
```

### 13.3 Suggested steps

1. Create `students/index.php` first: `SELECT StudentID, FirstName, LastName, Email, course_name ... FROM Students ORDER BY StudentID DESC`, loop to table.
2. Create `students/add.php`: form POST → validate → `INSERT INTO Students (...) VALUES (?, ...)`, redirect with msg.
3. Create `students/edit.php`: GET `id` → `SELECT ... WHERE StudentID = ?` → filled form → POST `UPDATE ... WHERE StudentID = ?`.
4. Create `students/delete.php`: GET `id` → show name → POST `DELETE WHERE StudentID = ?`.
5. Test from landing page: Home → Open Students Module → Add → Edit → Delete → Home.

### 13.4 Grading checklist

- [ ] All 4 files exist and run without warnings
- [ ] No SQL injection (all `?` params), no XSS (all `e()`)
- [ ] `WHERE StudentID = ?` present on UPDATE and DELETE
- [ ] Navbar highlights Students, Back to Home works
- [ ] Landing count for Students updates after add/delete
