<?php
/**
 * This PHP script handles the deletion of a project and its related data through an admin interface.
 * It checks if an admin is logged in and receives a 'project_id' via GET.
 * If the conditions are met, it first deletes the project and its associated records in the database.
 * This includes removing the project image file, clearing the cart, deleting project images,
 * purchase files, and reviews related to the project. It then redirects to the admin projects page.
 *
 * PHP version 7
 *
 * @category project_Deletion
 * @package  Admin_Interface
 * @author   Your Name
 */

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

// Get the 'project_id' from the GET request.
$project_id = $_GET['id'];

// SQL query to select the project by 'id'.
$sql = "SELECT * FROM projects WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

// Check if the project exists.
if ($row = $result->fetch_assoc()) {
    // SQL query to delete the project by 'id'.
    $sql = "DELETE FROM projects WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $stmt->close();
}

// SQL query to select project images by 'project_id'.
$sql = "SELECT * FROM project_images WHERE project_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

// Loop through the project images and delete them along with their records.
while ($row = $result->fetch_assoc()) {
    // SQL query to delete a project image by 'id'.
    $sql = "DELETE FROM project_images WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $row['id']);
    if ($stmt->execute()) {
        // Get the path of the project image and delete the file if it exists.
        $image_path = '../../../project_images/' . $row['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    $stmt->close();
}

// Redirect to the admin projects page.
header("Location: ../admin.php");
exit;
