<?php
/**
 * This PHP script handles the submission of product reviews.
 */

session_start();
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Check if the user is authenticated; otherwise, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

// If the request method is POST, process the review submission.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $rating = intval($_POST['rating']);
    $review = $_POST['review'];

    // Insert the review into the database.
    $sql = "INSERT INTO reviews (user_id, product_id, rating, review) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $user_id, $product_id, $rating, $review);

    // If the review is successfully inserted, redirect back to the product page with the anchor to the review section.
    if ($stmt->execute()) {
        header("location: ../product.php?id=" . $product_id . "#review");
    }
} else {
    $product_id = 0;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Error</title>
  <link rel="stylesheet" href="../../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <link rel="manifest" href="../../../manifest.php">
</head>

<body>

  <div class="container">
    <div class="invoice">
      <h2>Oops! We encountered an error.</h2>
      <br>
      <nav>
        <div>
          <ul>
            <li><a href="<?= "../product.php?id=" . $product_id . "#review" ?>">Retry</a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>

</body>
</html>
