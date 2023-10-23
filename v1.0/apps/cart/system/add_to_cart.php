<?php
/**
 * Add Product to User's Shopping Cart
 *
 * This script allows users to add a product to their shopping cart. It checks whether the user is logged in,
 * processes the POST request to add the product to the cart, and updates the cart if the product is already present.
 *
 * PHP version 7
 *
 * @category Shopping_Cart
 * @package  User_Interface
 */

// Start a PHP session.
session_start();

/**
 * Check if the user is not logged in. If not logged in, exit the script.
 */
if (!isset($_SESSION['user_id'])) {
    exit;
}

// Include necessary configuration files.
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

/**
 * Check if the HTTP request method is POST.
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = 1;

    // Check if the product is already in the user's cart.
    $sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If the product is already in the cart, update the quantity.
        $row = $result->fetch_assoc();
        $new_quantity = $row['quantity'] + 1;
        $sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
        $stmtUpdate = $conn->prepare($sql);
        $stmtUpdate->bind_param("iii", $new_quantity, $user_id, $product_id);
        $stmtUpdate->execute();
    } else {
        // If the product is not in the cart, insert it.
        $sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($sql);
        $stmtInsert->bind_param("iii", $user_id, $product_id, $quantity);
        $stmtInsert->execute();
    }

    // Respond with a success message.
    echo "Success! Added to the cart";

} else {
    // If the HTTP request method is not POST, respond with an error message.
    echo "Method not allowed";
}

// Close the database connection.
$conn->close();
?>
