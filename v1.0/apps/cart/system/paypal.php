<?php
/**
 * Create and configure a PayPal API context.
 *
 * @return \PayPal\Rest\ApiContext The configured PayPal API context.
 */
function getPayPalAPIContext()
{
    // Create a new PayPal API context with the given client ID and secret.
    $apiContext = new \PayPal\Rest\ApiContext(
        new \PayPal\Auth\OAuthTokenCredential(
            PAYPAL_CLIENT_ID,
            PAYPAL_SECRET
        )
    );

    // Check if the application is running in the PayPal sandbox mode.
    if (PAYPAL_SANDBOX) {
        $apiContext->setConfig([
            'mode' => 'sandbox',
        ]);
    } else {
        // If not in the sandbox mode, use the live mode.
        $apiContext->setConfig([
            'mode' => 'live',
        ]);
    }

    // Return the configured PayPal API context.
    return $apiContext;
}
