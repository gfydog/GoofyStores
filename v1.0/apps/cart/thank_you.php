<?php
/**
 * Start a new or resume the current session.
 */
session_start();

// Require the necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

/**
 * Redirect the user to the login page if they are not authenticated.
 */
if (!isset($_SESSION['user_id'])) {
    header("location: ../authentication/login.php");
}

/**
 * Get the user's ID from the session.
 */
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You for Your Purchase</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>
<body>
    <?php require_once '../common/header.php'; ?>

    <div class="container">
        <div class="invoice">
            <h2>Thank You for Your Purchase</h2>
            <p>Your payment has been successfully processed, and your purchase has been recorded in our database. <br> You can download your files from "My Files." <br> Thank you!</p>
            <br>
            <nav>
                <div>
                    <ul>
                        <li><a href="../account/my_files.php">My Files</a></li>
                        <li><a href="../public/index.php">Return to the Store</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</body>
</html>
