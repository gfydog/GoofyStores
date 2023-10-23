<?php
/**
 * This PHP script handles the deletion of a product and its related data through an admin interface.
 * It checks if an admin is logged in and receives a 'product_id' via GET.
 * If the conditions are met, it first deletes the product and its associated records in the database.
 * This includes removing the product image file, clearing the cart, deleting product images,
 * purchase files, and reviews related to the product. It then redirects to the admin products page.
 *
 * PHP version 7
 *
 * @category Product_Deletion
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Require configuration files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Get the 'product_id' from the GET request.
$product_id = $_GET['id'];

// SQL query to select the product by 'id'.
$sql = "SELECT * FROM products WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Check if the product exists.
if ($row = $result->fetch_assoc()) {
    // SQL query to delete the product by 'id'.
    $sql = "DELETE FROM products WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    if ($stmt->execute()) {
        // Get the path of the product image and delete the file if it exists.
        $image_path = "../../../files/" . $row['file_url'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt->close();
}

// SQL query to delete items from the cart with matching 'product_id'.
$sql = "DELETE FROM cart WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// SQL query to select product images by 'product_id'.
$sql = "SELECT * FROM product_images WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

// Loop through the product images and delete them along with their records.
while ($row = $result->fetch_assoc()) {
    // SQL query to delete a product image by 'id'.
    $sql = "DELETE FROM product_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $row['id']);
    if ($stmt->execute()) {
        // Get the path of the product image and delete the file if it exists.
        $image_path = '../../../product_images/' . $row['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt->close();
}

// SQL query to delete purchase files with matching 'file_id'.
$sql = "DELETE FROM purchase_files WHERE file_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// SQL query to delete reviews with matching 'product_id'.
$sql = "DELETE FROM reviews WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->close();

// Redirect to the admin products page.
header("Location: ../admin_products.php");
exit;
