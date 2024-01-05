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
$news_id = $_POST['news_id'];
$title = htmlspecialchars($_POST['title']);
$content = sanitizeHtml($_POST['content']);
$image = htmlspecialchars($_POST['image']);
$description = htmlspecialchars($_POST['description']);

$author = htmlspecialchars($_POST['author']);
$category = "";  // Considerar cómo manejar la categoría
$tags = htmlspecialchars($_POST['tags']);
$is_featured = isset($_POST['is_featured']) ? 1 : 0;

// Update news details in the database.
$sql = "UPDATE news SET title=?, content=?, image=?, description=?, author=?, category=?, tags=?, is_featured=? WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssssssi", $title, $content, $image, $description, $author, $category, $tags, $is_featured, $news_id);

try {
    if ($stmt->execute()) {
        header("location: ../index.php");
    } else {
        throw new Exception("Error executing SQL statement: " . $stmt->error);
    }
} catch (Exception $e) {
    die('Error: ' . $e->getMessage());
}

// Close the database connection.
$conn->close();
?>
