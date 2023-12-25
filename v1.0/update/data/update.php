<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../");
    exit;
}

require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

try {
    $stmt = $conn->prepare("ALTER TABLE `configurations` ADD `home` VARCHAR(255) NOT NULL DEFAULT './apps/public/index.php' AFTER `icon`");
    $stmt->execute();
    echo "#1 = OK";

    // Crear la tabla de comentarios
    $stmt = $conn->prepare("CREATE TABLE comments (
        id INT AUTO_INCREMENT PRIMARY KEY,
        post_id INT NOT NULL,
        user_id INT NOT NULL,
        content TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $stmt->execute();
    echo "#2 = OK";

    // Crear la tabla de imágenes subidas
    $stmt = $conn->prepare("CREATE TABLE uploaded_images (
        id INT PRIMARY KEY AUTO_INCREMENT,
        filename VARCHAR(255) NOT NULL,
        upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    $stmt->execute();
    echo "#3 = OK";

    // Crear la tabla de noticias
    $stmt = $conn->prepare("CREATE TABLE news (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        publication_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        author VARCHAR(100) NOT NULL,
        category VARCHAR(50),
        tags VARCHAR(255),
        main_image VARCHAR(255),
        is_featured BOOLEAN DEFAULT FALSE,
        status VARCHAR(20) DEFAULT 'active'
    )");
    $stmt->execute();
    echo "#4 = OK";

    function copyDirectory($source, $destination) {
        if (is_dir($source)) {
            if (!is_dir($destination)) {
                mkdir($destination, 0777, true);
            }
    
            $files = scandir($source);
    
            foreach ($files as $file) {
                if ($file != "." && $file != "..") {
                    $sourcePath = $source . DIRECTORY_SEPARATOR . $file;
                    $destinationPath = $destination . DIRECTORY_SEPARATOR . $file;
    
                    if (is_dir($sourcePath)) {
                        copyDirectory($sourcePath, $destinationPath);
                    } else {
                        copy($sourcePath, $destinationPath);
                    }
                }
            }
        }
    }

    // Ejemplo de uso
    $sourceDirectory = "./";
    $destinationDirectory = "../../";

    copyDirectory($sourceDirectory, $destinationDirectory);

    unlink("../versions/new.zip");
    unlink("../../update.php");
    header("Location: ../../welcome.php");
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
