<?php
/**
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Unset and destroy the admin user's session.
unset($_SESSION['admin_id']);
session_destroy();

// Redirect to the admin login page for a secure logout.
header("Location: ../authentication/admin_login.php");
?>
