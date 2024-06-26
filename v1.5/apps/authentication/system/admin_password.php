<?php
/**
 * Description: Script for resetting administrator password
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session for handling user authentication.
session_start();

// Include necessary configuration, database, and mailer files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";
require '../../common/Mailer.php';

/**
 * Generate a random string of the specified length.
 *
 * @param int $length The length of the random string to generate.
 *
 * @return string The generated random string.
 */
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Check if the script received a POST request.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get the username or email from the POST data and sanitize it.
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));

    // Prepare a database query to find the user by email or username.
    $stmt = $conn->prepare("SELECT * FROM admin WHERE email = ? or username = ?");
    $stmt->bind_param("ss", $username, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if a user was found.
    if ($user = $result->fetch_assoc()) {

        // Generate a new random password.
        $new_password = generateRandomString(8);

        // Hash the new password.
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // Get the user's email.
        $email = $user['email'];

        // Update the user's password in the database.
        $stmt = $conn->prepare("UPDATE admin SET password = ? WHERE email = ?");
        $stmt->bind_param("ss", $hashed_password, $email);
        $stmt->execute();

        // Create an instance of the Mailer class for sending an email.
        $mailer = new Mailer("no-reply@goofy.dog");
        $mailer->setRecipient($email);
        $mailer->setSubject("Password Recovery");
        $mailer->setMessage("Your new administrator password is: " . $new_password);

        // Send the email with the new password.
        if ($mailer->sendEmail()) {
            echo "An email with your new password has been sent.";
        } else {
            echo "Error sending the email.";
        }

    } else {
        echo "User or email not registered";
    }

    // Close the prepared statement and the database connection.
    $stmt->close();
    $conn->close();
}
?>