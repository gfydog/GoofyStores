<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../authentication/admin_login.php");
    exit;
}

require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $uploadDir = '../../../news_images/';

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $uploadedFiles = $_FILES['files'];

    foreach ($uploadedFiles['name'] as $key => $originalName) {
        $tmpName = $uploadedFiles['tmp_name'][$key];

        $uniqueName = uniqid('img_') . '_' . bin2hex(random_bytes(8)) . '.' . pathinfo($originalName, PATHINFO_EXTENSION);

        $targetPath = $uploadDir . $uniqueName;

        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $fileExtension = pathinfo($originalName, PATHINFO_EXTENSION);

        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            echo 'Error: File type not allowed. Only JPG, JPEG, PNG, and GIF images are permitted.<br>';
            continue;
        }

        $maxFileSize = 20000000;

        if (filesize($tmpName) > $maxFileSize) {
            echo 'Error: Image file size is too large. The maximum allowed size is 20 MB.<br>';
            continue;
        }

        if (move_uploaded_file($tmpName, $targetPath)) {
            $sql = "INSERT INTO uploaded_images (filename, upload_date) VALUES (?, NOW())";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $uniqueName);

            if ($stmt->execute()) {
                $response = ['success' => 'Image uploaded successfully.'];
                echo json_encode($response);
            } else {
                $response = ['error' => 'Error inserting record into database.'];
                http_response_code(500);
                echo json_encode($response);
            }
        } else {
            echo 'Error uploading image ' . $originalName . '.<br>';
        }
    }
}
header("location: ../");