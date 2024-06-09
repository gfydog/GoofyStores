<?php
/**
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
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
$sql = "SELECT * FROM users WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if a user was found.
if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    
    // Verify the provided password against the hashed password in the database.
    if (password_verify($password, $user['password'])) {
      
        if (isset($_SESSION['temp_user_id'])) {
            // Query to sum the quantities of the temporary cart to the current user's cart
            $sql_update_cart = "UPDATE cart AS c1
                                INNER JOIN cart AS c2 ON c1.product_id = c2.product_id AND c1.user_id = ?
                                SET c1.quantity = c1.quantity + c2.quantity
                                WHERE c2.user_id = ?";
        
            $stmt_update_cart = $conn->prepare($sql_update_cart);
            $stmt_update_cart->bind_param("ii", $user['id'], $_SESSION['temp_user_id']);
            $stmt_update_cart->execute();
        }
        
        // Set session variables for the authenticated user.
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        // Redirect the user to the main application page.
        header("Location: ../../public/index.php");
        exit;
    } else {
        // Set an error message for incorrect password.
        $message = "Incorrect password";
    }
} else {
    // Set an error message for user not found.
    $message = "User not found";
}

// Close the prepared statement and the database connection.
$stmt->close();
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
