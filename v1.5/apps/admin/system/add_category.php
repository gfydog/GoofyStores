<?php
/**
 * Description: This file handles the insertion of a new category into the database.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
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
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

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
