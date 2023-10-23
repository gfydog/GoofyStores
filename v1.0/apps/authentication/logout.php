<?php
/**
 * User Logout
 *
 * This script handles user logout by clearing the user's session data and redirecting them to the login page.
 *
 * PHP version 7
 *
 * @category User_Logout
 * @package  User_Interface
 */

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
