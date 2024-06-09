<?php

use PayPal\Rest\ApiContext;
use PayPal\Auth\OAuthTokenCredential;

/**
 * Create and configure a PayPal API context.
 *
 * @return ApiContext The configured PayPal API context.
 */
function getPayPalAPIContext()
{
    // Verifica si las constantes PAYPAL_CLIENT_ID y PAYPAL_SECRET están definidas
    if (!defined('PAYPAL_CLIENT_ID') || !defined('PAYPAL_SECRET')) {
        throw new Exception('PayPal client ID or secret not defined.');
    }

    // Crea un nuevo contexto de API de PayPal con el ID de cliente y el secreto proporcionados.
    $apiContext = new ApiContext(
        new OAuthTokenCredential(
            PAYPAL_CLIENT_ID,
            PAYPAL_SECRET
        )
    );

    // Configura el modo del contexto de acuerdo con la configuración.
    $mode = PAYPAL_SANDBOX ? 'sandbox' : 'live';
    $apiContext->setConfig(['mode' => $mode]);

    // Devuelve el contexto de API configurado.
    return $apiContext;
}
