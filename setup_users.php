<?php
// Setup users table - run once to create the users table
require_once __DIR__ . '/config/db.php';

$sql = "
IF NOT EXISTS (SELECT * FROM sys.tables WHERE name = 'users')
CREATE TABLE users (
    user_id INT IDENTITY(1,1) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    created_at DATETIME DEFAULT GETDATE(),
    is_active BIT DEFAULT 1
)";

$stmt = sqlsrv_query($conn, $sql);
if ($stmt === false) {
    die('Failed to create users table: ' . print_r(sqlsrv_errors(), true));
}
sqlsrv_free_stmt($stmt);
echo 'users table created successfully.';
echo '<p><a href="/Activity_1/auth/signup.php">Go to Signup</a></p>';
