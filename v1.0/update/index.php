<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../apps/authentication/admin_login.php");
    exit; // Halt execution for non-authenticated users.
}

require "../config/configFinal.php";
require "../config/database.php";
require "../config/config.php";

$newURL = './versions/new.zip'; // The path to the file on your server.

function ext($url) {
    $zipFile = $url; // Path to the ZIP file you want to extract
    $extractTo = './'; // Path to the directory where you want to extract the files

    $zip = new ZipArchive();

    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($extractTo);
        $zip->close();
        echo 'The ZIP file has been successfully extracted to ' . $extractTo;

        if (file_exists($extractTo . 'data/update.php')) {
            header("Location: " . $extractTo . 'data/update.php');
        } else {
            echo 'The update.php file was not found in the update.';
        }
    } else {
        echo 'The ZIP file could not be opened, or there was an error while extracting it.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (move_uploaded_file($_FILES['archivo_zip']['tmp_name'], $newURL)) {
        echo 'The file has been uploaded successfully.';
        ext($newURL);
    } else {
        echo 'The file could not be uploaded.';
    }
} elseif (isset($_GET['descargar'])) {
    // Process file download from the Internet
    $url = "https://goofy.dog/goofy/download/new.zip";

    if (file_put_contents($newURL, file_get_contents($url))) {
        echo 'The file has been downloaded successfully.';
        ext($newURL);
    } else {
        echo 'The file could not be downloaded.';
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../manifest.php">
</head>

<body>
    <?php require_once '../apps/common/admin_header.php'; ?>
    <form action="index.php" method="post" enctype="multipart/form-data">
        <h2>Upload a ZIP file to the server</h2>
        Select a ZIP file: <input type="file" name="archivo_zip" />
        <input type="submit" value="Update" />
    </form>

    <form action="index.php" method="get">
        <h2>Auto Update</h2>
        <input type="hidden" name="descargar" value="1">
        <input type="submit" value="Update">
    </form>
</body>

</html>
