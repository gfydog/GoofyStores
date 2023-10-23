<?php

/**
 * This PHP script handles the product details page, including product information, images, reviews, and interactions.
 * Users can view product details, add items to their cart, and leave reviews.
 */

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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Page title -->
    <title><?= htmlspecialchars(TITLE) ?></title>

    <meta charset="UTF-8">
    <!-- Set cache control headers -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <!-- Include common head content -->
    <?php require_once '../common/head.php'; ?>
    
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
        if (isset($_GET['id'])) {
            $product_id = intval($_GET['id']);
            $sql = "SELECT p.*, pi.image FROM products AS p JOIN product_images AS pi ON p.id = pi.product_id WHERE p.id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $product = $result->fetch_assoc();

                // Display product information
                echo "<div class='product-info'>";
                echo "<h1>" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "</h1>";
                echo "<p class='product-description'>" . nl2br($product['description']) . "</p>";
                echo "<p class='product-price'>Price: $" . htmlspecialchars($product['price'], ENT_QUOTES, 'UTF-8') . "</p>";

                // Display "Add to Cart" button for authenticated users
                if (isset($user_id)) {
                    echo "<button class='retro-button' onclick=\"addToCart(" . $product['id'] . ")\">Add to Cart</button>";
                } else {
                    echo "<a href='../authentication/login.php'><button class='retro-button'>Login</button></a>";
                }
                echo "</div>";

                // Display product images and thumbnails
                echo "<div class='product-images'>";
                echo "<div class='image-gallery'>";
                echo "<img src='../../product_images/" . $product['image'] . "' alt='" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "' id='mainImage'>";

                // Display all images as thumbnails
                echo "<div class='thumbnail-container'>";
                do {
                    echo "<img src='../../product_images/" . $product['image'] . "' alt='" . htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') . "' class='thumbnail'>";
                } while ($product = $result->fetch_assoc());
                echo "</div>";  // end thumbnail-container
                echo "</div>";  // end image-gallery
                echo "</div>";  // end product-images
            } else {
                header("location: index.php");
                echo "Product not found";
            }
        } else {
            header("location: index.php");
            echo "No product specified";
        }
        ?>
    </section>

    <!-- Review section for authenticated users -->
    <?php if (isset($user_id)) { ?>
        <section class="review-section">
            <a name="review"></a>
            <?php
            if (isset($_SESSION['user_id'])) {
            ?>
                <h2>Add your review</h2>
                <form method="post" action="./system/add_review.php" class="retro-form">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id, ENT_QUOTES, 'UTF-8'); ?>">
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
        <section class="reviews-display">
            <h2>Reviews</h2>
            <?php
            $sql = "SELECT reviews.*, users.username FROM reviews JOIN users ON reviews.user_id = users.id WHERE product_id = ? ORDER BY date DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $product_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($review = $result->fetch_assoc()) {
                    echo "<div class='review-box'>";
                    echo "<h3>@" . $review['username'] . "</h3>";
                    echo "<p><strong>Rating:</strong> " . htmlspecialchars($review['rating'], ENT_QUOTES, 'UTF-8') . "/5</p>";
                    echo "<p class='review-text'>" . htmlspecialchars($review['review'], ENT_QUOTES, 'UTF-8') . "</p>";
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