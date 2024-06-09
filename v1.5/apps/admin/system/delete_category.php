<?php
/**
 * Description: This file handles the deletion of a category from the database.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
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
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

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
