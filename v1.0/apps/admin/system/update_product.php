<?php
/**
 * This PHP script handles the update of product information through an admin interface.
 * It checks if an admin is logged in and processes form data to update product details, including name, description, price, and category.
 *
 * PHP version 7
 *
 * @category Product_Update
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Include configuration files and database connection.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Get product information from POST data.
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : "";
$description = isset($_POST['description']) ? htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8') : "";
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;
$category_id = intval($_POST['category_id']);

// Check if the provided data is valid.
if ($product_id <= 0 || empty($name) || empty($description) || $price < 0) {
    echo json_encode(['success' => false]);
    exit;
}

// Update the product details in the database.
$sql = "UPDATE products SET name = ?, description = ?, price = ?, category_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdii", $name, $description, $price, $category_id, $product_id);

// Check if the update was successful and respond with a JSON result.
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
