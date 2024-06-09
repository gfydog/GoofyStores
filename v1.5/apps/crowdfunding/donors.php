<?php
session_start();

// Verificar si el administrador está autenticado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Incluir archivos de configuración
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Obtener el ID del proyecto desde la variable GET
$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;

// Validar el ID del proyecto
if (!$project_id || !is_numeric($project_id)) {
    echo "Invalid project ID";
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Donators</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <link rel="stylesheet" href="../../assets/css/admin.css">
</head>
<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Donators Management</h1>
    <div id="donators"></div>
    <div id="pagination"></div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            var page = 1;
            var totalPages = 1;

            function fetchDonators(page) {
                $.ajax({
                    url: "./system/get_donors.php",
                    data: { page: page, project_id: <?php echo $project_id; ?> },
                    dataType: "json",
                    success: function(data) {
                        var donatorsHTML = "<table border='1'><tr><th>Username</th><th>Amount</th><th>See more</th></tr>";

                        // Generar enlaces por donador
                        for (var i = 0; i < data.length; i++) {
                            donatorsHTML += "<tr>";
                            donatorsHTML += "<td>" + data[i].username + "</td>";
                            donatorsHTML += "<td>" + data[i].amount + "</td>";
                            donatorsHTML += "<td><a href='details_donation.php?project_id=<?php echo $project_id; ?>&donation_id=" + data[i].donation_id + "'>View Details</a></td>";
                            donatorsHTML += "</tr>";
                        }

                        donatorsHTML += "</table>";
                        $("#donators").html(donatorsHTML);

                        totalPages = Math.ceil(data.length / 10);
                        renderPagination();
                    }
                });
            }

            function renderPagination() {
                var paginationHTML = "<span>Page: </span>";
                for (var i = 1; i <= totalPages; i++) {
                    paginationHTML += "<a href='#' data-page='" + i + "'>" + i + "</a>";
                }
                $("#pagination").html(paginationHTML);
            }

            // Paginación de donadores
            $(document).on("click", "#pagination a", function(e) {
                e.preventDefault();
                var clickedPage = $(this).data("page");
                if (clickedPage !== page) {
                    page = clickedPage;
                    fetchDonators(page);
                }
            });

            fetchDonators(page);
        });
    </script>
</body>
</html>
