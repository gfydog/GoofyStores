<?php
/**
 * Cart Item Quantity Adjustment Script
 *
 * This script allows users to adjust the quantity of items in their shopping cart. Users can increment or decrement
 * the quantity of a specific item in the cart. If the quantity reaches zero, the item is removed from the cart.
 *
 * PHP version 7
 *
 * @category Cart_Management
 * @package  User_Interface
 */

// Start a PHP session.
session_start();

// Include necessary configuration files.
require "../../../config/common.php";
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

$user_id = getUserID();
    
// Get the product ID to adjust the quantity (if provided).
$product_id = (isset($_GET['id'])) ? intval($_GET['id']) : "x";

try {
    // Check if the product exists in the cart and its quantity is greater than one.
    $sql_check = "SELECT quantity FROM cart WHERE user_id = ? AND product_id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("ii", $user_id, $product_id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $stmt_check->bind_result($quantity);
        $stmt_check->fetch();

        if ($quantity > 1) {
            // Reduce the quantity by one for the specific cart item.
            $sql_update = "UPDATE cart SET quantity = quantity - 1 WHERE user_id = ? AND product_id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ii", $user_id, $product_id);

            if ($stmt_update->execute()) {
                header("location: ../cart.php");
            } else {
                echo 'An error occurred while reducing the quantity of the product in the cart';
            }
        } else {
            // If the quantity is one, remove the cart item from the cart.
            $sql_delete = "DELETE FROM cart WHERE user_id = ? AND product_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("ii", $user_id, $product_id);

            if ($stmt_delete->execute()) {
                header("location: ../cart.php");
            } else {
                echo 'An error occurred while removing the product from the cart';
            }
        }
    } else {
        echo 'The product does not exist in the cart or the quantity is already one';
    }
} catch (Exception $e) {
    echo 'An error occurred while retrieving cart items';
}
?>
