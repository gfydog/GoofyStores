<?php
// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration files and establish a database connection.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Check if the news ID is provided in the URL.
if (!isset($_GET['id'])) {
    // Redirect to a page indicating that the news ID is missing.
    header("Location: ../common/error.php?message=News ID is missing.");
    exit;
}

// Get the news ID from the URL.
$newsId = $_GET['id'];

// Fetch news data based on the provided ID.
$sql = "SELECT * FROM news WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $newsId);
$stmt->execute();
$result = $stmt->get_result();

// Check if the news with the specified ID exists.
if ($result->num_rows === 0) {
    // Redirect to a page indicating that the news was not found.
    header("Location: ../common/error.php?message=News not found.");
    exit;
}

// Fetch the news details.
$newsDetails = $result->fetch_assoc();

// Close the prepared statement.
$stmt->close();

// Close the database connection.
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="manifest" href="../../manifest.php">

    <!-- Include CodeMirror CSS from CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.css">
    <!-- Include the Eclipse theme for CodeMirror from CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/eclipse.css">
</head>

<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Edit News</h1>
    <form action="./system/edit.php" method="post">
        <input type="hidden" name="news_id" value="<?php echo $newsId; ?>">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo $newsDetails['title']; ?>" required><br>
        <label for="content">Content (HTML):</label>
        <div style="border: 1px solid #333;">
            <textarea name="content" id="content"><?php echo $newsDetails['content']; ?></textarea>
        </div>
        <br>
        <label for="author">Author:</label>
        <input type="text" name="author" id="author" value="<?php echo $newsDetails['author']; ?>"><br>
        
        <label for="tags">Tags (comma-separated):</label>
        <input type="text" name="tags" id="tags" value="<?php echo $newsDetails['tags']; ?>"><br>

        <label for="is_featured">Is Featured:</label>
        <input type="checkbox" name="is_featured" id="is_featured" <?php echo ($newsDetails['is_featured'] == 1) ? 'checked' : ''; ?>><br>

        <button type="submit">Save Changes</button>
    </form>

    <!-- Include CodeMirror JS from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.js"></script>
    <!-- Include HTML mode for CodeMirror from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/htmlmixed/htmlmixed.js"></script>
    <!-- Include the Eclipse theme for CodeMirror from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/eclipse.js"></script>

    <script>
        var codeEditor = CodeMirror.fromTextArea(document.getElementById("content"), {
            mode: "htmlmixed",  // HTML code mode
            theme: "eclipse",   // Eclipse theme (light)
        });
    </script>

</body>

</html>
