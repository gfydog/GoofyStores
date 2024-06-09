<!-- Header section containing the site title -->
<header>
    <!-- Link to the site's home page -->
    <a href="<?= ROOT . "apps/public/" ?>">
        <!-- Site title -->
        <h1><?= htmlspecialchars(TITLE) ?></h1>
    </a>
</header>

<!-- Navigation menu section -->
<nav>
    <div>
        <ul>
            <!-- Store link -->
            <li><a href="<?= ROOT ?>">WEB</a></li>

            <!-- Conditional section for admin actions -->
            <?php if (isset($_SESSION['admin_id'])) { ?>
                <!-- Admin Products link -->
                <li><a href="<?= ROOT . "apps/news/" ?>">News</a></li>
                <!-- Admin Products link -->
                <li><a href="<?= ROOT . "apps/admin/" ?>admin_products.php">Products</a></li>
                <!-- Admin Orders link -->
                <li><a href="<?= ROOT . "apps/admin/" ?>admin_orders.php">Orders</a></li>
                <!-- All Files link -->
                <li><a href="<?= ROOT . "apps/admin/" ?>all_files.php">Files</a></li>

                <li><a target="_blank" href="<?= ROOT . "apps/gallery/" ?>">Gallery</a></li>
                <!-- Admin Data link -->
                <li><a href="<?= ROOT . "apps/admin/" ?>admin_data.php">Settings</a></li>
                <!-- Manage Categories link -->
                <li><a href="<?= ROOT . "apps/admin/" ?>manage_categories.php">Categories</a></li>
                <li><a href="<?= ROOT . "apps/crowdfunding/" ?>admin.php">Crowdfunding</a></li>
                <!-- Logout link for administrators -->
                <li><a href="<?= ROOT . "apps/admin/" ?>logout.php">Logout</a></li>
            <?php } ?>
        </ul>
    </div>
</nav>

<style>
    /* Styles for the header (top bar) */
    header {
        color: #fff;
        text-align: center;
        padding: 20px 0px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
        height: auto;
        margin: 0;
        overflow: hidden;
        /* Gradient background with animation */
        background: linear-gradient(45deg, #000, #000 25%, #333 25%, #333 50%, #000 50%, #000 75%, #333 75%, #333);
        background-size: 40px 40px;
        animation: moveBackground 45s linear infinite;
    }

    /* Keyframes animation for the background gradient */
    @keyframes moveBackground {
        0% {
            background-position: 0px 0px;
        }
        100% {
            background-position: 100% 100%;
        }
    }

    /* Styling for the site title within the header */
    header h1 {
        font-size: 36px;
        margin: 0;
        padding: 0;
        color: #FFF;
    }

    /* Styles for the navigation bar */
    nav {
        background-color: #FFF; /* Light gray background */
        text-align: center;
        padding: 10px 0;
        overflow: auto;
    }

    /* Styles for the navigation menu items */
    nav ul {
        list-style: none;
        padding: 0;
    }

    nav ul li {
        display: inline-flex; /* Makes the <li> elements behave as inline-flex */
        margin: 5px; /* Spacing between elements (adjust as needed) */
    }

    nav ul li:last-child {
        margin-right: 0; /* Removes the right margin from the last element to prevent extra space */
    }

    /* Styles for the navigation links */
    nav ul li a {
        text-decoration: none;
        color: #FFF;
        font-size: 14px;
        transition: color 0.2s ease;
        background-color: darkblue;
        padding: 5px 10px;
        margin: 5px;
        border-radius: 20px;
        font-weight: bold;
        min-width: 100px;
    }

    /* Hover effect for navigation links */
    nav ul li a:hover {
        color: #7b68ee; /* Link color on hover */
    }

    /* Special style for the cart button */
    .cart-btn {
        background-color: #a32f2f;
        color: #fff;
    }

    /* Hover effect for the cart button */
    .cart-btn:hover {
        background-color: #0056b3;
        color: #fff;
    }
</style>
