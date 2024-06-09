<?php
// Start a session to manage user data across requests.
session_start();

// Require configuration and database files.
require "../../config/common.php";
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

$user_id = getUserID();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="stylesheet" href="../../assets/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>

<body>
    <?php require_once '../common/header.php'; ?>
    <div class="container">
        <div class="invoice">
            <h2>Invoice</h2>
            <div class="container-table">
                <table id="productTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Subtotal</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Products will be added here with jQuery and Ajax -->
                    </tbody>
                </table>
            </div>
            <div class="total">
                <span>Total: </span>
                <span id="totalPrice">$0.00</span>
            </div>
            <form action="./system/checkout_paypal.php" method="POST" id="checkoutForm">
                <!-- Shipping Information Section -->
                <?php
                // Check if there are physical products in the cart
                if ($user_id !== null) {
                    $sql = "SELECT COUNT(*) AS count
            FROM cart 
            INNER JOIN products ON cart.product_id = products.id 
            WHERE cart.user_id = ? and type = 1";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $row = $result->fetch_assoc();
                    $physicalProductCount = $row['count'];

                    if ($physicalProductCount > 0) {

                        $qry = "SELECT * from users where id = ?";
                        $stmt2 = $conn->prepare($qry);
                        $stmt2->bind_param("i", $user_id);
                        $stmt2->execute();
                        $result2 = $stmt2->get_result();
                        $row2 = $result2->fetch_assoc();
                ?>

                        <h3>Shipping Information</h3>

                        <?php
                        if (isset($row2['email']) && !empty($row2['email'])) {
                            echo '<input type="hidden" name="email" value="'.$row2['email'].'" required>';
                        } else {
                            echo '<label for="email">Email:</label>';
                            echo '<input type="email" name="email" required>';
                        }
                        ?>

                        <label for="shipping_name">Name:</label>
                        <input type="text" id="shipping_name" name="shipping_name" value="<?php
                                                                                            if (isset($row2['shipping_name']) && !empty($row2['shipping_name'])) {
                                                                                                echo htmlspecialchars($row2['shipping_name'], ENT_QUOTES, 'UTF-8');
                                                                                            }
                                                                                            ?>" required>
                        <label for="shipping_address">Shipping Address:</label>
                        <input type="text" id="shipping_address" name="shipping_address" value="<?php
                                                                                                if (isset($row2['shipping_address']) && !empty($row2['shipping_address'])) {
                                                                                                    echo htmlspecialchars($row2['shipping_address'], ENT_QUOTES, 'UTF-8');
                                                                                                }
                                                                                                ?>" required>
                        <label for="shipping_city">City:</label>
                        <input type="text" id="shipping_city" name="shipping_city" value="<?php
                                                                                            if (isset($row2['shipping_city']) && !empty($row2['shipping_city'])) {
                                                                                                echo htmlspecialchars($row2['shipping_city'], ENT_QUOTES, 'UTF-8');
                                                                                            }
                                                                                            ?>" required>
                        <label for="shipping_zip">ZIP/Postal Code:</label>
                        <input type="text" id="shipping_zip" name="shipping_zip" value="<?php
                                                                                        if (isset($row2['shipping_zip']) && !empty($row2['shipping_zip'])) {
                                                                                            echo htmlspecialchars($row2['shipping_zip'], ENT_QUOTES, 'UTF-8');
                                                                                        }
                                                                                        ?>" required>
                        <label for="shipping_country">Country:</label>
                        <input type="text" id="shipping_country" name="shipping_country" value="<?php
                                                                                                if (isset($row2['shipping_country']) && !empty($row2['shipping_country'])) {
                                                                                                    echo htmlspecialchars($row2['shipping_country'], ENT_QUOTES, 'UTF-8');
                                                                                                }
                                                                                                ?>" required><br>
                <?php
                    }
                }
                ?>

                <button type="submit" class="pay-btn">Pay with PayPal</button>
            </form>

            <?php if ($user_id === null) { ?>
                <p>Please note: If you proceed without logging in, you may not be able to download purchased files more than once.</p>
            <?php } ?>
        </div>
        <script>
            $(document).ready(function() {
                var total = 0;
                $.ajax({
                    url: "./system/get_cart_items.php",
                    dataType: "json",
                    success: function(data) {
                        var cartHTML = "";
                        for (var i = 0; i < data.length; i++) {
                            let subtotal = data[i].price * data[i].quantity;
                            total += subtotal;
                            cartHTML += `
                                <tr>
                                    <td>${data[i].name}</td>
                                    <td>$${parseFloat(data[i].price).toFixed(2)}</td>
                                    <td>${data[i].quantity}</td>
                                    <td>$${subtotal.toFixed(2)}</td>
                                    <td><a href='system/delete_cart.php?id=${data[i].id}'>Delete</a></td>
                                </tr>
                            `;
                        }
                        $("#productTable tbody").html(cartHTML);
                        $("#totalPrice").text(`$${total.toFixed(2)}`);

                        console.log(total);

                        if (total <= 0) {
                            $(".pay-btn").prop("disabled", true);
                        }
                    }
                });
            });
        </script>
    </div>
</body>

</html>