<?php
/**
 * Cart Retrieval Script (JSON Response)
 *
 * This script retrieves the items in the user's shopping cart and returns them as a JSON response. It ensures that
 * the user is logged in before fetching the cart items from the database. The retrieved cart items include product
 * details such as ID, name, description, price, and quantity.
 *
 * PHP version 7
 *
 * @category Cart_Management
 * @package  User_Interface
 */

// Start a PHP session.
session_start();

// Include necessary configuration files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Set the content type to JSON for the response.
header('Content-Type: application/json');

// Check if the user is not logged in. If not logged in, return an error response in JSON format and exit.
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'You must be logged in to view the cart']);
    exit;
}

try {
    // Initialize an array to store the cart items.
    $cartItems = [];

    // Retrieve cart items for the logged-in user from the database.
    $sql = "SELECT * FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Loop through the cart items and fetch product details for each item.
    while ($row = $result->fetch_assoc()) {
        // Retrieve product details for the current cart item.
        $stmtProduct = $conn->prepare("SELECT * FROM products WHERE id = ?");
        $stmtProduct->bind_param("i", $row['product_id']);
        $stmtProduct->execute();
        $productResult = $stmtProduct->get_result();
        $product = $productResult->fetch_assoc();

        // If the product details are available, add the item to the cartItems array.
        if ($product) {
            $cartItems[] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'description' => $product['description'],
                'price' => $product['price'],
                'quantity' => $row['quantity']
            ];
        }
    }

    // Return the cart items as a JSON response.
    echo json_encode($cartItems);

} catch (Exception $e) {
    // Handle any exceptions or errors and return an error response in JSON format.
    echo json_encode(['error' => 'An error occurred while retrieving cart items']);
}
?>
