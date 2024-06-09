<?php
// Start a session to manage user data across requests.
session_start();

// Require configuration and database files.
require "../../config/common.php";
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

$user_id = getUserID();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compra ya procesada</title>
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
            <h2>Compra ya procesada</h2>
            La compra asociada a esta transacción de PayPal ya ha sido procesada previamente.
        </div>
    </div>

</body>

</html>
