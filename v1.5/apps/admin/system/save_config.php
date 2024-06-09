<?php
/**
 * Description: This file handles the upload and update of configuration settings, including file uploads.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session.
session_start();

// Check if an admin is not logged in.
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Include configuration files and database connection.
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

/**
 * Function to upload a file.
 *
 * @param string $inputName - The name of the file input field.
 *
 * @return string|null - The uploaded file name, or null if there was an error.
 */
function uploadFile($inputName)
{
    // Handle file upload.
    $file = $_FILES[$inputName];
    if ($file['error'] === UPLOAD_ERR_OK) {
        $fileName = rand(100, 999) . ".png";
        $targetPath = "../../../assets/images/" . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $fileName;
        }
    }

    return null;
}

/**
 * Function to delete a file.
 *
 * @param string $fileName - The name of the file to be deleted.
 */
function deleteFile($fileName)
{
    $filePath = "../../../assets/images/" . $fileName;
    if (file_exists($filePath)) {
        unlink($filePath);
    }
}

// Attempt to insert or update configuration settings.
try {
    // Check if a row already exists in the configurations table.
    $checkStmt = $conn->prepare("SELECT * FROM configurations");
    $checkStmt->execute();
    $checkStmt->store_result();
    $rowCount = $checkStmt->num_rows;

    if ($rowCount > 0) {
        // If a row exists, update it.
        $stmt = $conn->prepare("
            UPDATE configurations SET 
                TITLE = ?,
                STYLE = ?,
                PAYPAL_SANDBOX = ?, 
                PAYPAL_CLIENT_ID = ?, 
                PAYPAL_SECRET = ?,
                short_name = ?,
                description = ?,
                background_color = ?,
                theme_color = ?,
                icon_src = ?,
                keywords = ?,
                image = ?,
                icon = ?,
                home = ?
            WHERE id = 1
        ");
    } else {
        // If no row exists, insert a new one.
        $stmt = $conn->prepare("
            INSERT INTO configurations (
                TITLE, STYLE, PAYPAL_SANDBOX, PAYPAL_CLIENT_ID, PAYPAL_SECRET, short_name, description, background_color, theme_color, icon_src, keywords, image, icon, home
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
    }

    // Bind parameters for configuration settings.
    $TITLE = isset($_POST['TITLE']) ? $_POST['TITLE'] : 'Goofy Stores';
    $STYLE = isset($_POST['STYLE']) ? $_POST['STYLE'] : 0;
    $PAYPAL_SANDBOX = isset($_POST['PAYPAL_SANDBOX']) ? 1 : 0;
    $PAYPAL_CLIENT_ID = isset($_POST['PAYPAL_CLIENT_ID']) ? $_POST['PAYPAL_CLIENT_ID'] : '';
    $PAYPAL_SECRET = isset($_POST['PAYPAL_SECRET']) ? $_POST['PAYPAL_SECRET'] : '';
    $short_name = isset($_POST['short_name']) ? $_POST['short_name'] : 'Goofy Stores';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $background_color = isset($_POST['background_color']) ? $_POST['background_color'] : '#ffffff';
    $theme_color = isset($_POST['theme_color']) ? $_POST['theme_color'] : '#333';

    $home = './apps/public/index.php';
    if(isset($_POST['home'])){
        if($_POST['home'] == './apps/public/news.php'){
            $home = './apps/public/news.php';
        }else if($_POST['home'] == './apps/crowdfunding/index.php'){
            $home = './apps/crowdfunding/index.php';
        }
    }

    $previousIconSrc = isset($_POST['previousIconSrc']) ? $_POST['previousIconSrc'] : '';

    $icon_src = isset($_FILES['icon_src']) ? uploadFile('icon_src') : ''; // Check if a file is uploaded
    $keywords = isset($_POST['keywords']) ? $_POST['keywords'] : '';

    // Get previous file names.
    $previousImage = isset($_POST['previous_image']) ? $_POST['previous_image'] : '';
    $previousIcon = isset($_POST['previous_icon']) ? $_POST['previous_icon'] : '';

    // Upload new files and delete old ones if needed.
    $image = isset($_FILES['image']) ? uploadFile('image') : $previousImage; // Check if a file is uploaded
    $icon = isset($_FILES['icon']) ? uploadFile('icon') : $previousIcon; // Check if a file is uploaded

    if (!empty($icon)) {
        // Delete the previous icon file if it exists.
        if (!empty($previousIcon)) {
            deleteFile($previousIcon);
        }
    } else {
        $icon = $previousIcon; // Keep the previous icon file if no new one is uploaded
    }

    if (!empty($image)) {
        // Delete the previous image file if it exists.
        if (!empty($previousImage)) {
            deleteFile($previousImage);
        }
    } else {
        $image = $previousImage; // Keep the previous image file if no new one is uploaded
    }

    if (!empty($icon_src)) {
        // Delete the previous icon source file if it exists.
        if (!empty($previousIconSrc)) {
            deleteFile($previousIconSrc);
        }
    } else {
        $icon_src = $previousIconSrc; // Keep the previous icon source file if no new one is uploaded
    }

    $stmt->bind_param("ssisssssssssss", $TITLE, $STYLE, $PAYPAL_SANDBOX, $PAYPAL_CLIENT_ID, $PAYPAL_SECRET, $short_name, $description, $background_color, $theme_color, $icon_src, $keywords, $image, $icon, $home);

    // Execute the query to insert or update configuration settings.
    $stmt->execute();

    // Show a success message.
    echo "Configuration saved!";
} catch (Exception $e) {
    // If an error occurs, display the error message.
    echo "Error: " . $e->getMessage();
}
?>
