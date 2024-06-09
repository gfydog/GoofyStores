<?php
/**
 * This PHP script is part of a web application installation process.
 * It processes the second-page form for configuring PayPal credentials and the general style of the application.
 * The script stores these configurations in the database and proceeds to the next installation step.
 */

// Process the form on the second page if it's submitted.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Establish a connection to the database (customize it according to your database management system, e.g., MySQLi or PDO).
    require_once "../config/configFinal.php";
    require_once "../config/database.php";

    // Check the database connection.
    if ($conn->connect_error) {
        die('Database connection error');
    }

    $checkStmt = $conn->prepare("SELECT * FROM configurations");
    $checkStmt->execute();
    $checkStmt->store_result();
    $rowCount = $checkStmt->num_rows;

    if ($rowCount > 0) {
        // If a row already exists, update the existing row.
        $stmt = $conn->prepare("
            UPDATE configurations SET 
                TITLE = ?,
                STYLE = ?,
                PAYPAL_SANDBOX = ?, 
                PAYPAL_CLIENT_ID = ?, 
                PAYPAL_SECRET = ?
            WHERE id = 1
        ");
    } else {
        // If no row exists, insert a new row.
        $stmt = $conn->prepare("
            INSERT INTO configurations (
                TITLE, STYLE, PAYPAL_SANDBOX, PAYPAL_CLIENT_ID, PAYPAL_SECRET
            ) VALUES (?, ?, ?, ?, ?)
        ");
    }

    // Bind parameters
    $TITLE = $_POST['title'];
    $STYLE = $_POST['style'];
    $PAYPAL_SANDBOX = isset($_POST['paypalSandbox']) ? 1 : 0;
    $PAYPAL_CLIENT_ID = $_POST['paypalClientId'];
    $PAYPAL_SECRET = $_POST['paypalSecret'];

    $stmt->bind_param("ssiss", $TITLE, $STYLE, $PAYPAL_SANDBOX, $PAYPAL_CLIENT_ID, $PAYPAL_SECRET);

    $stmt->execute();

    header('Location: page3.php');
    exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Step 2 of 3</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>

<body>
    <h1>Installation (Step 2 of 3)</h1>
    <h4>PayPal Credentials</h4>
    <form method="post" action="page2.php">
        <label for="paypalClientId">PayPal Client ID:</label>
        <input type="text" name="paypalClientId" required><br>

        <label for="paypalSecret">PayPal Secret:</label>
        <input type="text" name="paypalSecret" required><br>

        <label for="paypalSandbox"><input type="checkbox" name="paypalSandbox"> Use sandbox mode: </label>

        <br><br>
        <label for="style">General Style:</label>
        <select name="style" required>
            <option value="0">Basic</option>
            <option value="1">Modern</option>
        </select><br>
        <label for="title">Store Title</label>
        <input type="text" name="title" required><br>

        <input type="submit" value="Continue">
    </form>
</body>

</html>
