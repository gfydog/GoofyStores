<?php
/**
 * Description: This file retrieves orders from the database with pagination and provides shipping details and purchased files.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Verify if the user is authenticated as an administrator.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Include configuration files.
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

// Define variables for pagination.
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// SQL query to retrieve orders with a limit and ordered by purchase date in descending order.
$sql = "SELECT p.id as purchase_id, p.purchase_date, IFNULL(u.username, 'Guest') as username, p.product_type as product_type
        FROM purchases p 
        LEFT JOIN users u ON p.user_id = u.id 
        ORDER BY p.purchase_date DESC LIMIT ?, ?";

// Prepare the query.
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];

if ($result) { 
    while ($row = $result->fetch_assoc()) {
        $purchase_id = $row['purchase_id'];

        // SQL query to retrieve files related to the purchase.
        $files_sql = "SELECT f.name, pf.quantity, f.type as product_type
                      FROM purchase_files pf 
                      INNER JOIN products f ON pf.file_id = f.id 
                      WHERE pf.purchase_id = $purchase_id";

        // Execute the SQL query to retrieve files.
        $files_result = $conn->query($files_sql);

        $files = [];

        if ($files_result->num_rows > 0) {
            while ($file_row = $files_result->fetch_assoc()) {
                $files[] = [
                    'name' => htmlspecialchars($file_row['name'], ENT_QUOTES, 'UTF-8'),
                    'quantity' => $file_row['quantity'],
                    'type' => $file_row['product_type'] // Assuming 'type' indicates the product type
                ];
            }
        }

        // Check if the order contains physical products.
        $physicalProductCount = count(array_filter($files, function ($file) {
            return $file['type'] == 1; // Assuming 'type' indicates the product type
        }));

        // Prepare shipping details.
        $shippingDetails = [];
        if ($physicalProductCount > 0) {
            // Query shipping details from the database.
            $shipping_sql = "SELECT shipping_name, shipping_address, shipping_city, shipping_zip, shipping_country 
                         FROM purchases 
                         WHERE id = $purchase_id";
            $shipping_result = $conn->query($shipping_sql);

            if ($shipping_result->num_rows > 0) {
                $shipping_row = $shipping_result->fetch_assoc();
                $shippingDetails = [
                    'name' => htmlspecialchars($shipping_row['shipping_name'], ENT_QUOTES, 'UTF-8'),
                    'address' => htmlspecialchars($shipping_row['shipping_address'], ENT_QUOTES, 'UTF-8'),
                    'city' => htmlspecialchars($shipping_row['shipping_city'], ENT_QUOTES, 'UTF-8'),
                    'zip' => htmlspecialchars($shipping_row['shipping_zip'], ENT_QUOTES, 'UTF-8'),
                    'country' => htmlspecialchars($shipping_row['shipping_country'], ENT_QUOTES, 'UTF-8')
                ];
            }
        }

        // Add the order to the list.
        $orders[] = [
            'purchase_id' => $purchase_id,
            'purchase_date' => $row['purchase_date'],
            'username' => htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'),
            'shipping' => $shippingDetails,
            'files' => $files
        ];
    }
} else {
    // Handle the case where the query failed.
    echo "Error: " . $conn->error; // Error message for debugging
}

// Respond with a JSON result containing the orders.
echo json_encode($orders);

// Close the database connection.
$stmt->close();
$conn->close();
?>
