<?php
session_start();

// Set a flash message
$_SESSION['logout_success'] = true;

// Unset user-specific session variables
unset($_SESSION['username']);

// Redirect ke halaman login
header('Location: login.php');
exit();
?>
