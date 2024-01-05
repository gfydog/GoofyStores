<?php
session_start();

// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Include configuration files and database connection.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

require "../../../config/common.php";

// Define the target directory for file uploads.
$target_dir = "../../../news_images/";

// Create the target directory if it does not exist.
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Get news information from POST data.
$title = htmlspecialchars($_POST['title']);
$content = sanitizeHtml($_POST['content']);
$image = htmlspecialchars($_POST['image']);
$description = htmlspecialchars($_POST['description']);

$author = htmlspecialchars($_POST['author']);
$category = "";  // Considerar cómo manejar la categoría
$tags = htmlspecialchars($_POST['tags']);
$is_featured = isset($_POST['is_featured']) ? 1 : 0;

// Insert news details into the database.
$sql = "INSERT INTO news (title, content, image, description, author, category, tags, main_image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssi", $title, $content, $image, $description, $author, $category, $tags, $new_main_image_name, $is_featured);

try {
    if ($stmt->execute()) {
        $response = ['success' => true];
        echo json_encode($response);
        header("location: ../index.php");
    } else {
        throw new Exception("Error executing SQL statement: " . $stmt->error);
    }
} catch (Exception $e) {
    $response = ['error' => 'Error: ' . $e->getMessage()];
    http_response_code(500);  // Internal Server Error
    echo json_encode($response);
}

// Close the database connection.
$conn->close();