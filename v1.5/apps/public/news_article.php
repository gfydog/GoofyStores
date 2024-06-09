<?php

/**
 * This PHP script handles the news article details page, including news information and interactions.
 * Users can view news details and leave comments.
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

if (isset($_GET['id'])) {
    $news_id = intval($_GET['id']);
    $sql = "SELECT * FROM news WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $news_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
    } else {
        header("location: ./index.php");
        echo "News article not found";
    }
} else {
    header("location: ./index.php");
    echo "No news article specified";
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Page title -->
    <title><?= htmlspecialchars($data['title']) ?></title>

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
    <meta property="og:url" content="<?= ROOT."apps/public/news_article.php?id=".$news_id ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= htmlspecialchars($data['title']) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($data['description']) ?>" />

    <meta property="og:image" content="<?php
                                        if (isset($data['image']) && !empty($data['image'])) {
                                            echo $data['image'];
                                        } else {
                                            echo SHORTROOT . "assets/images/logo.png";
                                        }
                                        ?>" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@gfydog">
    <meta name="twitter:title" content="<?= htmlspecialchars($data['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($data['description']) ?>">
    <meta name="twitter:image" content="<?php
                                        if (isset($data['image'])) {
                                            echo $data['image'];
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
    <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
    <!-- Include common header -->
    <?php require_once '../common/header.php'; ?>

    <section class="news-article-section">
        <?php

        // Display news information
        echo "<div>";
        echo $data['content'];
        echo "</div>";

        ?>
    </section>

    <!-- Comment section for authenticated users -->
    <?php if (isset($user_id)) { ?>
        <section class="pdc-section">
            <a name="pdc"></a>
            <?php
            if (isset($_SESSION['user_id'])) {
            ?>
                <h2>Add your comment</h2>
                <form method="post" action="./system/add_comment.php" class="retro-form">
                    <input type="hidden" name="news_id" value="<?php echo htmlspecialchars($news_id, ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="input-group">
                        <label for="comment">Comment:</label>
                        <textarea name="comment" id="comment" required></textarea>
                    </div>
                    <button type="submit" class="retro-button">Submit comment</button>
                </form>
            <?php
            } else {
                echo "<p class='retro-notice'>You must log in to leave a comment.</p>";
            }
            ?>
        </section>

        <style>
            .pdc-section {
                padding: 30px 20px;
                background-color: #666;
                margin: auto;
            }

            .pdc-section h2 {
                font-family: "Segoe UI", Tahoma, sans-serif;
                /* Fuente similar a la de Windows 11 */
                color: #fff;
                /* Color de texto blanco */
                margin-bottom: 20px;
            }

            .retro-form {
                display: flex;
                flex-direction: column;
            }

            .input-group {
                margin-bottom: 15px;
            }

            .input-group label {
                display: block;
                margin-bottom: 5px;
                font-family: "Segoe UI", Tahoma, sans-serif;
                /* Fuente similar a la de Windows 11 */
                color: #ccc;
                /* Color de texto gris claro */
            }

            .input-group input,
            .input-group textarea {
                width: 96%;
                padding: 8px 2%;
                border-radius: 5px;
                font-size: 16px;
                border: 1px solid #ccc;
            }

            .retro-button {
                padding: 15px 40px;
                background-color: #00457C;
                /* Color personalizado para el botón */
                color: #fff;
                /* Color de texto blanco */
                border: none;
                border-radius: 5px;
                cursor: pointer;
                font-family: "Segoe UI", Tahoma, sans-serif;
                /* Fuente similar a la de Windows 11 */
                transition: background-color 0.3s;
                margin-bottom: 20px;
                font-size: 15px;
            }

            .retro-button:hover {
                background-color: #0079C1;
                /* Cambio de color al pasar el ratón */
            }

            .retro-notice {
                font-style: italic;
                color: #aa8d7b;
            }

            .pdc-display {
                background-color: #f0f0f0;
                /* Fondo oscuro para mostrar comentarios */
                padding: 30px 20px;
            }

            .pdc-display h2 {
                font-family: "Segoe UI", Tahoma, sans-serif;
                /* Fuente similar a la de Windows 11 */
                color: #222;
                /* Color de texto blanco */
                margin-bottom: 20px;
            }

            .pdc-box {
                background-color: #666;
                /* Fondo oscuro para las cajas de comentarios */
                padding: 10px;
                border-radius: 8px;
                margin-bottom: 15px;
            }

            .pdc-box h3 {
                font-family: "Segoe UI", Tahoma, sans-serif;
                /* Fuente similar a la de Windows 11 */
                color: #fff;
                /* Color de texto blanco */
                margin-bottom: 10px;
                font-size: 1.2em;
            }

            .pdc-box p {
                font-size: 1em;
                margin-bottom: 8px;
                color: #FFF;
                /* Color de texto gris claro */
            }

            .pdc-text {
                font-style: italic;
                font-family: "Segoe UI", Tahoma, sans-serif;
                /* Fuente similar a la de Windows 11 */
                color: #FFF;
            }

            .pdc-box p em {
                font-style: italic;
                color: #00457C;
                /* Color personalizado para resaltar texto */
            }
        </style>
        <section class="pdc-display">
            <h2>Comments</h2>
            <?php
            $sql = "SELECT comments.*, users.username FROM comments JOIN users ON comments.user_id = users.id WHERE post_id = ? ORDER BY created_at DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $news_id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($comment = $result->fetch_assoc()) {
                    echo "<div class='pdc-box'>";
                    echo "<h3>@" . $comment['username'] . "</h3>";
                    echo "<p class='pdc-text'>" . htmlspecialchars($comment['content'], ENT_QUOTES, 'UTF-8') . "</p>";
                    echo "</div>";
                }
            } else {
                echo "<p class='retro-notice'>No comments available for this news article.</p>";
            }
            ?>
        </section>
    <?php } ?>

    <!-- Include common customBox script -->
    <?php require_once '../common/customBox.php'; ?>
</body>

</html>