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
    /*
    $stmt = $conn->prepare("ALTER TABLE `configurations` 
        ADD `short_name` VARCHAR(20) NOT NULL DEFAULT 'Goofy Stores' AFTER `TITLE`, 
        ADD `description` VARCHAR(255) NOT NULL AFTER `short_name`, 
        ADD `background_color` VARCHAR(8) NOT NULL DEFAULT '#ffffff' AFTER `description`, 
        ADD `theme_color` VARCHAR(8) NOT NULL DEFAULT '#333' AFTER `background_color`, 
        ADD `icon_src` VARCHAR(8) NOT NULL DEFAULT 'logo' AFTER `theme_color`;");

    $stmt->execute();
    echo "#1 = OK";
    */

    //unlink("../../manifest.json");
    
    
    copy("./config/data.php", "../../config/data.php");

    unlink("../versions/new.zip");
    header("Location: ../../welcome.php");

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
