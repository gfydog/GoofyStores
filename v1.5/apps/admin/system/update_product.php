<?php
/**
 * Description: This file handles the upload and update of configuration settings, including file uploads.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Include configuration files and database connection.
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

// Get product information from POST data.
$product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
$name = isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : "";
$description = isset($_POST['description']) ? $_POST['description'] : "";
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.00;
$category_id = intval($_POST['category_id']);
$type = intval($_POST['type']); // Convierte el tipo de producto a un entero
$stock_quantity = intval($_POST['stock_quantity']);

// Check if the provided data is valid.
if ($product_id <= 0 || empty($type) || empty($name) || empty($description) || $price < 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
    exit;
}

// Check if a file is provided.
if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
    // Process the uploaded file.
    $file_tmp_name = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'];
    $file_error = $_FILES['file']['error'];
    $file_type = pathinfo($file_name, PATHINFO_EXTENSION);
    $file_size = $_FILES["file"]["size"];

    // Move the uploaded file to a directory.
    $new_file_name = uniqid('', true) . "." . $file_type;
    $upload_directory = "../../../files/";
    $new_file_path = $upload_directory . $new_file_name;

    // Delete the old file if it exists.
    $old_product = getProductById($conn, $product_id);
    if ($old_product && !empty($old_product['file_url'])) {
        $old_file_path = "../../../files/" . $old_product['file_url'];
        if (file_exists($old_file_path)) {
            unlink($old_file_path);
        }
    }

    // Define allowed file extensions and size limit.
    $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'mp3', 'mp4'];

    // Check if the uploaded file type is allowed and within size limits.
    if (!in_array($file_type, $allowed_extensions)) {
        echo json_encode(['success' => false, 'message' => 'File type not allowed. Please check the allowed extensions.']);
        exit;
    }

    // Move the uploaded file to the target directory.
    if (!move_uploaded_file($file_tmp_name, $new_file_path)) {
        echo json_encode(['success' => false, 'message' => 'Error uploading file']);
        exit;
    }

    $sql = "UPDATE products SET file_url = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_file_name, $product_id);
    if (!$stmt->execute()) {
        echo json_encode(['success' => false, 'message' => 'Error updating file path in database']);
        exit;
    }
}

// Update the product details in the database.
$sql = "UPDATE products SET stock_quantity = ?, type = ?, name = ?, description = ?, price = ?, category_id = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iissdii", $stock_quantity, $type, $name, $description, $price, $category_id, $product_id);

// Check if the update was successful and respond with a JSON result.
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating product']);
}

$stmt->close();
$conn->close();

// Function to get product details by ID from the database.
function getProductById($conn, $product_id) {
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}
?>
