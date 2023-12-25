<?php
/**
 * This PHP script handles the submission of comments.
 */

session_start();
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

// Check if the user is authenticated; otherwise, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

// If the request method is POST, process the comment submission.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $news_id = intval($_POST['news_id']);
    $content = $_POST['comment'];

    // Insert the comment into the database.
    $sql = "INSERT INTO comments (user_id, post_id, content) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iis", $user_id, $news_id, $content);

    // If the comment is successfully inserted, redirect back to the news article page.
    if ($stmt->execute()) {
        header("location: ../../public/news_article.php?id=" . $news_id);
        exit;
    }
} else {
    $news_id = 0;
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
            <li><a href="<?= "../../public/news_article.php?id=" . $news_id ?>">Retry</a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>

</body>
</html>
