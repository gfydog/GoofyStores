<?php
/**
 * Description: This file handles the upload and update of configuration settings, including file uploads, in the admin panel.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include necessary configuration and database files.
require_once "../../config/data.php";
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

$configurations = [];

try {
    // Fetch configuration settings from the database.
    $stmt = $conn->prepare("SELECT * FROM configurations LIMIT 1");
    $stmt->execute();

    // Bind result variables.
    $stmt->bind_result(
        $id,
        $paypalSandbox,
        $paypalClientId,
        $paypalSecret,
        $style,
        $title,
        $shortName,
        $description,
        $backgroundColor,
        $themeColor,
        $iconSrc,
        $keywords,
        $image,
        $icon,
        $home
    );

    // Fetch the results.
    if ($stmt->fetch()) {
        $configurations = [
            'id' => $id,
            'PAYPAL_SANDBOX' => $paypalSandbox,
            'PAYPAL_CLIENT_ID' => $paypalClientId,
            'PAYPAL_SECRET' => $paypalSecret,
            'STYLE' => $style,
            'TITLE' => $title,
            'short_name' => $shortName,
            'description' => $description,
            'background_color' => $backgroundColor,
            'theme_color' => $themeColor,
            'icon_src' => $iconSrc,
            'keywords' => $keywords,
            'image' => $image,
            'icon' => $icon,
            'home' => $home
        ];
    }

    $stmt->close();
} catch (Exception $e) {
    // Handle any errors that occur during configuration loading.
    die("Error loading configuration: " . $e->getMessage());
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

<body>

    <?php require_once '../common/admin_header.php'; ?>

    <div class="div-link">
        <a href="../../update/" class="pass">Version: <?= VERSION ?></a>
    </div>

    <form id="form" action="./system/save_config.php" method="post" enctype="multipart/form-data">
        <label for="PAYPAL_SANDBOX">PAYPAL_SANDBOX:</label>
        <input type="checkbox" name="PAYPAL_SANDBOX" id="PAYPAL_SANDBOX" <?php echo (!isset($configurations['PAYPAL_SANDBOX']) || $configurations['PAYPAL_SANDBOX'] == 1) ? 'checked' : ''; ?>><br>

        <label for="PAYPAL_CLIENT_ID">PAYPAL_CLIENT_ID:</label>
        <input type="text" name="PAYPAL_CLIENT_ID" id="PAYPAL_CLIENT_ID" value="<?php echo htmlspecialchars(isset($configurations['PAYPAL_CLIENT_ID']) ? $configurations['PAYPAL_CLIENT_ID'] : ''); ?>" required><br>

        <label for="PAYPAL_SECRET">PAYPAL_SECRET:</label>
        <input type="text" name="PAYPAL_SECRET" id="PAYPAL_SECRET" value="<?php echo htmlspecialchars(isset($configurations['PAYPAL_SECRET']) ? $configurations['PAYPAL_SECRET'] : ''); ?>" required><br>

        <br>

        <label for="STYLE">General Style:</label>
        <select name="STYLE" required>
            <option value="0" <?php echo ($configurations['STYLE'] == 0) ? 'selected' : ''; ?>>Basic</option>
            <option value="1" <?php echo ($configurations['STYLE'] == 1) ? 'selected' : ''; ?>>Modern</option>
        </select><br>

        <label for="TITLE">Store Title:</label>
        <input type="text" name="TITLE" id="TITLE" value="<?php echo htmlspecialchars(isset($configurations['TITLE']) ? $configurations['TITLE'] : 'Goofy Stores'); ?>" required><br>

        <label for="short_name">Short Name:</label>
        <input type="text" name="short_name" id="short_name" value="<?php echo htmlspecialchars(isset($configurations['short_name']) ? $configurations['short_name'] : 'Goofy Stores'); ?>" required><br>

        <label for="description">Description:</label>
        <input type="text" name="description" id="description" value="<?php echo htmlspecialchars(isset($configurations['description']) ? $configurations['description'] : ''); ?>" required><br>

        <label for="background_color">Background Color:</label>
        <input type="text" name="background_color" id="background_color" value="<?php echo htmlspecialchars(isset($configurations['background_color']) ? $configurations['background_color'] : '#ffffff'); ?>" required><br>

        <label for="theme_color">Theme Color:</label>
        <input type="text" name="theme_color" id="theme_color" value="<?php echo htmlspecialchars(isset($configurations['theme_color']) ? $configurations['theme_color'] : '#333'); ?>" required><br>

        <input type="hidden" name="previousIconSrc" value="<?php echo isset($configurations['icon_src']) ? $configurations['icon_src'] : ''; ?>">

        <label for="icon_src">Icon Source:</label>
        <input type="file" name="icon_src" id="icon_src"><br>

        <label for="keywords">Keywords:</label>
        <input type="text" name="keywords" id="keywords" value="<?php echo htmlspecialchars(isset($configurations['keywords']) ? $configurations['keywords'] : ''); ?>" required><br>

        <input type="hidden" name="previous_image" value="<?php echo isset($configurations['image']) ? $configurations['image'] : ''; ?>">
        <input type="hidden" name="previous_icon" value="<?php echo isset($configurations['icon']) ? $configurations['icon'] : ''; ?>">

        <label for="image">Image:</label>
        <input type="file" name="image" id="image"><br>

        <label for="icon">Icon:</label>
        <input type="file" name="icon" id="icon"><br>

        <label for="home">Home Page:</label>
        <select name="home" required>
            <option value="./apps/public/index.php" <?php echo ($configurations['home'] == './apps/public/index.php') ? 'selected' : ''; ?>>Store</option>
            <option value="./apps/public/news.php" <?php echo ($configurations['home'] == './apps/public/news.php') ? 'selected' : ''; ?>>News</option>
            <option value="./apps/crowdfunding/index.php" <?php echo ($configurations['home'] == './apps/crowdfunding/index.php') ? 'selected' : ''; ?>>Crowdfunding</option>
        </select><br>

        <button type="submit">Save Configuration</button>
    </form>

    <?php require_once '../common/customBox.php'; ?>

    <script>
        // Handle form submission via AJAX
        $("#form").on("submit", function(e) {
            e.preventDefault();
            var formData = new FormData($('#form')[0]); // Use FormData to handle files
            $.ajax({
                type: 'POST',
                url: './system/save_config.php',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    showAlert(response, () => {
                        location.reload(true);
                    });
                },
                error: function() {
                    showAlert("Error saving configuration!");
                }
            });
        });
    </script>
</body>

</html>
