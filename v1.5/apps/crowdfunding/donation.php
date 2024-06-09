<?php 
session_start();

require_once '../../vendor/autoload.php';
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";
require_once "../common/paypal.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0.00;

    if ($project_id <= 0 || $amount <= 0) {
        die("Invalid project or amount.");
    }

    $apiContext = getPayPalAPIContext();

    $payer = new \PayPal\Api\Payer();
    $payer->setPaymentMethod('paypal');

    $amountObj = new \PayPal\Api\Amount();
    $amountObj->setTotal($amount);
    $amountObj->setCurrency('USD');

    $transaction = new \PayPal\Api\Transaction();
    $transaction->setAmount($amountObj);
    $transaction->setDescription('Donation for Project ' . $project_id);

    $redirectUrls = new \PayPal\Api\RedirectUrls();
    $redirectUrls->setReturnUrl(ROOT . 'apps/crowdfunding/donation_success.php')
                ->setCancelUrl(ROOT . 'apps/crowdfunding/index.php');

    $payment = new \PayPal\Api\Payment();
    $payment->setIntent('sale')
            ->setPayer($payer)
            ->setTransactions([$transaction])
            ->setRedirectUrls($redirectUrls);

    $payment->create($apiContext);

    foreach ($payment->getLinks() as $link) {
        if ($link->getRel() == 'approval_url') {
            $redirectUrl = $link->getHref();
            break;
        }
    }

    $_SESSION['paymentId'] = $payment->getId();
    $_SESSION['project_id'] = $project_id;
    $_SESSION['amount'] = $amount;

    header("Location: $redirectUrl");
    exit;
} else {
    header("Location: index.php");
    exit;
}
?>
