<?php
/**
 * This PHP script is part of a web application installation process.
 * It processes the database setup form and creates the necessary database tables.
 * After successful setup, it generates a configuration file and proceeds to the next installation step.
 */

// Process the installation form if it is submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $root = $_POST['root'];
    $dbHost = $_POST['dbHost']; // Customize with your database server.
    $dbUsername = $_POST['dbUsername']; // Customize with your database username.
    $dbPassword = $_POST['dbPassword']; // Customize with your database password.
    $dbName = $_POST['dbName']; // Customize with your database name.

    // Establish a database connection (customize according to your database management system, e.g., MySQLi or PDO).
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword);

    // Check the database connection.
    if ($conn->connect_error) {
        die('Database connection error: ' . $conn->connect_error);
    }

    // Create the database if it doesn't exist.
    $createDbSql = "CREATE DATABASE IF NOT EXISTS $dbName";
    if ($conn->query($createDbSql) === TRUE) {
        // Select the newly created database.
        $conn->select_db($dbName);

        // SQL queries to create tables and other objects.
        require_once 'db.php';

        if ($conn->multi_query($query)) {
            // Create a configuration file with the provided data (customize the name and content according to your needs).
            $configFileContent = <<<EOD
<?php
define("ROOT", '$root');
\$servername = '$dbHost';
\$username = '$dbUsername';
\$password = '$dbPassword';
\$dbname = '$dbName';
EOD;

            $directory = '../config/';
            if (!is_dir($directory)) {
                // If the directory doesn't exist, try to create it.
                if (!mkdir($directory, 0777, true)) {
                    // If the directory cannot be created, handle the error.
                    die('Failed to create the directory ' . $directory);
                }
            }

            // Now you can write to the file.
            file_put_contents($directory . 'configFinal.php', $configFileContent);

            header('Location: page2.php');
            exit;
        } else {
            echo 'Error creating tables: ' . $conn->error;
        }
    } else {
        echo 'Error creating the database: ' . $conn->error;
    }

    // Close the database connection.
    $conn->close();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 1 of 3</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <h1>Installation (Step 1 of 3)</h1>
    <h4>Database</h4>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">

        <label for="root">Final Folder URL</label>
        <input type="text" name="root" required placeholder="https://example.com/store/"><br>

        <label for="dbHost">Database Server:</label>
        <input type="text" name="dbHost" required placeholder="localhost"><br>

        <label for="dbUsername">Database User:</label>
        <input type="text" name="dbUsername" required placeholder="root"><br>

        <label for="dbPassword">Database Password:</label>
        <input type="password" name="dbPassword"><br>

        <label for="dbName">Database Name:</label>
        <input type="text" name="dbName" required><br>

        <input type="submit" value="Continue">
    </form>
</body>

</html>
