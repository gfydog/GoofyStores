<?php
/**
 * Admin Login Script
 *
 * This script handles the login process for administrators. It checks the provided username
 * or email and password against the database, and if the credentials are valid, it sets a
 * session variable to grant access to the admin panel.
 *
 * PHP version 7
 *
 * @category Admin_Login
 * @package  Admin_Interface
 */

// Start a PHP session to handle user authentication.
session_start();

// Include necessary configuration and database files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Retrieve username and password from the POST request.
$username = $_POST['username'];
$password = $_POST['password'];

// Verify administrator credentials in the database.
$sql = "SELECT * FROM admin WHERE username = ? or email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$result = $stmt->get_result();

// Check if an administrator with the provided username or email exists.
if ($result->num_rows > 0) {
    $admin = $result->fetch_assoc();
    // Verify the provided password against the hashed password in the database.
    if (password_verify($password, $admin['password'])) {
        // Set a session variable for the administrator.
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
    <title>Inicio de sessión</title>
    <link rel="stylesheet" href="../../../assets/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="invoice">
            <h2>Ups! Tuvimos un error.</h2>
            <p><?= $message ?></p>
            <br>
            <nav>
                <div>
                    <ul>
                        <li><a href="../admin_login.php">Volver a intentarlo</a></li>
                    </ul>
                </div>
            </nav>
        </div>
    </div>
</body>
</html>
