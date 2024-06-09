<?php
/**
 * Start a new or resume the current session.
 */
session_start();

// Require the necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
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
            <h2>Thank you for your donation</h2>
            <br>
            <nav>
                <div>
                    <ul>
                        <li><a href="./index.php">Crowdfunding</a></li>
                        <li><a href="../public/index.php">Return to the Store</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</body>
</html>
