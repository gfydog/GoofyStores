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
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Include the PayPal configuration file.
require "./paypal.php";

// Check if the user is not logged in. If not logged in, redirect to the login page and exit.
if (!isset($_SESSION['user_id'])) {
  header("Location: ../../authentication/login.php");
  exit;
}

// Retrieve the user's ID from the session.
$user_id = $_SESSION['user_id'];

// Query the database to retrieve the user's cart items and calculate the total amount.
$sql = "SELECT products.id, products.price, cart.quantity
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
      'quantity' => $row['quantity']
    ];
  }
}

// Calculate the total amount to be paid.
$total = 0;
foreach ($cart as $item) {
  $total += $item['price'] * $item['quantity'];
}

// Check if the HTTP request method is POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Create a PayPal payment request.
  $apiContext = getPayPalAPIContext();

  $payer = new \PayPal\Api\Payer();
  $payer->setPaymentMethod('paypal');

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
      <h2>Make Payment with PayPal</h2>
      <?php if (isset($error)) : ?>
        <p>Error: <?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>
      <form action="checkout_paypal.php" method="post">
        <button id="pay" type="submit" class="pay-btn">Pay $<?php echo $total; ?> with PayPal</button>
      </form>
    </div>
  </div>
</body>

</html>