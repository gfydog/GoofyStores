<?php
/**
 * This PHP script handles the file upload for adding projects via the admin interface.
 * It checks if an admin is logged in, allows them to upload project images,
 * and provides form fields for entering project details.
 *
 * PHP version 7
 *
 * @category Project_Upload
 * @package  Admin_Interface
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration files and establish a database connection.
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Project</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="manifest" href="../../manifest.php">
</head>

<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Upload Project</h1>
    <form action="./system/upload_project.php" method="post" enctype="multipart/form-data">
        <label for="project_images">Images:</label>
        <input type="file" name="project_images[]" id="project_images" multiple accept="image/*" required><br>
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br>
        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea><br>
        <label for="goal">Goal:</label>
        <input type="number" step="0.01" name="goal" id="goal" required><br>
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" required><br>
        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" required><br>

        <button type="submit">Upload Project</button>
    </form>
</body>

</html>
