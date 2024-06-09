<?php
// Start a PHP session.
session_start();

// Check if an admin is logged in, redirect to admin login page if not.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Definir el número de noticias por página
$newsPerPage = 10;

// Obtener el número total de noticias
$sqlCount = "SELECT COUNT(id) as total FROM news";
$resultCount = $conn->query($sqlCount);
$totalNews = $resultCount->fetch_assoc()['total'];

// Calcular el número total de páginas
$totalPages = ceil($totalNews / $newsPerPage);

// Obtener el número de la página actual desde $_GET
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Calcular el índice de inicio para la consulta
$offset = ($page - 1) * $newsPerPage;

// SQL query para obtener las noticias de la página actual
$sql = "SELECT * FROM news ORDER BY publication_date DESC LIMIT $offset, $newsPerPage";
$result = $conn->query($sql);

$news = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin News</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>

<body>

    <?php require_once '../common/admin_header.php'; ?>
    <h1>Administer News</h1>
    <div id="a-container">
        <a href="new.php">Add News</a>
    </div>
    <div class="container-table">
        <table border="1">
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Author</th>
                <th>Status</th>
                <th colspan="2">Actions</th>
            </tr>
            <?php foreach ($news as $new) : ?>
                <tr>
                    <td><?php echo $new['id']; ?></td>
                    <td><?php echo $new['title']; ?></td>
                    <td><?php echo $new['author']; ?></td>
                    <td><?php echo $new['status']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $new['id']; ?>"><button>Edit</button></a>
                    </td>
                    <td>
                        <button onclick="showConfirm('Are you sure you want to delete this news?', () => { window.location.href = '<?= "./system/delete.php?id=" . $new['id'] ?>';});">Delete</button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Agregar enlaces de paginación -->
        <div class="pagination">
            <?php if ($page > 1) : ?>
                <a href="?page=<?php echo $page - 1; ?>" class="pagination-button">&laquo; Anterior</a>
            <?php endif; ?>

            <a href="?page=<?php echo $page; ?>" class="active pagination-button"><?php echo $page; ?></a>

            <?php if ($page < $totalPages) : ?>
                <a href="?page=<?php echo $page + 1; ?>" class="pagination-button">Siguiente &raquo;</a>
            <?php endif; ?>
        </div>
        <style>
            .pagination {
                text-align: center;
                margin-top: 20px;
                margin-bottom: 20px;
            }

            .pagination-button {
                display: inline-block;
                padding: 8px 12px;
                margin: 0 4px;
                border: 1px solid #3498db;
                background-color: #3498db;
                color: #fff;
                text-decoration: none;
                border-radius: 4px;
            }

            .pagination-button:hover {
                background-color: #2980b9;
            }

            .active {
                background-color: #2980b9;
                color: #fff;
                cursor: default;
            }
        </style>
    </div>

    <?php require_once '../common/customBox.php'; ?>

</body>

</html>