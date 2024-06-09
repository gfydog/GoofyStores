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

// Function to handle file upload for digital products.
function handleDigitalProductFileUpload() {
    global $conn;

    // Check if the product type is digital and a file is provided.
    if (!empty($_FILES['file']['name'])) {
        // Handle file upload for digital products.
        $target_dir = "../../../files/";

        // Create the target directory if it does not exist.
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Define variables for product file handling.
        $file_name = basename($_FILES["file"]["name"]);
        $file_type = pathinfo($file_name, PATHINFO_EXTENSION);

        // Define allowed file extensions and size limit.
        $allowed_extensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'csv', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'zip', 'rar', 'mp3', 'mp4'];

        // Check if the uploaded file type is allowed.
        if (!in_array($file_type, $allowed_extensions)) {
            die("File type not allowed. Please check the allowed extensions.");
        }

        // Generate a unique file name and set the target file path.
        $new_file_name = uniqid('', true) . "." . $file_type;
        $target_file = $target_dir . $new_file_name;

        // Move the uploaded file to the target directory.
        if (!move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            die("Error uploading the file.");
        }

        return $new_file_name; // Return the new file name
    } elseif ($_POST['type'] === '2') {
        // If the product type is digital but no file is provided, show an error message.
        die("Please upload a file for digital products.");
    }

    return null;
}

// Get product information from POST data.
$name = htmlspecialchars($_POST['name']);
$description = $_POST['description'];
$price = floatval($_POST['price']);
$category_id = intval($_POST['category_id']);
$type = intval($_POST['type']); // Convert product type to an integer
$stock_quantity = intval($_POST['stock_quantity']); // Convert product type to an integer

// Handle file upload for digital products.
$file_url = handleDigitalProductFileUpload();

// Insert product details into the database.
$sql = "INSERT INTO products (name, description, price, category_id, file_url, type, stock_quantity) VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdisii", $name, $description, $price, $category_id, $file_url, $type, $stock_quantity);

if ($stmt->execute()) {
    $product_id = $conn->insert_id;

    // Insert uploaded images into the product_images table.
    if (!empty($_FILES['product_images'])) {
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

        $sql_image = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
        $stmt_image = $conn->prepare($sql_image);
        $stmt_image->bind_param("is", $product_id, $image);

        foreach ($uploaded_images as $image) {
            if (!$stmt_image->execute()) {
                echo "Error: " . $sql_image . "<br>" . $conn->error;
            }
        }
    }

    header("Location: ../admin_products.php?success");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Close the database connection.
$conn->close();
?>
