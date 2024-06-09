<?php
/**
 * Description: 
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session for user authentication.
session_start();

// Include necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

/**
 * Check if the admin is already authenticated. If so, redirect to the admin control panel.
 */
if (isset($_SESSION['admin_id'])) {
    header("location: ../admin/admin_products.php");
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Control Panel</title>
  <link rel="stylesheet" href="../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="manifest" href="../../manifest.php">
</head>

<body>

  <?php require_once '../common/admin_header.php'; ?>

  <div class="login-form">
    <h2 style="text-align: center;">Login</h2>
    <form action="./system/admin_authenticate.php" method="post">
      <input type="text" name="username" id="username" required placeholder="Username"><br>
      <input type="password" name="password" id="password" required placeholder="Password"><br>
      <button type="submit">Log In</button>
    </form>
    <div class="div-link">
      <a href="#" id="pass" class="pass">Forgot Password</a>
    </div>
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
            url: "./system/admin_password.php",
            data: {
              username: username
            },
            success: function(response) {
              // Here, you can handle the response from "admin_password.php"
              // For example, show a message to the user:
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
