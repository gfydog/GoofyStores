<?php
/**
 * This PHP script handles the product listing page and its associated functionalities.
 * It retrieves product data from the database based on search, filter, and order criteria.
 * Users can view and filter products, add them to their cart, and navigate through pages.
 */

// Start a session
session_start();

// Redirect to installation if the configuration file does not exist
if (!file_exists('../../config/configFinal.php')) {
  header("location: ../../install/index.php");
  exit;
}

// Require configuration and database files
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Get user ID from the session, if available
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

  <!-- Search bar -->
  <div class="search-bar">
    <form id="search-form" class="search-form">
      <input type="text" id="search" placeholder="Search...">
      <button id="search-button">Search</button>
    </form>
  </div>

  <!-- Filters section for authenticated users -->
  <?php if (isset($user_id)) { ?>
    <section class="filtros">
      <form id="filter-form" class="filter-form">
        <label for="category">Category:</label>
        <select id="filter">
          <option value="">All</option>
          <?php
          // Fetch and display product categories
          $sql = "SELECT * FROM categories";
          $result = $conn->query($sql);
          while ($row = $result->fetch_assoc()) {
            $selected = $row['id'] == $category_id ? "selected" : "";
            echo "<option value='" . $row['id'] . "' $selected>" . $row['name'] . "</option>";
          }
          ?>
        </select>

        <label for="order">Order by:</label>
        <select id="order">
          <option value="price_asc">Price: Low to High</option>
          <option value="price_desc">Price: High to Low</option>
          <option value="name_asc">Name: A-Z</option>
          <option value="name_desc">Name: Z-A</option>
        </select>

        <input type="submit" value="Filter" class="btn">
      </form>
    </section>
  <?php } ?>

  <!-- Product listings section -->
  <section id="products" class="bxs"></section>
  <div id="pagination"></div>

  <!-- Admin site link -->
  <div class="div-link">
    <a href="../authentication/admin_login.php" class="pass">Admin Site</a>
  </div>

  <script>
    // Function to load products based on filters
    function loadProducts(search = '', filter = '', order = '', page = 1) {
      $.ajax({
        url: "./system/get_products.php",
        data: {
          search: search,
          filter: filter,
          order: order,
          page: page
        },
        dataType: "json",
        success: function(data) {
          console.log(data);
          var productsHTML = "";
          for (var i = 0; i < data.products.length; i++) {
            productsHTML += '<article class="bx">';
            productsHTML += '<a href="product.php?id=' + data.products[i].id + '">';
            productsHTML += "<img src='../../product_images/" + data.products[i].images[0]['image'] + "'>";
            productsHTML += "<h2>" + data.products[i].name + "</h2>";
            productsHTML += '</a>';
            productsHTML += "<p>$" + data.products[i].price + "</p>";
            <?php if (isset($user_id)) { ?>
              productsHTML += "<button onclick=\"addToCart(" + data.products[i].id + ")\">Add to Cart</button>";
            <?php } else { ?>
              productsHTML += "<a href='../authentication/login.php'><button>Login</button></a>";
            <?php } ?>
            productsHTML += "</article>";
          }
          $("#products").html(productsHTML);

          <?php if (isset($user_id)) { ?>
            // Generate pagination buttons
            var paginationHTML = "";
            for (var i = 1; i <= data.total_pages; i++) {
              paginationHTML += "<button onclick=\"loadProducts('" + search + "', '" + filter + "', '" + order + "', " + i + ")\">" + i + "</button>";
            }
            $("#pagination").html(paginationHTML);
          <?php } ?>
        },
        error: function(data) {
          console.log(data.responseText);
        }
      });
    }

    // Function to add a product to the cart
    function addToCart(productId) {
      $.post("../cart/system/add_to_cart.php", {
          product_id: productId
        })
        .done(function(data) {
          console.log(data);
          showAlert(data);
        });
    }

    $(document).ready(function() {
      // Load products on page load
      loadProducts();

      // Handle search form submission
      $("#search-form").on("submit", function(e) {
        e.preventDefault();
        var search = $("#search").val();
        var filter = $("#filter").val();
        var order = $("#order").val();
        loadProducts(search, filter, order);
      });

      // Handle filter form submission
      $("#filter-form").on("submit", function(e) {
        e.preventDefault();
        var search = $("#search").val();
        var filter = $("#filter").val();
        var order = $("#order").val();
        loadProducts(search, filter, order);
      });
    });
  </script>

  <!-- Include common customBox script -->
  <?php require_once '../common/customBox.php'; ?>
</body>
</html>
