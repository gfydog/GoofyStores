<?php
/**
 * Description: This file manages the administration of orders, including pagination and displaying order details.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration files and establish a database connection.
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>

<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Orders Management</h1>
    <div id="orders"></div>
    <div id="pagination"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var page = 1;
            var totalPages = 1;

            function fetchOrders(page) {
                $.ajax({
                    url: "./system/get_orders.php",
                    data: { page: page },
                    dataType: "json",
                    success: function(data) {
                        var ordersHTML = "<table border='1'><tr><th>Order ID</th><th>Username</th><th>Date</th></tr>";

                        // Generate links for each order
                        for (var i = 0; i < data.length; i++) {
                            ordersHTML += "<tr>";
                            ordersHTML += "<td><a href='order.php?id=" + data[i].purchase_id + "'>See details</a></td>";
                            ordersHTML += "<td>" + data[i].username + "</td>";
                            ordersHTML += "<td>" + data[i].purchase_date + "</td>";
                            ordersHTML += "</tr>";
                        }

                        ordersHTML += "</table>";
                        $("#orders").html(ordersHTML);

                        totalPages = Math.ceil(data.length / 10);
                        renderPagination();
                    }
                });
            }

            function renderPagination() {
                var paginationHTML = "<span>Page: </span>";
                for (var i = 1; i <= totalPages; i++) {
                    paginationHTML += "<a href='#' data-page='" + i + "'>" + i + "</a>";
                }
                $("#pagination").html(paginationHTML);
            }

            // Pagination of orders
            $(document).on("click", "#pagination a", function(e) {
                e.preventDefault();
                var clickedPage = $(this).data("page");
                if (clickedPage !== page) {
                    page = clickedPage;
                    fetchOrders(page);
                }
            });

            fetchOrders(page);
        });
    </script>
</body>

</html>
