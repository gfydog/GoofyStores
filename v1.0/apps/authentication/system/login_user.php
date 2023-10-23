<?php
/**
 * User Login Script
 *
 * This script handles user authentication by checking the provided username and password
 * against the database records. If the provided credentials are valid, the user is redirected
 * to the main application page.
 *
 * PHP version 7
 *
 * @category User_Login
 * @package  User_Interface
 */

// Start a PHP session for handling user authentication.
session_start();

// Include necessary configuration and database files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Get the username and password from the POST data and sanitize them.
$username = mysqli_real_escape_string($conn, $_POST['username']);
$password = mysqli_real_escape_string($conn, $_POST['password']);

// Prepare a database query to retrieve the user by their username.
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = $conn->query($sql);

// Check if a user was found.
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify the provided password against the hashed password in the database.
    if (password_verify($password, $user['password'])) {
        // Set session variables for the authenticated user.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Redirect the user to the main application page.
        header("Location: ../../public/index.php");
    } else {
        // Set an error message for incorrect password.
        $message = "Incorrect password";
    }
} else {
    // Set an error message for user not found.
    $message = "User not found";
}

// Close the database connection.
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="../../../assets/css/all.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>

  <div class="container">
    <div class="invoice">
      <h2>Oops! An error occurred.</h2>
      <p><?= $message ?></p>
      <br>
      <nav>
        <div>
          <ul>
            <li><a href="../login.php">Try again</a></li>
          </ul>
        </div>
      </nav>
    </div>
  </div>

</body>

</html>
