<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

require "../../config/data.php";
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
$configurations = [];

try {
    $stmt = $conn->prepare("SELECT * FROM configurations LIMIT 1");
    $stmt->execute();

    // Vinculación de variables de resultado
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

    // Olvidaste fetchear los resultados
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
    die("Error al cargar la configuración: " . $e->getMessage());
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

        <label for="STYLE">Estilo general:</label>
        <select name="STYLE" required>
            <option value="0" <?php echo ($configurations['STYLE'] == 0) ? 'selected' : ''; ?>>Básica</option>
            <option value="1" <?php echo ($configurations['STYLE'] == 1) ? 'selected' : ''; ?>>Moderna</option>
        </select><br>

        <label for="TITLE">Título de la tienda:</label>
        <input type="text" name="TITLE" id="TITLE" value="<?php echo htmlspecialchars(isset($configurations['TITLE']) ? $configurations['TITLE'] : 'Goofy Stores'); ?>" required><br>

        <label for="short_name">Nombre corto:</label>
        <input type="text" name="short_name" id="short_name" value="<?php echo htmlspecialchars(isset($configurations['short_name']) ? $configurations['short_name'] : 'Goofy Stores'); ?>" required><br>

        <label for="description">Descripción:</label>
        <input type="text" name="description" id="description" value="<?php echo htmlspecialchars(isset($configurations['description']) ? $configurations['description'] : ''); ?>" required><br>

        <label for="background_color">Color de fondo:</label>
        <input type="text" name="background_color" id="background_color" value="<?php echo htmlspecialchars(isset($configurations['background_color']) ? $configurations['background_color'] : '#ffffff'); ?>" required><br>

        <label for="theme_color">Color del tema:</label>
        <input type="text" name="theme_color" id="theme_color" value="<?php echo htmlspecialchars(isset($configurations['theme_color']) ? $configurations['theme_color'] : '#333'); ?>" required><br>

        <input type="hidden" name="previousIconSrc" value="<?php echo isset($configurations['icon_src']) ? $configurations['icon_src'] : ''; ?>">

        <label for="icon_src">Fuente del icono:</label>
        <input type="file" name="icon_src" id="icon_src"><br>

        <label for="keywords">Palabras clave:</label>
        <input type="text" name="keywords" id="keywords" value="<?php echo htmlspecialchars(isset($configurations['keywords']) ? $configurations['keywords'] : ''); ?>" required><br>

        <input type="hidden" name="previous_image" value="<?php echo isset($configurations['image']) ? $configurations['image'] : ''; ?>">
        <input type="hidden" name="previous_icon" value="<?php echo isset($configurations['icon']) ? $configurations['icon'] : ''; ?>">

        <label for="image">Imagen:</label>
        <input type="file" name="image" id="image"><br>

        <label for="icon">Icono:</label>
        <input type="file" name="icon" id="icon"><br>

        <label for="home">Página de inicio:</label>
        <select name="home" required>
            <option value="./apps/public/index.php" <?php echo ($configurations['home'] == './apps/public/index.php') ? 'selected' : ''; ?>>index.php</option>
            <option value="./apps/public/news.php" <?php echo ($configurations['home'] == './apps/public/news.php') ? 'selected' : ''; ?>>news.php</option>
        </select><br>

        <button type="submit">Guardar configuración</button>
    </form>

    <?php require_once '../common/customBox.php'; ?>

    <script>
        $("#form").on("submit", function(e) {
            e.preventDefault();
            var formData = new FormData($('#form')[0]); // Usar FormData para manejar archivos
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
