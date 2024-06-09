<?php
/**
 * Start a session or resume the current session.
 */
session_start();

// Require necessary configuration and database files
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

/**
 * Redirect to the login page if the user is not authenticated.
 */
if (!isset($_SESSION['user_id'])) {
    header("location: ../authentication/login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Canceled</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>
<body>
    <?php require_once '../common/header.php'; ?>

    <div class="container">
        <div class="invoice">
            <h2>Payment Not Completed</h2>
            <p>The PayPal payment was canceled.</p>
            <br>
            <nav>
                <div>
                    <ul>
                        <li><a href="../public/index.php">Return to the Store</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</body>
</html>
