<?php
/**
 * Description: This file manages the administration of available files for products.
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

// Include configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// SQL query to retrieve product files information from the database.
$sql = "SELECT * FROM products";
$result = $conn->query($sql);
$products = [];

// Fetch product files data and store it in the $products array.
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}
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
    <h1>Available Files</h1>
    <table border="1">
        <?php foreach ($products as $product) : 
            if (!empty($product['file_url'])) { ?>
            <tr>
                <td>
                    <a href="../account/download.php?id=<?php echo $product['id']; ?>">
                        <?php echo $product['name']; ?>
                    </a>
                </td>
            </tr>
        <?php } endforeach; ?>
    </table>
</body>

</html>
