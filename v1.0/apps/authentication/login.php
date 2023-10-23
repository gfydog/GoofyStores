<?php
/**
 * User Login Page
 *
 * This script handles the user login process. It checks if the user is already authenticated and
 * allows them to log in by providing a username and password. It also provides a "Forgot Password"
 * feature for password recovery.
 *
 * PHP version 7
 *
 * @category User_Login
 * @package  User_Interface
 */

// Start a PHP session for user authentication.
session_start();

// Include necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

/**
 * Check if the user is already authenticated. If so, redirect to the user's control panel.
 */
if (isset($_SESSION['user_id'])) {
  header("location: ../public/index.php");
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="manifest" href="../../manifest.php">
</head>

<body>

  <?php require_once '../common/header.php'; ?>

  <div class="login-form">
    <h2 style="text-align: center;">Login</h2>
    <form action="./system/login_user.php" method="post">
      <input type="text" name="username" id="username" required placeholder="Username"><br>
      <input type="password" name="password" id="password" required placeholder="Password"><br>
      <button type="submit">Log In</button>
    </form>
    <div class="div-link">
      <a href="#" id="pass" class="pass">Forgot Password</a>
    </div>
  </div>
  <div class="div-link">
    <a href="admin_login.php" class="pass">Admin Site</a>
  </div>

  <?php require_once '../common/customBox.php'; ?>

  <script>
    $(document).ready(function() {
      $("#pass").on("click", function(e) {
        e.preventDefault(); // Prevent the link from performing its default behavior.

        var username = $("#username").val();

        // Check if the username field is not empty
        if (username.trim() !== "") {
          $.ajax({
            type: "POST",
            url: "./system/password.php",
            data: {
              username: username
            },
            success: function(response) {
              // Handle the response from "password.php" here
              // For example, display a message to the user:
              showAlert(response);
            },
            error: function(jqXHR, textStatus, errorThrown) {
              // Handle errors here
              showAlert("Error: " + errorThrown);
            }
          });
        } else {
          showAlert("Please enter your username.");
        }
      });
    });
  </script>
</body>

</html>