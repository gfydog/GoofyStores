<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-5SYFYRBVL9"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-5SYFYRBVL9');
</script>

<style>
    @import url(//fonts.googleapis.com/css?family=Lato:300:400);

    body {
        margin: 0;
    }

    nav {
        background-color: #FFF;
        text-align: center;
        padding: 10px 0;
        overflow: auto;
    }

    nav ul {
        list-style: none;
        padding: 0;
    }

    nav ul li {
        display: inline-flex;
        /* Hace que los elementos <li> se comporten como inline-flex */
        margin: 5px;
        /* Espacio entre elementos (ajusta según sea necesario) */
    }

    nav ul li:last-child {
        margin-right: 0;
        /* Elimina el margen derecho del último elemento para evitar espacio extra */
    }

    nav ul li a {
        text-decoration: none;
        color: #666;
        font-size: 14px;
        transition: color 0.2s ease;
        background-color: #DDD;
        padding: 5px 10px;
        margin: 5px;
        border-radius: 20px;
        font-weight: bold;
        min-width: 100px;
    }

    nav ul li a:hover {
        background-color: #00457C;
        color: #FFF;
    }

    .cart-btn {
        background-color: #00457C;
        color: #fff;
    }

    .cart-btn:hover {
        background-color: #FFF;
        color: #999;
    }

    .hdrt h1 {
        font-family: 'Lato', sans-serif;
        font-weight: 300;
        letter-spacing: 2px;
        font-size: 48px;
        color: #FFF;
    }
</style>
<?php if (STYLE === "0") { ?>
    <div class="hdrt">

        <!--Content before waves-->
        <div class="inner-header flex">
            <h1><?= htmlspecialchars(TITLE) ?></h1>
        </div>
    </div>

    <nav>
        <div>
            <ul>
                <!-- Store link -->
                <li><a href="<?= ROOT . "apps/public/" ?>">Store</a></li>
                <li><a href="<?= ROOT . "apps/public/news.php" ?>">News</a></li>
                
                <?php if (!isset($_SESSION['user_id'])) { ?>
                    <!-- Display links for users who are not logged in -->
                    <li><a href="<?= ROOT . "apps/authentication/" ?>login.php">Log In</a></li>
                    <li><a href="<?= ROOT . "apps/authentication/" ?>register.php">Register</a></li>
                <?php } else { ?>
                    <!-- Display links for logged-in users -->
                    <li><a href="<?= ROOT . "apps/account/" ?>my_files.php">My Files</a></li>
                    <li><a href="<?= ROOT . "apps/cart/" ?>cart.php" class="cart-btn">Cart</a></li>
                    <li><a href="<?= ROOT . "apps/authentication/" ?>logout.php">Logout</a></li>
                <?php }
                if (isset($_SESSION['admin_id'])) { ?>
                    <!-- Display admin control panel link for administrators -->
                    <li><a href="<?= ROOT . "apps/admin/" ?>admin_products.php">Control Panel</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <style>
        /* Estilo para el encabezado (header) */
        .hdrt {
            color: #999;
            text-align: center;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.2);
            height: auto;
            margin: 0;
            overflow: hidden;
            background: linear-gradient(45deg,
                    #666,
                    #666 25%,
                    #333 25%,
                    #333 50%,
                    #666 50%,
                    #666 75%,
                    #333 75%,
                    #333);
            background-size: 40px 40px;
            animation: moveBackground 45s linear infinite;
            width: 100%;
        }

        @keyframes moveBackground {
            0% {
                background-position: 0px 0px;
            }

            100% {
                background-position: 100% 100%;
            }
        }
    </style>

<?php } else 
if (STYLE === "1") { ?>
    <div class="hdrt">

        <!--Content before waves-->
        <div class="inner-header flex">
            <h1><?= htmlspecialchars(TITLE) ?></h1>
        </div>

        <!--Waves Container-->
        <div>
            <svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
                <defs>
                    <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
                </defs>
                <g class="parallax">
                    <use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(255,255,255,0.7" />
                    <use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(255,255,255,0.5)" />
                    <use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(255,255,255,1)" />
                    <use xlink:href="#gentle-wave" x="48" y="7" fill="rgba(255,255,255,0.1)" />
                </g>
            </svg>
        </div>
        <!--Waves end-->

    </div>
    <!--Header ends-->
    <nav>
        <div>
            <ul>
                <!-- Store link -->
                <li><a href="<?= ROOT . "apps/public/" ?>">Store</a></li>
                <li><a href="<?= ROOT . "apps/public/news.php" ?>">News</a></li>

                <?php if (!isset($_SESSION['user_id'])) { ?>
                    <!-- Display links for users who are not logged in -->
                    <li><a href="<?= ROOT . "apps/authentication/" ?>login.php">Log In</a></li>
                    <li><a href="<?= ROOT . "apps/authentication/" ?>register.php">Register</a></li>
                <?php } else { ?>
                    <!-- Display links for logged-in users -->
                    <li><a href="<?= ROOT . "apps/account/" ?>my_files.php">My Files</a></li>
                    <li><a href="<?= ROOT . "apps/cart/" ?>cart.php" class="cart-btn">Cart</a></li>
                    <li><a href="<?= ROOT . "apps/authentication/" ?>logout.php">Logout</a></li>
                <?php }
                if (isset($_SESSION['admin_id'])) { ?>
                    <!-- Display a control panel link for administrators -->
                    <li><a href="<?= ROOT . "apps/admin/" ?>admin_products.php">Control Panel</a></li>
                <?php } ?>
            </ul>
        </div>
    </nav>

    <style>
        p {
            font-family: 'Lato', sans-serif;
            letter-spacing: 1px;
            font-size: 14px;
            color: #333333;
        }

        .hdrt {
            position: relative;
            text-align: center;
            background: linear-gradient(60deg, #8C52FF 0%, #FF914D 100%);
            color: white;
        }

        .logo {
            width: 50px;
            fill: white;
            padding-right: 15px;
            display: inline-block;
            vertical-align: middle;
        }

        .inner-header {
            height: 0px;
            width: 100%;
            margin: 0;
            padding: 0;
            padding-top: 50px;
        }

        .flex {
            /*Flexbox for containers*/
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .waves {
            position: relative;
            width: 100%;
            height: 15vh;
            margin-bottom: -10px;
            /*Fix for safari gap*/
            min-height: 50px;
            max-height: 150px;
        }

        .content {
            position: relative;
            text-align: center;
            background-color: white;
        }

        /* Animation */

        .parallax>use {
            animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
        }

        .parallax>use:nth-child(1) {
            animation-delay: -2s;
            animation-duration: 7s;
        }

        .parallax>use:nth-child(2) {
            animation-delay: -3s;
            animation-duration: 10s;
        }

        .parallax>use:nth-child(3) {
            animation-delay: -4s;
            animation-duration: 13s;
        }

        .parallax>use:nth-child(4) {
            animation-delay: -5s;
            animation-duration: 20s;
        }

        @keyframes move-forever {
            0% {
                transform: translate3d(-90px, 0, 0);
            }

            100% {
                transform: translate3d(85px, 0, 0);
            }
        }

        /*Shrinking for mobile*/
        @media (max-width: 768px) {
            .waves {
                height: 40px;
                min-height: 40px;
            }

            .content {
                height: 30vh;
            }

            h1 {
                font-size: 24px;
            }
        }
    </style>

<?php } ?>