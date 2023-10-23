<?php
/**
 * This PHP script handles the creation of categories through an API endpoint.
 * It checks if an admin is logged in, and if a valid 'name' parameter is received via POST.
 * If the conditions are met, it inserts a new category into the database and responds with
 * a JSON success status.
 *
 * PHP version 7
 *
 * @category Category_Creation
 * @package  Category_API
 * @author   Raúl Méndez Rodríguez
 */

// Set the content type to JSON.
header("Content-Type: application/json");

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

// Check if the 'name' parameter is received via POST.
if (isset($_POST['name'])) {
    $name = trim($_POST['name']);

    // SQL query to insert a new category.
    $sql = "INSERT INTO categories (name) VALUES (?)";

    // Prepare the SQL statement.
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $name);

    // Execute the SQL statement and capture the result.
    $result = $stmt->execute();

    // Respond with a JSON success status.
    echo json_encode(array("success" => $result));
} else {
    // Respond with a JSON success status indicating failure.
    echo json_encode(array("success" => false));
}
?>
