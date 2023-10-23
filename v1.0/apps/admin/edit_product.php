<?php
/**
 * PHP script for editing product details in the admin interface.
 *
 * This script is responsible for editing product information, such as name, description, price, and category, in the admin interface. It retrieves the product details from the database, allows the admin to update the information, and handles image uploads related to the product.
 *
 * PHP version 7
 *
 * @category Admin_Product_Editing
 * @package  Admin_Interface
 * @author   Your Name
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Retrieve product details based on the provided product ID.
if (isset($_GET['id'])) {
    $product_id = intval($_GET['id']);
    $sql = "SELECT * FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $description = $row['description'];
        $price = $row['price'];
        $category_id = $row['category_id'];
    } else {
        die("The product does not exist.");
    }
} else {
    die("Product ID was not provided.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>

<body class="admin-background">
    <?php require_once '../common/admin_header.php'; ?>
    <div class="admin-container">
        <h1>Edit Product</h1>
        <div class="form-group">
            <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" value="<?php echo $name; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo $description; ?></textarea>
        </div>
        <div class="form-group">
            <label for="price">Price:</label>
            <input type="number" step="0.01" name="price" id="price" value="<?php echo $price; ?>" required>
        </div>
        <div class="form-group">
            <label for="category">Category:</label>
            <select name="category" id="category">
                <?php
                $sql = "SELECT * FROM categories";
                $result = $conn->query($sql);
                while ($row = $result->fetch_assoc()) {
                    $selected = $row['id'] == $category_id ? "selected" : "";
                    echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <button onclick="updateProduct();" class="admin-btn">Update Product</button>
        </div>
    </div>
    <div class="admin-container">
        <h2>Product Images</h2>
        <form action="./system/add_images.php" method="POST" enctype="multipart/form-data" class="image-form">
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            <div class="form-group">
                <input type="file" name="product_images[]" id="product_images" multiple accept="image/*">
            </div>
            <div class="form-group">
                <input type="submit" value="Add Images" class="admin-btn">
            </div>
        </form>
        <div id="images"></div>
    </div>
    <script>
        function updateProduct() {
            let product_id = $('#product_id').val();
            let name = $('#name').val();
            let description = $('#description').val();
            let price = $('#price').val();
            let category = $('#category').val();
            let data = {
                product_id: product_id,
                name: name,
                description: description,
                price: price,
                category_id: category
            };

            $.ajax({
                type: "POST",
                url: "./system/update_product.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    if (response.success) {
                        showAlert("Producto actualizado correctamente.");
                    } else {
                        showAlert("Error al actualizar el producto.");
                    }
                },
            });
        }

        function deleteImage(product_id) {
            showConfirm("¿Está seguro de que desea eliminar esta imagen?", () => {
                window.location.href = "./system/delete_image.php?id=" + product_id;
            });
        }

        function showImages() {
            let product_id = $('#product_id').val();
            $.ajax({
                type: "GET",
                url: "../public/system/get_images.php",
                data: {
                    id: product_id
                },
                dataType: "json",
                success: function(response) {
                    let imagesHTML = "";
                    for (let i = 0; i < response.length; i++) {
                        imagesHTML += "<img src='../../" + response[i].image + "' style='width: 100px;'>";
                        imagesHTML += " <button onclick='deleteImage(" + response[i].id + ");'>Eliminar</button><br>";
                    }
                    $("#images").html(imagesHTML);
                }
            });
        }

        $(document).ready(function() {
            showImages();
        });
    </script>
    <?php require_once '../common/customBox.php'; ?>
</body>

</html>
