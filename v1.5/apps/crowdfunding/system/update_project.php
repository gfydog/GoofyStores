<?php
/**
 * This PHP script handles the update of project information through an admin interface.
 * It checks if an admin is logged in and processes form data to update project details, including title, description, goal, start date, and end date.
 *
 * PHP version 7
 *
 * @category Project_Update
 * @package  Admin_Interface
 */

// Start a PHP session.
session_start();


error_reporting(E_ALL);
ini_set('display_errors', 1);


// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Include configuration files and database connection.
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

// Get project information from POST data.
$project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
$title = isset($_POST['title']) ? htmlspecialchars($_POST['title'], ENT_QUOTES, 'UTF-8') : "";
$description = isset($_POST['description']) ? $_POST['description'] : "";
$goal = isset($_POST['goal']) ? floatval($_POST['goal']) : 0.00;
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : "";
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : "";

// Check if the provided data is valid.
if ($project_id <= 0 || empty($title) || empty($description) || $goal < 0 || empty($start_date) || empty($end_date)) {
    echo json_encode(['success' => false]);
    exit;
}

// Update the project details in the database.
$sql = "UPDATE projects SET title = ?, description = ?, goal = ?, start_date = ?, end_date = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdssi", $title, $description, $goal, $start_date, $end_date, $project_id);

// Check if the update was successful and respond with a JSON result.
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}

$stmt->close();
$conn->close();
?>
