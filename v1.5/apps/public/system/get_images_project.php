<?php
/**
 * This PHP script returns project images in JSON format.
 */

session_start();

require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

header('Content-Type: application/json');

// Check if the user is logged in as an administrator.
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(['error' => 'You must log in as an administrator to view project images']);
    exit;
}

// Get the project ID from the GET parameters.
$project_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if a valid project ID was provided.
if ($project_id <= 0) {
    echo json_encode(['error' => 'You must provide a valid project ID']);
    exit;
}

// Query the database to select project images for the given project ID.
$sql = "SELECT * FROM project_images WHERE project_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $project_id);
$stmt->execute();
$result = $stmt->get_result();

$images = [];

// Fetch project images and store them in an array.
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $images[] = [
            'id' => $row['id'],
            'image' => 'project_images/' . $row['image']
        ];
    }
}

// Encode the array as JSON and echo it.
echo json_encode($images);

$stmt->close();
$conn->close();
?>
