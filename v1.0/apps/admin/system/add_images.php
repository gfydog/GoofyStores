<?php
/**
 * This PHP script handles the uploading and insertion of product images through an admin interface.
 * It checks if an admin is logged in and if the required 'product_id' and 'product_images' parameters
 * are provided via POST. If the conditions are met, it uploads the images, performs validation on file type
 * and size, and inserts the image information into the database. It then redirects to an edit product page
 * with a success message.
 *
 * PHP version 7
 *
 * @category Product_Images
 * @package  Image_Upload
 * @author   Raúl Méndez Rodríguez
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

// Check if the 'product_id' and 'product_images' parameters are provided via POST.
if (isset($_POST['product_id']) && isset($_FILES['product_images'])) {
    $product_id = intval($_POST['product_id']);
    $product_images = $_FILES['product_images'];
    $image_count = count($product_images['name']);
    $uploaded_images = [];

    for ($i = 0; $i < $image_count; $i++) {
        $image_name = basename($product_images['name'][$i]);
        $image_type = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_size = $product_images['size'][$i];

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_type, $allowed_extensions)) {
            die("File type not allowed. Only JPG, JPEG, PNG, and GIF images are allowed.");
        }

        if ($image_size > 20000000) {
            die("The image file size is too large. The maximum allowed size is 20 MB.");
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
            die("Error uploading the product image.");
        }
    }

    foreach ($uploaded_images as $image) {

        $sql_image = "INSERT INTO product_images (product_id, image) VALUES (?, ?)";
        $stmt_image = $conn->prepare($sql_image);
        $stmt_image->bind_param("is", $product_id, $image);

        if (!$stmt_image->execute()) {
            echo "Error: " . $sql_image . "<br>" . $conn->error;
        }
    }

    // Redirect to the edit product page with a success message.
    header("Location: ../edit_product.php?id=" . $product_id . "&success");
} else {
    die("No product images or product ID provided.");
}

// Close the database connection.
$conn->close();
