<?php
/**
 * Description: This file handles the download of files associated with tokens.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start session
session_start();

// Include necessary files
require_once '../../config/common.php';
require_once '../../config/configFinal.php';
require_once '../../config/database.php';
require_once '../../config/config.php';

// Check if token is provided in the URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Retrieve file information associated with the token from the database
    $stmt = $conn->prepare("SELECT file_id, use_count FROM download_tokens WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Fetch the row
        $row = $result->fetch_assoc();
        $file_id = $row['file_id'];
        $use_count = $row['use_count'];

        // Check if the token has been used less than three times
        if ($use_count < 3) {
            // Increment the token usage count
            $use_count++;
            $update_stmt = $conn->prepare("UPDATE download_tokens SET use_count = ? WHERE token = ?");
            $update_stmt->bind_param("is", $use_count, $token);
            $update_stmt->execute();

            // Get the file URL from the products table
            $sql_file = "SELECT file_url FROM products WHERE id = ?";
            $stmt_file = $conn->prepare($sql_file);
            $stmt_file->bind_param("i", $file_id);
            $stmt_file->execute();
            $result_file = $stmt_file->get_result();

            if ($result_file->num_rows > 0) {
                // Fetch the file information
                $file = $result_file->fetch_assoc();
                $file_path = "../../files/" . $file['file_url'];

                // Check if the file exists
                if (file_exists($file_path)) {
                    // Generate a random filename
                    $random_filename = uniqid('download_', true) . '.' . pathinfo($file['file_url'], PATHINFO_EXTENSION);

                    // Download the file with the random filename
                    header('Content-Description: File Transfer');
                    header('Content-Type: application/octet-stream');
                    header('Content-Disposition: attachment; filename="' . $random_filename . '"');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($file_path));
                    readfile($file_path);
                    exit;
                }
            }
        } else {
            // If the token has been used twice, redirect to the homepage or show an error message

            // Delete the token from the database
            $stmt = $conn->prepare("DELETE FROM download_tokens WHERE token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();

            // Redirect to invalid_token.php
            header("Location: invalid_token.php");
            exit;
        }
    }
}

// If the token is not valid, redirect to invalid_token.php
header("Location: invalid_token.php");
exit;