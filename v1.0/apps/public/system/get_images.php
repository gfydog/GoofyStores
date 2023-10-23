<?php
/**
 * This PHP script returns product images in JSON format.
 */

session_start();

require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

header('Content-Type: application/json');

// Check if the user is logged in as an administrator.
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'You must log in as an administrator to view product images']);
    exit;
}

// Get the product ID from the GET parameters.
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if a valid product ID was provided.
if ($product_id <= 0) {
    echo json_encode(['error' => 'You must provide a valid product ID']);
    exit;
}

// Query the database to select product images for the given product ID.
$sql = "SELECT * FROM product_images WHERE product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();

$images = [];

// Fetch product images and store them in an array.
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'id' => $row['id'],
            'image' => 'product_images/' . $row['image']
        ];
    }
}

// Encode the array as JSON and echo it.
echo json_encode($images);

$stmt->close();
$conn->close();
?>
