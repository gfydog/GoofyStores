<?php
/**
 * PHP script for managing and displaying product administration in the admin interface.
 *
 * This script checks if an admin is logged in and retrieves a list of products from a backend system.
 * It then displays the products in a tabular format on the admin interface, allowing admins to edit or delete them.
 *
 * PHP version 7
 *
 * @category Admin_Products
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

// SQL query to retrieve product information from the database.
$sql = "SELECT id, name, description, price FROM products ORDER BY created_at DESC";
$result = $conn->query($sql);

$products = [];

// Fetch product data and store it in the $products array.
while ($row = $result->fetch_assoc()) {
    $products[] = $row;
}

// Close the database connection.
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>

<body>

    <?php require_once '../common/admin_header.php'; ?>
    <h1>Administer Products</h1>
    <div id="a-container">
        <a href="add_product.php">Add Product</a>
    </div>
    <div class="container-table">
        <table border="1">
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th colspan="2">Actions</th>
            </tr>
            <?php foreach ($products as $product) : ?>
                <tr>
                    <td><?php echo $product['name']; ?></td>
                    <td><?php echo $product['price']; ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>"><button>Edit</button></a>
                    </td>
                    <td>
                        <button onclick="showConfirm('Are you sure you want to delete this product?', () => { window.location.href = '<?= "./system/delete_product.php?id=" . $product['id']; ?>';});">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php require_once '../common/customBox.php'; ?>
    
</body>

</html>
