<?php
/**
 * Description: 
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

/**
 * Check if the user is already logged in. If yes, redirect them to the main index page.
 */
if (isset($_SESSION['user_id'])) {
  header("location: ../public/index.php");
}

// Include necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <link rel="stylesheet" href="../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="manifest" href="../../manifest.php">
</head>

<body>

  <?php require_once '../common/header.php'; ?>

  <div class="login-form">
    <h2 style="text-align: center;">Register</h2><br>

    <!-- User registration form -->
    <form action="./system/register_user.php" method="post">
      <input type="email" name="email" id="email" required placeholder="Email"><br>
      <input type="text" name="username" id="username" required placeholder="Username"><br>
      <input type="password" name="password" id="password" required placeholder="New Password"><br>
      <button type="submit">Register</button>
    </form>
  </div>
  
  <!-- Link to the admin login page -->
  <div class="div-link">
    <a href="admin_login.php" class="pass">Admin Site</a>
  </div>
</body>

</html>
