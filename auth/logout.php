<?php
require_once dirname(__DIR__) . '/includes/auth.php';

logoutUser();
$_SESSION['msg'] = 'You have been logged out.';
header('Location: /Activity_1/index.php');
exit;
