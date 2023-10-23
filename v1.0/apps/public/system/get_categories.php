<?php
/**
 * This PHP script fetches and returns a JSON list of categories.
 */

header("Content-Type: application/json");

session_start();
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Query the database to select all categories.
$sql = "SELECT * FROM categories";
$result = $conn->query($sql);

$categories = array();

// Fetch the categories and store them in an array.
while ($row = $result->fetch_assoc()) {
    $categories[] = $row;
}

// Encode the array as JSON and echo it.
echo json_encode($categories);
?>
