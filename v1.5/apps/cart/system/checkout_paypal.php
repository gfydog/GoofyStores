<?php

/**
 * PayPal Payment Processing Script
 *
 * This script handles the processing of PayPal payments for items in the user's shopping cart. It retrieves the user's
 * cart items and calculates the total amount. It creates a payment request to PayPal and redirects the user to the PayPal
 * approval page. Any errors during this process are displayed to the user.
 *
 * PHP version 7
 *
 * @category Payment_Processing
 * @package  User_Interface
 */

// Start a PHP session.
session_start();

// Require the PayPal SDK.
require_once '../../../vendor/autoload.php';

// Include necessary configuration files.
require "../../../config/common.php";
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Include the PayPal configuration file.
require "../../common/paypal.php";

// Retrieve the user's ID from the session or set it to null if the user is not logged in.
$user_id = getUserID();

// Initialize variables for user shipping information.
$email = $shipping_name = $shipping_address = $shipping_city = $shipping_zip = $shipping_country = '';
$error = '';

// Check if the HTTP request method is POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Check if there are physical products in the cart
    $sql = "SELECT COUNT(*) AS count FROM cart 
            INNER JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = ? AND products.type = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $physicalProductCount = $row['count'];

    // Process the shipping information form only if there are physical products in the cart
    if ($physicalProductCount > 0) {
        $email = $_POST['email'];
        $shipping_name = $_POST['shipping_name'];
        $shipping_address = $_POST['shipping_address'];
        $shipping_city = $_POST['shipping_city'];
        $shipping_zip = $_POST['shipping_zip'];
        $shipping_country = $_POST['shipping_country'];

        // Validate the shipping information (you can add more validation if needed).
        // For simplicity, let's assume the fields are required.

        if (empty($email) || empty($shipping_name) || empty($shipping_address) || empty($shipping_city) || empty($shipping_zip) || empty($shipping_country)) {
            $error = "All shipping information fields are required.";
        }
    }

    // If there are no validation errors or if there are no physical products in the cart, proceed with creating the PayPal payment.
    if (empty($error) || $physicalProductCount == 0) {
        // Store shipping information in the session for later retrieval in paypal_success.php
        $_SESSION['email'] = $email;
        $_SESSION['shipping_name'] = $shipping_name;
        $_SESSION['shipping_address'] = $shipping_address;
        $_SESSION['shipping_city'] = $shipping_city;
        $_SESSION['shipping_zip'] = $shipping_zip;
        $_SESSION['shipping_country'] = $shipping_country;

        // Query the database to retrieve the user's cart items and calculate the total amount.
        $sql = "SELECT products.id, products.type, products.price, products.stock_quantity, cart.quantity
            FROM cart
            INNER JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cart = [];

        // Fetch cart items and store them in an array.
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $cart[] = [
                    'product_id' => $row['id'],
                    'price' => $row['price'],
                    'stock_quantity' => $row['stock_quantity'],
                    'quantity' => $row['quantity'],
                    'type' => $row['type']
                ];
            }
        }

        // Calculate the total amount to be paid.
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        // Check if the quantity requested exceeds the available stock for physical products.
        foreach ($cart as $item) {
            if ($item['stock_quantity'] < $item['quantity'] && $item['type'] == 1) {
                $error = "One or more items in your cart are out of stock.";
                break;
            }
        }

        // If there are no errors, proceed with creating the PayPal payment.
        if (empty($error)) {
            // Create a PayPal payment request.
            $apiContext = getPayPalAPIContext();

            $payer = new \PayPal\Api\Payer();
            $payer->setPaymentMethod('paypal'); // Cambiado de 'credit_card' a 'paypal'

            $amount = new \PayPal\Api\Amount();
            $amount->setTotal($total);
            $amount->setCurrency('USD');

            $transaction = new \PayPal\Api\Transaction();
            $transaction->setAmount($amount);
            $transaction->setDescription('Purchase in the digital content store');

            $redirectUrls = new \PayPal\Api\RedirectUrls();
            $redirectUrls->setReturnUrl(ROOT . 'apps/cart/paypal_success.php')
                ->setCancelUrl(ROOT . 'apps/cart/paypal_cancel.php');

            $payment = new \PayPal\Api\Payment();
            $payment->setIntent('sale')
                ->setPayer($payer)
                ->setTransactions([$transaction])
                ->setRedirectUrls($redirectUrls);

            try {
                // Create the payment with PayPal.
                $payment->create($apiContext);

                // Redirect the user to the PayPal approval page.
                header('Location: ' . $payment->getApprovalLink());
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="../../../assets/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../../manifest.php">
</head>

<body>

    <?php require_once '../../common/header.php'; ?>

    <div class="container">
        <div class="invoice">
            <?php if (!empty($error)) : ?>
                <p>Error: <?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
