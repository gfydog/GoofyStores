<?php
/**
 * PHP script for managing and displaying orders in the admin interface.
 *
 * This script checks if an admin is logged in and retrieves orders from a backend system.
 * It then displays the orders in a tabular format on the admin interface.
 *
 * PHP version 7
 *
 * @category Admin_Orders
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="manifest" href="../../manifest.php">
</head>

<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Order Management</h1>
    <div id="orders"></div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Use AJAX to fetch orders from the backend.
            $.ajax({
                url: "./system/get_orders.php",
                dataType: "json",
                success: function(data) {
                    var ordersHTML = "<div class='container-table'><table border='1'><tr><th>ID</th><th>User</th><th>Files</th><th>Date</th></tr>";
                    for (var i = 0; i < data.length; i++) {
                        ordersHTML += "<tr>";
                        ordersHTML += "<td>" + data[i].id + "</td>";
                        ordersHTML += "<td>" + data[i].username + "</td>";
                        ordersHTML += "<td>";
                        for (var j = 0; j < data[i].files.length; j++) {
                            ordersHTML += data[i].files[j].name + " (Quantity: " + data[i].files[j].quantity + ")<br>";
                        }
                        ordersHTML += "</td>";
                        ordersHTML += "<td>" + data[i].date + "</td>";
                        ordersHTML += "</tr>";
                    }
                    ordersHTML += "</table></div>";
                    $("#orders").html(ordersHTML);
                }
            });
        });
    </script>
</body>

</html>
