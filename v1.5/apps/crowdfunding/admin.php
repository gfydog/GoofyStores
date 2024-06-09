<?php
// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration and database files.
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// SQL query to retrieve crowdfunding project information from the database.
$sql = "SELECT id, title, description, goal, start_date, end_date FROM projects ORDER BY start_date DESC";
$result = $conn->query($sql);

$projects = [];

// Fetch project data and store it in the $projects array.
while ($row = $result->fetch_assoc()) {
    $projects[] = $row;
}

// Close the database connection.
$conn->close();
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Crowdfunding Projects</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>

<body>

    <?php require_once '../common/admin_header.php'; ?>
    <h1>Administer Crowdfunding Projects</h1>
    <div id="a-container">
        <a href="add_project.php">Add Project</a>
    </div>
    <div class="container-table">
        <table border="1">
            <tr>
                <th>Title</th>
                <th>Goal</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Donors</th>
                <th colspan="2">Actions</th>
            </tr>
            <?php foreach ($projects as $project) : ?>
                <tr>
                    <td><?php echo $project['title']; ?></td>
                    <td><?php echo $project['goal']; ?></td>
                    <td><?php echo $project['start_date']; ?></td>
                    <td><?php echo $project['end_date']; ?></td>
                    <td>
                        <a href="donors.php?project_id=<?php echo $project['id']; ?>">view donors</a>
                    </td>
                    <td>
                        <a href="edit_project.php?id=<?php echo $project['id']; ?>"><button>Edit</button></a>
                    </td>
                    <td>
                        <button onclick="showConfirm('Are you sure you want to delete this project?', () => { window.location.href = '<?= "./system/delete_project.php?id=" . $project['id']; ?>';});">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <?php require_once '../common/customBox.php'; ?>
    
</body>

</html>
