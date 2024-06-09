<?php
/**
 * This PHP script is part of a web application installation process.
 * It handles the configuration of the administrator's default account and finalizes the installation.
 */

// Establish a connection to the database (customize it according to your database management system, e.g., MySQLi or PDO).
require_once "../config/configFinal.php";
require_once "../config/database.php";


// Check the database connection.
if ($conn->connect_error) {
    die('Database connection error: ' . $conn->connect_error);
}

$checkStmt = $conn->prepare("SELECT * FROM `admin`");
$checkStmt->execute();
$checkStmt->store_result();
$rowCount = $checkStmt->num_rows;

// Process the third-page form if not continuing directly to the next step.
if (!isset($_GET['next'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $adminUsername = $_POST['adminUsername']; // Default administrator username.
        $adminPassword = $_POST['adminPassword']; // Default administrator password.
        $adminEmail = $_POST['adminEmail']; // Default administrator email.

        // Hash the default administrator password (use more secure hash methods).
        $hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

        // Insert the default administrator data into the `admin` table.
        $insertAdminSql = "INSERT INTO `admin` (`username`, `password`, `email`) VALUES ('$adminUsername', '$hashedPassword', '$adminEmail')";
        if ($conn->query($insertAdminSql) === TRUE) {
            deleteInstallFolder();
            header('Location: ../welcome.php');
            exit;
        } else {
            echo 'Error registering the administrator: ' . $conn->error;
        }

        // Close the database connection.
        $conn->close();
    }
} else {
    if ($rowCount > 0) {
        deleteInstallFolder();
        header('Location: ../welcome.php');
        exit;
    }
}

// Function to delete the 'install' folder and its files.
function deleteInstallFolder()
{
    $dirPath = __DIR__ . '/../install';

    if (is_dir($dirPath)) {
        $files = glob($dirPath . '/*');

        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        rmdir($dirPath);
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 3 of 3</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <h1>Installation (Step 3 of 3)</h1>
    <h4>Administrator Account</h4>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="adminUsername">Username:</label>
        <input type="text" name="adminUsername" required><br>

        <label for="adminPassword">Password:</label>
        <input type="password" name="adminPassword" required><br>

        <label for="adminEmail">Email:</label>
        <input type="email" name="adminEmail" required><br>

        <input type="submit" value="Complete Installation"><br><br>
        <?php if ($rowCount > 0) { ?>
            <a href="page3.php?next">Skip this step</a>
        <?php } ?>
    </form>

</body>

</html>
