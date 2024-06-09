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

// Get the news ID from the request.
$news_id = $_GET['id'];

// Check if the news ID is valid.
if (!is_numeric($news_id)) {
    die('Invalid news ID.');
}

// Delete the news entry from the database.
$sql = "DELETE FROM news WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $news_id);

try {
    if ($stmt->execute()) {
        // Optionally, you can also delete associated images or perform other cleanup tasks.

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
?>