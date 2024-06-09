<?php
/**
 * Description: This file handles the upload of product details, including images and files, in the admin panel.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
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
  <title>Admin</title>
  <link rel="stylesheet" href="../../assets/css/admin.css">
  <link rel="manifest" href="../../manifest.php">
</head>

<body>
  <?php require_once '../common/admin_header.php'; ?>
  <h1>Upload Files</h1>
  <form id="productForm" action="./system/upload.php" method="post" enctype="multipart/form-data">
    <label for="product_images">Images:</label>
    <input type="file" name="product_images[]" id="product_images" multiple accept="image/*" required><br>
    <label for="type">Product Type:</label>
    <select name="type" id="type" required>
      <option value="1">Physical</option>
      <option value="2">Digital</option>
    </select><br>
    <label for="name">Name:</label>
    <input type="text" name="name" id="name" required><br>
    <label for="description">Description:</label>
    <textarea name="description" id="description" required></textarea><br>
    <label for="price">Price:</label>
    <input type="number" step="0.01" name="price" id="price" required><br>
    <label for="price">Stock Quantity:</label>
    <input type="number" name="stock_quantity" id="stock_quantity" required><br>
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
    <div id="fileField">
      <label for="file">File:</label>
      <input type="file" name="file" id="file"><br>
    </div>
    <button type="submit">Upload File</button>
  </form>

  <script>
    // Function to toggle visibility of file upload field based on product type selection
    function toggleFileUpload() {
      var typeSelect = document.getElementById("type");
      var fileField = document.getElementById("fileField");

      if (typeSelect.value === "2") {
        document.getElementById("file").setAttribute("required", "required");
      } else {
        document.getElementById("file").removeAttribute("required");
      }
    }

    // Call toggleFileUpload function when the product type select changes
    document.getElementById("type").addEventListener("change", toggleFileUpload);

    // Initial call to set file upload visibility on page load
    toggleFileUpload();
  </script>
</body>

</html>
