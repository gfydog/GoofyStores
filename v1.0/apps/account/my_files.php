<?php
/**
 * This PHP script retrieves a list of user-specific files and displays them on a web page.
 * It first checks if the user is logged in, then queries the database to fetch file information
 * related to the user's purchases. The retrieved data is then displayed on the web page.
 *
 * PHP version 7
 *
 * @category User_Files
 * @package  User_File_List
 * @author   Raúl Méndez Rodríguez
 */

// Start a PHP session.
session_start();

// Require configuration files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Check if the user is not logged in, redirect to the login page.
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Get the user ID from the session.
$user_id = $_SESSION['user_id'];

// SQL query to retrieve a list of user-specific files.
$sql = "SELECT 
        pf.file_id, 
        MAX(p.id) as id, 
        MAX(f.name) as name, 
        MAX(f.description) as description, 
        MAX(pf.quantity) as quantity, 
        MIN(pi.image) as image
        FROM purchases p
        JOIN purchase_files pf ON p.id = pf.purchase_id
        JOIN products f ON pf.file_id = f.id
        LEFT JOIN product_images pi ON pi.product_id = f.id
        WHERE p.user_id = ?
        GROUP BY pf.file_id
        ORDER BY MAX(p.purchase_date) DESC";

// Prepare the SQL statement.
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();

// Get the result set.
$result = $stmt->get_result();

// Create an empty array to store file information.
$files = [];

// Iterate through the result set and store file details in the array.
while ($row = $result->fetch_assoc()) {
    $files[] = [
        'file_id' => $row['file_id'],
        'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
        'description' => htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'),
        'quantity' => $row['quantity'],
        'image' => $row['image']
    ];
}

// Close the database connection.
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Files</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>

<body>
    <?php require_once '../common/header.php'; ?>

    <section id="products" class="productos">
        <?php if (count($files) > 0) : ?>
            <?php foreach ($files as $file) : ?>
                <article class="producto">
                    <a href="../public/product.php?id=<?php echo $file['file_id']; ?>">
                        <img src="../../product_images/<?php echo $file['image']; ?>">
                        <h2><?php echo $file['name']; ?></h2>
                    </a>
                    <p></p>
                    
                    <?php if (isset($user_id)) { ?>
                        <a href='download.php?id=<?php echo $file['file_id']; ?>'><button>Download</button></a>
                    <?php } ?>
                </article>
            <?php endforeach; ?>
        <?php else : ?>
            <p>You have no files available.</p>
        <?php endif; ?>
    </section>

    <?php require_once '../common/customBox.php'; ?>
</body>
</html>
