<?php
/**
 * Description:
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Starting a PHP session.
session_start();

// Checking if the admin is authenticated.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Checking if an order ID is provided in the URL.
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: admin_orders.php");
    exit;
}

// Getting the order ID from the URL.
$purchase_id = intval($_GET['id']);

// Including necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// SQL query to retrieve the order details.
$sql = "SELECT p.id as purchase_id, p.purchase_date, IFNULL(u.username, 'Guest') as username, p.product_type as product_type,
        p.total,
        p.shipping_name, p.shipping_address, p.shipping_city, p.shipping_zip, p.shipping_country, u.email
        FROM purchases p
        LEFT JOIN users u ON p.user_id = u.id
        WHERE p.id = $purchase_id";

// Executing the SQL query.
$result = $conn->query($sql);

// Checking if the order is found.
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $purchase_id = $row['purchase_id'];
    $username = $row['username'];
    $email = $row['email'];
    $purchase_date = $row['purchase_date'];
    $product_type = $row['product_type'];
    $total = $row['total'];

    // Shipping details (if available).
    $shippingDetails = [];
    if ($product_type == 'physical') { // Getting shipping details for physical products only.
        $shippingDetails = [
            'email' => htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'),
            'name' => htmlspecialchars($row['shipping_name'], ENT_QUOTES, 'UTF-8'),
            'address' => htmlspecialchars($row['shipping_address'], ENT_QUOTES, 'UTF-8'),
            'city' => htmlspecialchars($row['shipping_city'], ENT_QUOTES, 'UTF-8'),
            'zip' => htmlspecialchars($row['shipping_zip'], ENT_QUOTES, 'UTF-8'),
            'country' => htmlspecialchars($row['shipping_country'], ENT_QUOTES, 'UTF-8')
        ];
    }

    // SQL query to retrieve files related to the order.
    $files_sql = "SELECT f.name, pf.quantity, f.price, f.type
                  FROM purchase_files pf 
                  INNER JOIN products f ON pf.file_id = f.id 
                  WHERE pf.purchase_id = $purchase_id";

    // Executing the SQL query to retrieve files.
    $files_result = $conn->query($files_sql);

    // Creating an HTML table to display order details.
    $orderDetailsHTML = "<h1>Order Details - Order ID: $purchase_id</h1>";
    $orderDetailsHTML .= "<table class='order-details'>";
    $orderDetailsHTML .= "<tr><th colspan='2'>Personal Information</th></tr>";
    $orderDetailsHTML .= "<tr><td>Username:</td><td>$username ($email)</td></tr>";
    $orderDetailsHTML .= "<tr><td>Purchase Date:</td><td>$purchase_date</td></tr>";
    $orderDetailsHTML .= "<tr><td>Total Amount:</td><td>$total</td></tr>";

    // Displaying shipping details if available.
    if (!empty($shippingDetails)) {
        $orderDetailsHTML .= "<tr><th colspan='2'>Shipping Details</th></tr>";
        $orderDetailsHTML .= "<tr><td>Email:</td><td>" . $shippingDetails['email'] . "</td></tr>";
        $orderDetailsHTML .= "<tr><td>Name:</td><td>" . $shippingDetails['name'] . "</td></tr>";
        $orderDetailsHTML .= "<tr><td>Address:</td><td>" . $shippingDetails['address'] . "</td></tr>";
        $orderDetailsHTML .= "<tr><td>City:</td><td>" . $shippingDetails['city'] . "</td></tr>";
        $orderDetailsHTML .= "<tr><td>ZIP:</td><td>" . $shippingDetails['zip'] . "</td></tr>";
        $orderDetailsHTML .= "<tr><td>Country:</td><td>" . $shippingDetails['country'] . "</td></tr>";
    }

    // Displaying purchased products.
    $orderDetailsHTML .= "<tr><th colspan='2'>Products Purchased</th></tr>";
    $orderDetailsHTML .= "<tr><th>Name</th><th>Quantity</th></tr>";
    while ($file_row = $files_result->fetch_assoc()) {

        $type = ($file_row['type'] == 1) ? "(Physical)" : "";
        $orderDetailsHTML .= "<tr>";
        $orderDetailsHTML .= "<td>" . htmlspecialchars($file_row['name'], ENT_QUOTES, 'UTF-8') . "</td>";
        $orderDetailsHTML .= "<td>" . $file_row['quantity'] . " " . $type . "</td>";
        $orderDetailsHTML .= "</tr>";
    }

    $orderDetailsHTML .= "</table>";
} else {
    // No order details found.
    $orderDetailsHTML = "<p>No details found for Order ID: $purchase_id</p>";
}

// Closing the database connection.
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .order-details {
            width: 90%;
            max-width: 800px;
            border-collapse: collapse;
            margin: 20px auto;
        }
        .order-details th,
        .order-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .order-details th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php require_once '../common/admin_header.php'; ?>
    <div class="container">
        <?php echo $orderDetailsHTML; ?>
    </div>
</body>
</html>
