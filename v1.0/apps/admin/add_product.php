<?php
/**
 * This PHP script handles the file upload for adding products via the admin interface.
 * It checks if an admin is logged in, allows them to upload product images and a file,
 * and provides form fields for entering product details.
 *
 * PHP version 7
 *
 * @category Product_Upload
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
  header("Location: ../authentication/admin_login.php");
  exit;
}

// Include configuration files and establish a database connection.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin</title>
  <link rel="stylesheet" href="../../assets/css/admin.css">
  <link rel="manifest" href="../../manifest.php">
</head>

<body>
  <?php require_once '../common/admin_header.php'; ?>
  <h1>Upload Files</h1>
  <form action="./system/upload.php" method="post" enctype="multipart/form-data">
    <label for="product_images">Images:</label>
    <input type="file" name="product_images[]" id="product_images" multiple accept="image/*" required><br>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required><br>
    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea><br>
    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" id="price" required><br>
    <label for="category_id">Category:</label>
    <select name="category_id" id="category_id">
      <?php
      // Populate the dropdown with available categories from the database.
      $sql = "SELECT * FROM categories";
      $result = $conn->query($sql);
      while ($row = $result->fetch_assoc()) {
        echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
      }
      ?>
    </select><br>
    <label for="file">File:</label>
    <input type="file" name="file" id="file" required><br>
    <button type="submit">Upload File</button>
  </form>
</body>
</html>
