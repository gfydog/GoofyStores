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
        echo "<h1>" . htmlspecialchars($data['name'], ENT_QUOTES, 'UTF-8') . "</h1>";
        echo "<p class='product-description'>" . nl2br($data['description']) . "</p>";
        echo "<p class='product-price'>Price: $" . htmlspecialchars($data['price'], ENT_QUOTES, 'UTF-8') . "</p>";

        // Display "Add to Cart" button for authenticated users
        if ($data['price'] == 0) {
            echo "<a href='../account/download.php?id=" . $data['id'] . "&free'><button class='retro-button'>Download</button></a>";
        } else if (isset($user_id)) {
            echo "<button class='retro-button' onclick=\"addToCart(" . $data['id'] . ")\">Add to Cart</button>";
        } else {
            echo "<a href='../authentication/login.php'><button class='retro-button'>Login</button></a>";
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
        echo "</div>";  // end thumbnail-container
        echo "</div>";  // end image-gallery
        echo "</div>";  // end product-images

        ?>
    </section>

    <!-- Review section for authenticated users -->
    <?php if (isset($user_id)) { ?>
        <section class="pdc-section">
            <a name="review"></a>
            <?php
            if (isset($_SESSION['user_id'])) {
            ?>
                <h2>Add your review</h2>
                <form method="post" action="./system/add_review.php" class="retro-form">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($data_id, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="input-group">
                        <label for="rating">Rating (1-5):</label>
                        <input type="number" name="rating" id="rating" min="1" max="5" required>
                    </div>
                    <div class="input-group">
                        <label for="review">Review:</label>
                        <textarea name="review" id="review" required></textarea>
                    </div>
                    <button type="submit" class="retro-button">Submit review</button>
                </form>
            <?php
            } else {
                echo "<p class='retro-notice'>You must log in to leave a review.</p>";
            }
            ?>
        </section>

        <!-- Display product reviews -->
        <section class="pdc-display">
            <h2>Reviews</h2>
            <?php
            $sql = "SELECT reviews.*, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE product_id = ? ORDER BY date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $data_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($review = $result->fetch_assoc()) {
                    echo "<div class='pdc-box'>";
                    echo "<h3>@" . $review['username'] . "</h3>";
                    echo "<p><strong>Rating:</strong> " . htmlspecialchars($review['rating'], ENT_QUOTES, 'UTF-8') . "/5</p>";
                    echo "<p class='pdc-text'>" . htmlspecialchars($review['review'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='retro-notice'>No reviews available for this product.</p>";
            }
            ?>
        </section>
    <?php } ?>

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