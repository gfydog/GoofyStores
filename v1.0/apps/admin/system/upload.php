<?php
/**
 * This PHP script handles the addition of new products through an admin interface.
 * It checks if an admin is logged in, processes form data to add a new product, and uploads product images and files.
 *
 * PHP version 7
 *
 * @category Product_Addition
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

// Define the target directory for file uploads.
$target_dir = "../../../files/";

// Create the target directory if it does not exist.
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Get product information from POST data.
$name = htmlspecialchars($_POST['name']);
$description = htmlspecialchars($_POST['description']);
$price = floatval($_POST['price']);
$category_id = intval($_POST['category_id']);

// Define variables for file upload handling.
$fill = basename($_FILES["file"]["name"]);
$target_file = $target_dir . $fill;
$file_url = $target_file;

// Check if product images are provided.
if (isset($_FILES['product_images'])) {
    $product_images = $_FILES['product_images'];
    $image_count = count($product_images['name']);
    $uploaded_images = [];

    // Process and validate uploaded product images.
    for ($i = 0; $i < $image_count; $i++) {
        $image_name = basename($product_images['name'][$i]);
        $image_type = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_size = $product_images['size'][$i];

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_type, $allowed_extensions)) {
            die("File type not allowed. Only JPG, JPEG, PNG, and GIF images are permitted.");
        }

        if ($image_size > 10000000) {
            die("Image file size is too large. The maximum allowed size is 10 MB.");
        }

        $upload_dir = '../../../product_images/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        $new_image_name = uniqid() . "." . $image_type;
        $upload_path = $upload_dir . $new_image_name;

        if (move_uploaded_file($product_images['tmp_name'][$i], $upload_path)) {
            $uploaded_images[] = $new_image_name;
        } else {
            die("Error uploading product image.");
        }
    }
} else {
    die("No product images provided.");
}

// Define variables for product file handling.
$file_name = basename($_FILES["file"]["name"]);
$file_type = pathinfo($file_name, PATHINFO_EXTENSION);
$file_size = $_FILES["file"]["size"];

// Define allowed file extensions and size limit.
$allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'mp3', 'mp4'];
$size_limit = 20485760; // 20 MB in bytes

// Check if the uploaded file type is allowed and within size limits.
if (!in_array($file_type, $allowed_extensions)) {
    die("File type not allowed. Please check the allowed extensions.");
}

if ($file_size > $size_limit) {
    die("File size is too large. The maximum allowed size is 20 MB.");
}

// Generate a unique file name and set the target file path.
$new_file_name = uniqid('', true) . "." . $file_type;
$target_file = $target_dir . $new_file_name;
$file_url = $target_file;

// Move the uploaded file to the target directory.
if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
    // Insert product details into the database.
    $sql = "INSERT INTO products (name, description, price, category_id, file_url) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdis", $name, $description, $price, $category_id, $new_file_name);

    if ($stmt->execute()) {
        $product_id = $conn->insert_id;

        // Insert uploaded images into the product_images table.
        $sql_image = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
        $stmt_image = $conn->prepare($sql_image);
        $stmt_image->bind_param("is", $product_id, $image);

        foreach ($uploaded_images as $image) {
            if (!$stmt_image->execute()) {
                echo "Error: " . $sql_image . "<br>" . $conn->error;
            }
        }

        header("Location: ../admin_products.php?success");
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
} else {
    echo "Error uploading the file.";
}

// Close the database connection.
$conn->close();
