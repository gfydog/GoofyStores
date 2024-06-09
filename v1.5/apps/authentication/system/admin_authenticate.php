<?php
/**
 * Description: 
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Starting a PHP session to handle user authentication.
session_start();

// Including necessary configuration and database files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Retrieving username and password from the POST request.
$username = $_POST['username'];
$password = $_POST['password'];

// Verifying administrator credentials in the database.
$sql = "SELECT * FROM admin WHERE username = ? OR email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// Checking if an administrator with the provided username or email exists.
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    // Verifying the provided password against the hashed password in the database.
    if (password_verify($password, $admin['password'])) {
        // Setting a session variable for the administrator.
        $_SESSION['admin_id'] = $admin['id'];
        header("Location: ../../admin/admin_products.php");
        exit;
    } else {
        $message = "Incorrect password";
    }
} else {
    $message = "User does not exist";
}
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
            <h2>Oops! We encountered an error.</h2>
            <p><?= $message ?></p>
            <br>
            <nav>
                <div>
                    <ul>
                        <li><a href="../admin_login.php">Try Again</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</body>
</html>
