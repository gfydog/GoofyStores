<?php
/**
 * Description: This file handles the download of files associated with tokens or purchased files.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
$admin = false;
if (isset($_SESSION['admin_id'])) {
  $admin = true;
}

// Require configuration files.
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Check if the user is logged in.
$user_id = "-1";
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

// Check if free parameter is set and the price is 0 or 0.00
if (isset($_GET["free"])) {
    // Retrieve the product price from the database.
    $sql_price = "SELECT price FROM products WHERE id = ?";
    $stmt_price = $conn->prepare($sql_price);
    $stmt_price->bind_param("i", $file_id);
    $stmt_price->execute();
    $result_price = $stmt_price->get_result();

    if ($result_price->num_rows > 0) {
        $product_price = $result_price->fetch_assoc()['price'];

        // Check if the price is 0 or 0.00
        if ($product_price == 0 || $product_price == 0.00) {
            // Proceed to download the file.
            downloadFile($file_id, $conn);
        } else {
            $message = "You do not have access to this file. Product not free.";
        }
    } else {
        $message = "You do not have access to this file. Product not found.";
    }
} else if($user_id != "-1" || $admin){
    // Proceed with the existing logic for purchased files.
    // SQL query to retrieve purchase and file information.
    $sql_purchase = "SELECT p.*, pf.* FROM purchase_files pf
            JOIN purchases p ON pf.purchase_id = p.id
            WHERE p.user_id = ? AND pf.file_id = ?";

    // Prepare the SQL statement.
    $stmt_purchase = $conn->prepare($sql_purchase);
    $stmt_purchase->bind_param("ii", $user_id, $file_id);
    $stmt_purchase->execute();

    // Get the result set.
    $result_purchase = $stmt_purchase->get_result();

    if ($result_purchase->num_rows > 0  || $admin) {
        // File has been purchased.
        downloadFile($file_id, $conn);
    } else {
        $message = "You do not have access to this file.";
    }
}else {
  $message = "Oops! You do not have access to this file.";
}

// Close the database connection.
$conn->close();

// Function to download the file.
function downloadFile($file_id, $conn)
{
    // SQL query to retrieve the file URL.
    $sql_file = "SELECT file_url FROM products WHERE id = ?";

    // Prepare the SQL statement.
    $stmt_file = $conn->prepare($sql_file);
    $stmt_file->bind_param("i", $file_id);
    $stmt_file->execute();

    // Get the result set.
    $result_file = $stmt_file->get_result();

    // Check if the file exists or an admin is logged in.
    if ($result_file->num_rows > 0) {

        // Fetch the product information.
        $product = $result_file->fetch_assoc();
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
}
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
