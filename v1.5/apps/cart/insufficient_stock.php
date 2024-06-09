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
    <title>Stock Insuficiente</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <style>
        .message{
            text-align: center;
            width: 90%;
            max-width: 500px;
            margin: 10px auto;
        }
    </style>
</head>

<body>

    <?php require_once '../common/header.php'; ?>

    <div class="container">
        <div class="message">
            <h2>Stock Insuficiente</h2>
            <p>Lo sentimos, no hay suficiente stock disponible para completar tu compra.</p>
        </div>
    </div>

</body>

</html>
