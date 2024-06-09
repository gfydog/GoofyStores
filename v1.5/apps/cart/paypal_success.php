<?php
session_start();

require_once '../../vendor/autoload.php';
require "../../config/common.php";
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
require "../common/paypal.php";
require "../common/utils.php";

// Verificar si se han pasado los parámetros de pago
if (!isset($_GET['paymentId']) || !isset($_GET['PayerID'])) {
    header("Location: ../authentication/logout.php");
    exit;
}

$cart_ss = [];
$download_html = "";

$apiContext = getPayPalAPIContext();

$paymentId = $_GET['paymentId'];

$payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

$execution = new \PayPal\Api\PaymentExecution();
$execution->setPayerId($_GET['PayerID']);

try {
    $payment->execute($execution, $apiContext);

    // Obtener detalles del pago
    $transactions = $payment->getTransactions();
    $transaction = $transactions[0];
    $amount = $transaction->getAmount();
    $total = $amount->getTotal();

    $userId = getUserID();

    $purchaseDate = date("Y-m-d H:i:s");

    // Verificar si ya se ha realizado un pago con el mismo ID de pago
    $sql = "SELECT COUNT(*) AS count FROM purchases WHERE payment_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $paymentId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $purchaseCount = $row['count'];

    if ($purchaseCount > 0) {
        // Redireccionar a una página que indique que la compra ya ha sido procesada
        header("Location: already_purchased.php");
        exit;
    }

    // Procesar la compra si el pago es exitoso y no está duplicado
    // Esto incluye insertar detalles de compra en la base de datos y limpiar el carrito

    // Verificar si hay productos físicos en el carrito
    $sql = "SELECT COUNT(*) AS count FROM cart 
            INNER JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = ? AND products.type = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $physicalProductCount = $row['count'];

    $product_type = "digital";

    // Procesar la compra solo si hay productos físicos en el carrito
    // Process the purchase only if there are physical products in the cart
    if ($physicalProductCount > 0) {
        $product_type = "physical";
    }
    // Fetch shipping information only if there are physical products
    $email = $_SESSION['email'] ?? '';
    $shippingName = $_SESSION['shipping_name'] ?? '';
    $shippingAddress = $_SESSION['shipping_address'] ?? '';
    $shippingCity = $_SESSION['shipping_city'] ?? '';
    $shippingZip = $_SESSION['shipping_zip'] ?? '';
    $shippingCountry = $_SESSION['shipping_country'] ?? '';

    // Check if the quantity requested exceeds the available stock for physical products
    $sql = "SELECT cart.product_id, cart.quantity, products.name, products.file_url,  products.stock_quantity, products.type FROM cart
            INNER JOIN products ON cart.product_id = products.id
            WHERE cart.user_id = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        if ($row['quantity'] > $row['stock_quantity'] && $row['type'] == 1) {
            // Redirect to a page indicating that the purchase cannot be completed due to insufficient stock
            header("Location: insufficient_stock.php");
            exit;
        }

        array_push($cart_ss, $row);
    }

    // Insert purchase details into the database and clear the cart
    $stmt = $conn->prepare("INSERT INTO purchases (user_id, product_type, total, purchase_date, email, shipping_name, shipping_address, shipping_city, shipping_zip, shipping_country, payment_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isdssssssss", $userId, $product_type, $total, $purchaseDate, $email, $shippingName, $shippingAddress, $shippingCity, $shippingZip, $shippingCountry, $paymentId);
    $stmt->execute();

    $purchaseId = $stmt->insert_id;

    $sql = "SELECT product_id, quantity FROM cart WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();


    while ($row = $result->fetch_assoc()) {
        // Update stock quantity
        $update_sql = "UPDATE products SET stock_quantity = stock_quantity - ? WHERE id = ? and type = 1";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $row['quantity'], $row['product_id']);
        $update_stmt->execute();

        // Insert into purchase_files
        $stmt = $conn->prepare("INSERT INTO purchase_files (purchase_id, file_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $purchaseId, $row['product_id'], $row['quantity']);
        $stmt->execute();
    }

    if (isset($_SESSION['user_id'])) {
        $update_sql = "UPDATE users SET shipping_name=?, shipping_address=?, shipping_city=?, shipping_zip=?, shipping_country=? WHERE id=?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssssi", $shippingName, $shippingAddress, $shippingCity, $shippingZip, $shippingCountry, $userId);
        $update_stmt->execute();
    }

    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Display links for downloading digital files.
    if (count($cart_ss) > 0) {
        $download_html .= "<h3>Download Links</h3>";
        $download_html .= "<ul>";
        // Loop through each cart item to display download links.

        foreach ($cart_ss as $ite) {
            if (!empty($ite['file_url'])) {
                $download_token = getDownloadToken($ite['product_id']); // Ajusta esto según tu lógica de generación de tokens
                // Generar el enlace de descarga con el token
                $download_link =  "../account/download_token.php?token=" . $download_token;

                $_SESSION['download_links'][] = ["url" => $download_link, "name" => $ite['name']];

                // Mostrar el enlace de descarga
                $download_html .= "<li><a href='" . $download_link . "'>" . $ite['name'] . "</a></li>";
            }
        }
        $download_html .= "</ul>";
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <link rel="manifest" href="../../manifest.php">
</head>

<body>
    <?php require_once '../common/header.php'; ?>
    <div class="container">
        <div class="invoice">
            <?php
            // Verificar si los parámetros de pago están presentes en la URL
            if (isset($_GET['paymentId']) && isset($_GET['PayerID'])) {
                // Payment was successful, display success message.
                $paymentId = $_GET['paymentId'];
                $payerId = $_GET['PayerID'];

                echo "<h2>Payment Successful</h2>";
                echo "<p>Thank you for your purchase.</p>";
                echo "<p>Payment ID: " . $paymentId . "</p>";
                echo "<p>Payer ID: " . $payerId . "</p>";

                // Display the receipt for the purchase.
                echo "<h3>Receipt</h3>";
                echo "<p>Products Purchased:</p>";
                echo "<ul>";
                // Retrieve cart items from the session.
                foreach ($cart_ss as $item) {
                    echo "<li>" . $item['quantity'] . " x " . $item['name'] . "</li>";
                }
                echo "</ul>";

                if ($product_type == "physical") {
                    echo "<h3>Shipping Information</h3>";
                    echo "<p>Email: " . $email . "</p>";
                    echo "<p>Name: " . $shippingName . "</p>";
                    echo "<p>Address: " . $shippingAddress . "</p>";
                    echo "<p>City: " . $shippingCity . "</p>";
                    echo "<p>ZIP/Postal Code: " . $shippingZip . "</p>";
                    echo "<p>Country: " . $shippingCountry . "</p>";
                }

                echo $download_html;
            } else {
                // Redireccionar a la página de inicio si la solicitud es inválida
                header("Location: ../../../index.php");
                exit;
            }
            ?>
        </div>
    </div>
</body>

</html>