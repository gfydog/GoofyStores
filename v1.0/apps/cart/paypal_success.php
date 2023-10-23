<?php
/**
 * Start a new or resume the current session.
 */
session_start();

// Require the necessary configuration and database files.
require_once '../../vendor/autoload.php';
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Require the PayPal-related functions.
require_once "./system/paypal.php";

/**
 * Redirect the user to the login page if they are not authenticated.
 */
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

/**
 * Redirect the user to the logout page if the required PayPal parameters are not present.
 */
if (!isset($_GET['paymentId']) || !$_GET['PayerID']) {
    header("Location: ../authentication/logout.php");
    exit;
}

// Initialize the PayPal API context to handle payment processing.
$apiContext = getPayPalAPIContext();

// Get the payment ID from the URL.
$paymentId = $_GET['paymentId'];

// Fetch the payment details from PayPal.
$payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

// Create a new PaymentExecution instance and set the payer ID from the URL.
$execution = new \PayPal\Api\PaymentExecution();
$execution->setPayerId($_GET['PayerID']);

try {
    // Execute the payment with PayPal.
    $payment->execute($execution, $apiContext);

    // Get the total amount paid.
    $transactions = $payment->getTransactions();
    $transaction = $transactions[0];
    $amount = $transaction->getAmount();
    $total = $amount->getTotal();

    // Register the purchase in the database.
    $userId = $_SESSION['user_id'];
    $purchaseDate = date("Y-m-d H:i:s");

    // Prepare and execute the SQL statement to insert the purchase record.
    $stmt = $conn->prepare("INSERT INTO purchases (user_id, total, purchase_date) VALUES (?, ?, ?)");
    $stmt->bind_param("ids", $userId, $total, $purchaseDate);
    $stmt->execute();

    // Get the purchase ID for later use.
    $purchaseId = $stmt->insert_id;

    // Fetch product IDs and quantities from the user's cart.
    $sql = "SELECT product_id, quantity FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    // Insert purchase file records in the database.
    while ($row = $result->fetch_assoc()) {
        $stmt = $conn->prepare("INSERT INTO purchase_files (purchase_id, file_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $purchaseId, $row['product_id'], $row['quantity']);
        $stmt->execute();
    }

    // Clear the user's shopping cart in the database.
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Redirect the user to a thank-you page.
    header("Location: thank_you.php");
} catch (Exception $e) {
    // Handle and store any exceptions that occurred during payment processing.
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>PayPal Success</title>
</head>
<body>
    <?php if (isset($error)): ?>
        <!-- Display an error message if any errors occurred. -->
        <p>Error: <?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
</body>
</html>
