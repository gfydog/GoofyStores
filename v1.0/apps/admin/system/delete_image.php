<?php
/**
 * This PHP script handles the deletion of a product image through an admin interface.
 * It checks if an admin is logged in and if a valid 'id' parameter is received via GET.
 * If the conditions are met, it attempts to delete the image file, remove its database record,
 * and then redirects to the edit product page.
 *
 * PHP version 7
 *
 * @category Image_Deletion
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

// Get the 'id' parameter from the GET request.
$image_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// If 'id' is not a valid value, redirect to the admin products page.
if ($image_id <= 0) {
    header("Location: ../admin_products.php");
    exit;
}

// SQL query to select the product image by 'id'.
$sql = "SELECT * FROM product_images WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $image_id);
$stmt->execute();
$result = $stmt->get_result();

// Check if the image exists.
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_path = '../../../product_images/' . $row['image'];

    // Check if the image file exists and delete it.
    if (file_exists($image_path)) {
        unlink($image_path);
    }

    // SQL query to delete the image from the database.
    $sql_delete = "DELETE FROM product_images WHERE id = ?";
    $stmt_delete = $conn->prepare($sql_delete);
    $stmt_delete->bind_param("i", $image_id);

    // Execute the deletion query.
    if ($stmt_delete->execute()) {
        // Redirect to the edit product page.
        header("Location: ../edit_product.php?id=" . $row['product_id']);
    } else {
        echo "Error deleting the image.";
    }
} else {
    // If the image doesn't exist, redirect to the admin products page.
    header("Location: ../admin_products.php");
}

// Close database connections.
$stmt->close();
$conn->close();
