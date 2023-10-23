<?php
$sql = "SELECT * FROM configurations LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    
    $storeData = $result->fetch_assoc();

    // También puedes definir constantes si es necesario
    define('PAYPAL_SANDBOX', $storeData['PAYPAL_SANDBOX']);
    define('PAYPAL_CLIENT_ID', $storeData['PAYPAL_CLIENT_ID']);
    define('PAYPAL_SECRET', $storeData['PAYPAL_SECRET']);
    define('TITLE', $storeData['TITLE']);
    define('STYLE', $storeData['STYLE']);

    
} else {
    echo "No se encontraron filas en la tabla configurations.";
    exit;
}
