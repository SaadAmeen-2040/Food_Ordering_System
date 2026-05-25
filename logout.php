<?php
session_start();

// Unset user specific session variables
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);

// Unset admin session if applicable (in case they use the same logout logic, but we'll have a separate one in admin/logout.php)
unset($_SESSION['admin_id']);
unset($_SESSION['admin_username']);

$_SESSION['message'] = "You have been successfully logged out.";
header("Location: login.php");
exit;
?>
