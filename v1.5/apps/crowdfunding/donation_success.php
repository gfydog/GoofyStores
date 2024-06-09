<?php
session_start();

require_once '../../vendor/autoload.php';
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";
require_once "../common/paypal.php";

$apiContext = getPayPalAPIContext();

if (!isset($_SESSION['paymentId']) || !isset($_GET['PayerID'])) {
    // Redirigir al usuario a la página de cancelación de donación si falta información necesaria
    header("Location: donation_cancel.php");
    exit;
}

$paymentId = $_SESSION['paymentId'];
$project_id = $_SESSION['project_id'];
$amount = $_SESSION['amount'];

try {
    $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);

    $execution = new \PayPal\Api\PaymentExecution();
    $execution->setPayerId($_GET['PayerID']);

    $result = $payment->execute($execution, $apiContext);

    if ($result->getState() === 'approved') {
        // Verificar si el usuario desea donar de forma anónima
        $anonymous = isset($_POST['anonymous']) && $_POST['anonymous'] == 1 ? 1 : 0;

        if ($anonymous || !isset($_SESSION['user_id'])) {
            // Insertar detalles de donación anónima en la base de datos
            $stmt = $conn->prepare("INSERT INTO donations (project_id, user_id, amount, donation_date, payment_id) VALUES (?, NULL, ?, NOW(), ?)");
            $stmt->bind_param("ids", $project_id, $amount, $paymentId);
            $stmt->execute();
        } else {
            // Insertar detalles de donación en la base de datos con el usuario logueado
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("INSERT INTO donations (project_id, user_id, amount, donation_date, payment_id) VALUES (?, ?, ?, NOW(), ?)");
            $stmt->bind_param("iids", $project_id, $user_id, $amount, $paymentId);
            $stmt->execute();
        }

        // Redirigir al usuario a la página de agradecimiento después de una donación exitosa
        header("Location: thank_you.php");
        exit;
    } else {
        // Manejar el fallo del pago
        header("Location: donation_cancel.php");
        exit;
    }
} catch (Exception $e) {
    $error = $e->getMessage();
    // Manejar el error de pago adecuadamente, como redirigir a una página de error.
    echo "Error procesando la donación: " . $error;
    exit;
}
?>
