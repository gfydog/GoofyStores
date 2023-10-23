<?php
/**
 * This PHP script handles the deletion of a category through an API endpoint.
 * It checks if an admin is logged in and if a valid 'id' parameter is received via POST.
 * If the conditions are met, it deletes the category from the database and responds with
 * a JSON success status.
 *
 * PHP version 7
 *
 * @category Category_Deletion
 * @package  Category_API
 * @author   Raúl Méndez Rodríguez
 */

// Set the content type to JSON.
header("Content-Type: application/json");

// Start a PHP session.
session_start();

// Check if an admin is not logged in, redirect to the admin login page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../authentication/admin_login.php");
    exit;
}

// Require configuration files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Check if the 'id' parameter is received via POST.
if (isset($_POST['id'])) {
    $id = intval($_POST['id']);

    // SQL query to delete a category.
    $sql = "DELETE FROM categories WHERE id = ?";

    // Prepare the SQL statement.
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    // Execute the SQL statement and capture the result.
    $result = $stmt->execute();

    // Respond with a JSON success status.
    echo json_encode(array("success" => $result));
} else {
    // Respond with a JSON success status indicating failure.
    echo json_encode(array("success" => false));
}
?>