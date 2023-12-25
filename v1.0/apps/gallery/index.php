<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="manifest" href="../../manifest.php">

    <title>Image Gallery</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        h1 {
            text-align: center;
        }

        form {
            text-align: center;
            margin-bottom: 20px;
        }

        #image-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .gallery-image {
            margin: 10px;
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>

<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Image Gallery</h1>

    <form action="./system/upload.php" method="post" enctype="multipart/form-data">
        <label for="file">Choose Image(s):</label>
        <input type="file" name="files[]" id="file" multiple accept="image/*" required>
        <button type="submit">Upload Images</button>
    </form>

    <div id="image-container">
        <?php
        $sql = "SELECT filename FROM uploaded_images";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $imagePath = $row['filename'];
                echo '<img src="../../news_images/' . $imagePath . '" alt="Gallery Image" class="gallery-image"  onclick="copyToClipboard(this.src)" >';
            }
        } else {
            echo '<p>No images found.</p>';
        }

        $conn->close();
        ?>
    </div>
    <script>
        function copyToClipboard(text) {
            // Crear un elemento de entrada de texto temporal
            var tempInput = document.createElement("input");

            // Asignar la cadena a copiar al valor del elemento
            tempInput.value = text;

            // Agregar el elemento al documento
            document.body.appendChild(tempInput);

            // Seleccionar el contenido del elemento
            tempInput.select();
            tempInput.setSelectionRange(0, 99999); // Para dispositivos móviles

            // Copiar el contenido al portapapeles
            document.execCommand("copy");

            // Eliminar el elemento de entrada de texto temporal
            document.body.removeChild(tempInput);

        }
    </script>
</body>

</html>