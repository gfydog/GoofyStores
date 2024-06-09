<?php
// Start a PHP session.
session_start();

/**
 * Unset the 'user_id' session variable to log the user out.
 * This effectively clears the user's session data.
 */
unset($_SESSION['user_id']);

// Redirect the user to the login page for a fresh session.
header("Location: login.php");
?>
