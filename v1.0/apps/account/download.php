<?php
/**
 * This PHP script allows users to download a file if they have the necessary permissions.
 * It first checks if the user is logged in and if the 'id' parameter is set in the URL. Then,
 * it verifies the user's access rights and provides the file for download if applicable.
 *
 * PHP version 7
 *
 * @category File_Download
 * @package  Goofy
 * @author   Raúl Méndez Rodríguez
 */

// Start a PHP session.
session_start();

// Require configuration files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Check if the user is logged in.
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

// Check if the 'id' parameter is set in the URL; if not, redirect to the login page.
if (!isset($_GET['id'])) {
    header("Location: ../login.php");
    exit;
}

// Get the file ID from the URL and ensure it's an integer.
$file_id = intval($_GET['id']);

// SQL query to retrieve purchase and file information.
$sql = "SELECT p.*, pf.* FROM purchase_files pf
        JOIN purchases p ON pf.purchase_id = p.id
        WHERE p.user_id = ? AND pf.file_id = ?";

// Prepare the SQL statement.
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $file_id);
$stmt->execute();

// Get the result set.
$result = $stmt->get_result();

// Check if the user has access or if an admin is logged in.
if ($result->num_rows > 0 || isset($_SESSION['admin_id'])) {

    // SQL query to retrieve the file URL.
    $sql = "SELECT p.file_url FROM products p WHERE p.id = ?";

    // Prepare the SQL statement.
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $file_id);
    $stmt->execute();

    // Get the result set.
    $result = $stmt->get_result();

    // Check if the file exists or an admin is logged in.
    if ($result->num_rows > 0 || isset($_SESSION['admin_id'])) {

        // Fetch the product information.
        $product = $result->fetch_assoc();
        $file_url = "../../files/" . $product['file_url'];

        // Get the original file name.
        $original_filename = basename($file_url);

        // Generate a random filename to use for download.
        $random_filename = rand(1000, 9999);
        $file_extension = pathinfo($original_filename, PATHINFO_EXTENSION);
        $visible_filename = $random_filename . '.' . $file_extension;

        // Set headers for file download.
        header('Content-Type: application/octet-stream');
        header('Content-Transfer-Encoding: Binary');
        header('Content-Disposition: attachment; filename="' . $visible_filename . '"');
        header('Content-Length: ' . filesize($file_url));

        // Output the file for download.
        readfile($file_url);
    } else {
        $message =  "File not found.";
    }
} else {
    $message = "You do not have access to this file.";
}

// Close the database connection.
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Goofy</title>
  <link rel="stylesheet" href="../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="manifest" href="../../manifest.php">
</head>

<body>
  <?php require_once '../common/header.php'; ?>

  <div class="container">
    <div class="factura">
      <h2>Oops!</h2>
      <p><?= $message ?></p>
      <br>
      <nav>
        <div>
          <ul>
            <li><a href="../public/index.php">Return to the store</a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>
</body>
</html>
