<?php
// Start a session to manage user data across requests.
session_start();

// Require configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Check if a user session is active. If not, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
  header("location: ../authentication/login.php");
}
$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Shopping Cart</title>
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
      <form action="./system/checkout_paypal.php" method="POST">
        <button id="pay" type="submit" class="pay-btn">Pay with PayPal</button>
      </form>
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
            if (total <= 0) {
              $("#pay").prop("disabled", true);
            }
          }
        });
      });
    </script>
  </div>
</body>
</html>
