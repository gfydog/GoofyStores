<?php
/**
 * This PHP script retrieves a list of recent orders made by users and their associated files.
 * It checks if the user has logged in as an administrator.
 * If the conditions are met, it retrieves the orders and their corresponding files, and responds with a JSON result.
 *
 * PHP version 7
 *
 * @category Order_Retrieval
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Require configuration files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// SQL query to retrieve all purchases without a limit, sorted by purchase date in descending order.
$sql = "SELECT p.id as purchase_id, p.purchase_date, u.username 
        FROM purchases p 
        INNER JOIN users u ON p.user_id = u.id 
        ORDER BY p.purchase_date DESC LIMIT 12";

// Execute the SQL query.
$result = $conn->query($sql);

$orders = [];

while ($row = $result->fetch_assoc()) {
    $purchase_id = $row['purchase_id'];

    // SQL query to retrieve the files related to the purchase.
    $files_sql = "SELECT f.name, pf.quantity 
                      FROM purchase_files pf 
                      INNER JOIN products f ON pf.file_id = f.id 
                      WHERE pf.purchase_id = $purchase_id";

    // Execute the SQL query for files.
    $files_result = $conn->query($files_sql);

    $files = [];

    if ($files_result->num_rows > 0) {
        while ($file_row = $files_result->fetch_assoc()) {
            $files[] = [
                'name' => htmlspecialchars($file_row['name'], ENT_QUOTES, 'UTF-8'),
                'quantity' => $file_row['quantity']
            ];
        }
    }

    $orders[] = [
        'id' => $purchase_id,
        'username' => htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'),
        'files' => $files,
        'date' => $row['purchase_date']
    ];
}

// Respond with a JSON result containing the orders.
echo json_encode($orders);

// Close the database connection.
$conn->close();
