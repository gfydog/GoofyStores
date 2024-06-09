<?php
/**
 * Description: This file displays download links if available in the session or redirects to the home page if not.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a session to manage user data across requests.
session_start();

// Include configuration and database files.
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Check if download links are available in the session.
if (isset($_SESSION['download_links'])) {
    $download_html = "";

    // Construct HTML for displaying download links.
    $download_html .= "<h3>Download Links</h3>";
    $download_html .= "<ul>";

    // Loop through each item in the cart to display download links.
    foreach ($_SESSION['download_links'] as $item) {

        // Display the download link.
        $download_html .= "<li><a href='" . $item['url'] . "'>" . $item['name'] . "</a></li>";
    }
    $download_html .= "</ul>";
} else {
    // Redirect to the home page if download links are not available.
    header("location: ../../");
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
            // Display the generated download HTML.
            echo $download_html;
            ?>
        </div>
    </div>
</body>

</html>
