<?php
session_start();
header('Content-Type: application/json');

require_once "./config/configFinal.php";
require_once "./config/database.php";
require_once "./config/config.php";

$data = [];

try {
    $stmt = $conn->prepare("SELECT TITLE, short_name, description, background_color, theme_color, icon_src FROM configurations LIMIT 1");

    $stmt->execute();

    // Vincula variables a las columnas de resultados
    $stmt->bind_result($title, $shortName, $description, $backgroundColor, $themeColor, $iconSrc);

    // Usar fetch() para obtener los resultados
    if ($stmt->fetch()) {
        $data = [
            "name" => $title,
            "short_name" => $shortName,
            "description" => $description,
            "start_url" => SHORTROOT,
            "display" => "standalone",
            "background_color" => $backgroundColor,
            "theme_color" => $themeColor,
            "icons" => [
                [
                    "src" => "./assets/images/" . $iconSrc,
                    "sizes" => "512x512",
                    "type" => "image/png"
                ]
            ]
        ];
    }

    $stmt->close();
} catch (Exception $e) {
    die("Error al cargar la configuración: " . $e->getMessage());
}

echo json_encode($data);
?>
