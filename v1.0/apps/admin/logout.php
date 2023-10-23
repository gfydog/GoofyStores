<?php
/**
 * PHP script for logging out an admin user.
 *
 * This script is responsible for ending the current admin user's session, effectively logging them out of the web application. After destroying the session, it redirects the user to the admin login page for security reasons.
 *
 * PHP version 7
 *
 * @category Admin_Logout
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Unset and destroy the admin user's session.
unset($_SESSION['admin_id']);
session_destroy();

// Redirect to the admin login page for a secure logout.
header("Location: ../authentication/admin_login.php");
?>
