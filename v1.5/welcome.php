<?php
/**
 * This PHP script handles the product listing page and its associated functionalities.
 * It retrieves product data from the database based on search, filter, and order criteria.
 * Users can view and filter products, add them to their cart, and navigate through pages.
 */

// Start a session
session_start();

// Redirect to installation if the configuration file does not exist
if (!file_exists('./config/configFinal.php')) {
  header("location: ./install/index.php");
  exit;
}

// Require configuration and database files
require_once "./config/configFinal.php";
require_once "./config/database.php";
require_once "./config/config.php";

// Get user ID from the session, if available
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Page title -->
  <title><?= htmlspecialchars(TITLE) ?></title>

  <meta charset="UTF-8">
  <!-- Set cache control headers -->
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">
  
  <!-- Include common head content -->
  <?php require_once './apps/common/head.php'; ?>

  <!-- Include jQuery library -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Link to web app manifest -->
  <link rel="manifest" href="./manifest.php">
  <link rel="stylesheet" href="./assets/css/all.css">
</head>

<body>
  <!-- Include common header -->
  <?php require_once './apps/common/header.php'; ?>

  <div class="container">
    <div class="invoice">
      <h2>Gracias por usar Goofy Stores</h2>
      <br>
      <nav>
        <div>
          <ul>
            <li><a href="./apps/authentication/admin_login.php">Acceder como Administrador</a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>

</body>

</html>