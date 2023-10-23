<?php
/**
 * User Registration Script
 *
 * This script handles the user registration process. It validates user input, inserts user data
 * into the database, and sets up a session for the new user upon successful registration.
 *
 * PHP version 7
 *
 * @category User_Registration
 * @package  User_Interface
 */

// Start a PHP session for user authentication.
session_start();

// Include necessary configuration and database files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

/**
 * Check if a user is already authenticated or if there is no POST data. Redirect to the login page.
 */
if (isset($_SESSION['user_id']) || empty($_POST)) {
    header("location: ../login.php");
}

// Get user input from the POST data and sanitize it.
$username = mysqli_real_escape_string($conn, $_POST['username']);
$email = mysqli_real_escape_string($conn, $_POST['email']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Hash the user's password for secure storage in the database.
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Prepare an SQL query to insert user data into the database.
$sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $username, $email, $hashed_password);

// Execute the query to insert user data.
if ($stmt->execute()) {
    $_SESSION['user_id'] = $conn->insert_id;
    $_SESSION['username'] = $username;
    header("Location: ../../public/index.php");
    exit;
}

// Close the database connection.
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registration</title>
  <link rel="stylesheet" href="../../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
  <div class="container">
    <div class="invoice">
      <h2>Oops! An error occurred.</h2>
      <br>
      <nav>
        <div>
          <ul>
            <li><a href="../register.php">Try Again</a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>
</body>
</html>
