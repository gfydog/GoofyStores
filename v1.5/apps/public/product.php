<?php
// Start a session
session_start();

// Include necessary configuration and database files
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Get the user ID from the session, if available
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
}

if (isset($_GET['id'])) {
    $data_id = intval($_GET['id']);
    $sql = "SELECT p.*, pi.image FROM products AS p JOIN product_images AS pi ON p.id = pi.product_id WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $data_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        header("location: index.php");
        echo "Product not found";
        exit;
    }
} else {
    header("location: index.php");
    echo "No product specified";
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Page title -->
    <title><?= htmlspecialchars($data['name']) ?></title>

    <meta charset="UTF-8">
    <!-- Set cache control headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta name="description" content="<?= htmlspecialchars($data['description']) ?>">
    <meta name="keywords" content="Goofy Stores, goofy, stores">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Google -->
    <meta name="google" content="nositelinkssearchbox" />
    <meta name="google" content="notranslate" />

    <!-- Facebook -->
    <meta property="og:url" content="<?= ROOT . "apps/public/product.php?id=" . $data['id'] ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= htmlspecialchars($data['name']) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($data['description']) ?>" />

    <meta property="og:image" content="<?php
                                        if (isset($data['image'])) {
                                            echo SHORTROOT . "product_images/" . $data['image'];
                                        } else {
                                            echo SHORTROOT . "assets/images/logo.png";
                                        }
                                        ?>" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@gfydog">
    <meta name="twitter:title" content="<?= htmlspecialchars($data['name']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($data['description']) ?>">
    <meta name="twitter:image" content="<?php
                                        if (isset($data['image'])) {
                                            echo SHORTROOT . "product_images/" . $data['image'];
                                        } else {
                                            echo SHORTROOT . "assets/images/logo.png";
                                        }
                                        ?>" />

    <link rel="icon" type="image/png" href="<?php
                                            if (!empty($data['icon'])) {
                                                echo SHORTROOT . "assets/images/" . $storeData['icon'];
                                            } else {
                                                echo SHORTROOT . "assets/images/logo.png";
                                            }
                                            ?>" />

    <!-- Include jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Link to web app manifest -->
    <link rel="manifest" href="../../manifest.php">
    <link rel="stylesheet" href="../../assets/css/all.css">
</head>

<body>
    <!-- Include common header -->
    <?php require_once '../common/header.php'; ?>

    <section class="product-section">
        <?php
        // Display product information
        echo "<div class='product-info'>";
        $type = ($data['type'] == 1) ? "Physical product" : "Digital product";
        echo "<div class='p-type'>" . $type . "</div>";
        echo "<h1>" . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . "</h1>";
        echo "<p class='product-description'>" . nl2br($data['description']) . "</p>";
        echo "<p class='product-price'>Price: $" . htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8') . "</p>";

        if ($data['price'] == 0 && $data['type'] == 2) {
            echo "<a href='../account/download.php?id=" . $data_id . "&free'><button class='retro-button'>Download</button></a>";
        } else {
            echo "<button class='retro-button' onclick=\"addToCart(" . $data_id . ")\">Add to Cart</button>";
        }

        echo "</div>";

        // Display product images and thumbnails
        echo "<div class='product-images'>";
        echo "<div class='image-gallery'>";
        echo "<img src='../../product_images/" . $data['image'] . "' alt='" . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . "' id='mainImage'>";

        // Display all images as thumbnails
        echo "<div class='thumbnail-container'>";
        do {
            echo "<img src='../../product_images/" . $data['image'] . "' alt='" . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . "' class='thumbnail'>";
        } while ($data = $result->fetch_assoc());
        echo "</div>"; // end thumbnail-container
        echo "</div>"; // end image-gallery
        echo "</div>"; // end product-images
        ?>
    </section>

    <!-- Script to handle adding items to the cart -->
    <script>
        function addToCart(productId) {
            $.post("../cart/system/add_to_cart.php", {
                    product_id: productId
                })
                .done(function(data) {
                    console.log(data);
                    showAlert(data);
                });
        }
    </script>

    <!-- Include the main application script -->
    <script src="./assets/js/app.js"></script>

    <!-- Include common customBox script -->
    <?php require_once '../common/customBox.php'; ?>
</body>

</html>
