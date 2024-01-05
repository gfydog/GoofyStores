<?php
// Start a session
session_start();

// Redirect to installation if the configuration file does not exist
if (!file_exists('../../config/configFinal.php')) {
  header("location: ../../install/index.php");
  exit;
}

// Require configuration and database files
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

require_once "../../config/common.php";

// Get user ID from the session, if available
if (isset($_SESSION['user_id'])) {
  $user_id = $_SESSION['user_id'];
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <!-- Page title -->
  <title><?= htmlspecialchars(TITLE) ?></title>

  <meta charset="UTF-8">
  <!-- Set cache control headers -->
  <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
  <meta http-equiv="Pragma" content="no-cache">
  <meta http-equiv="Expires" content="0">

  <!-- Include common head content -->
  <?php require_once '../common/head.php'; ?>

  <!-- Include jQuery library -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

  <!-- Link to web app manifest -->
  <link rel="manifest" href="../../manifest.php">
  <link rel="stylesheet" href="../../assets/css/all.css">

  <!-- Additional Styles for nytimes.com-like design -->
  <style>
    .bxs {
      width: 100%;
      display: inline-flex;
      flex-direction: row;
      flex-wrap: wrap;

    }

    .bxNews {
      width: 48%;
      margin: 50px auto;
      box-shadow: 0 0 5px rgba(0, 0, 0, 0.3);
      transition: transform 0.3s ease-in-out;
      display: flex;
      flex-direction: row;
    }

    .bxNews:hover {
      transform: scale(1.03);
    }

    .bxNews h2 {
      font-size: 1.5rem;
      margin-bottom: 10px;
      text-align: left;
    }

    .bxNews p {
      margin: 0;
      color: #777;
    }

    .bxNews img {
      width: 130px;
      height: 130px;
      /* Establece la altura igual a la anchura */
      object-fit: cover;
      /* Mantiene la proporción y cubre el contenedor */
    }

    .bxNews .text-container {
      margin: 15px;
      flex: 1;
      /* Ocupa el espacio restante disponible */
    }

    @media (max-width: 640px) {
    .bxs {
      flex-direction: column; /* Cambia a disposición de columna */
    }

    .bxNews {
      width: 80%;
      margin: 15px auto;
      flex-direction: column;
    }

    .bxNews img {
      width: 100%; /* Ocupa el 100% del ancho del contenedor */
      height: auto;
      max-height: 300px;
      margin-right: 0; /* No hay margen derecho en pantallas más pequeñas */
      margin-bottom: 10px; /* Añade un margen inferior para separar la imagen del texto */
    }
  }

    #pagination {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 10px;
    }

    .div-link {
      text-align: center;
      margin-top: 20px;
    }

    .pass {
      color: #333;
      text-decoration: none;
      background-color: #fff;
      padding: 10px 15px;
      border-radius: 5px;
    }
  </style>
</head>

<body>
  <!-- Include common header -->
  <?php require_once '../common/header.php'; ?>

  <!-- Search bar -->
  <div class="search-bar">
    <form id="search-form" class="search-form">
      <input type="text" id="search" placeholder="Search...">
      <button id="search-button">Search</button>
    </form>
  </div>

  <!-- News listings section -->
  <section id="news" class="bxs"></section>
  <div id="pagination"></div>

  <!-- Admin site link -->
  <div class="div-link">
    <a href="../authentication/admin_login.php" class="pass">Admin Site</a>
  </div>

  <script>
    function getDate(timestamp) {
      // Array con los nombres de los meses en español
      const meses = [
        'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
        'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
      ];

      // Crear un objeto de fecha a partir del timestamp
      const fecha = new Date(timestamp);

      // Obtener el día, mes y año
      const dia = fecha.getDate();
      const numeroMes = fecha.getMonth() + 1; // Los meses en JavaScript van de 0 a 11
      const ano = fecha.getFullYear();

      // Construir la cadena de fecha traducida
      const fechaTraducida = `${dia} de ${meses[numeroMes - 1]}, ${ano}`;

      return fechaTraducida;
    }

    // Function to load news based on filters
    function loadNews(search = '', page = 1) {
      $.ajax({
        url: "./system/get_news.php",
        data: {
          search: search,
          page: page
        },
        dataType: "json",
        success: function(data) {
          console.log(data);
          var newsHTML = "";
          for (var i = 0; i < data.news.length; i++) {
            newsHTML += '<a href="news_article.php?id=' + data.news[i].id + '" class="bxNews">';
            if (data.news[i].image != "") {
              newsHTML += "<img src='" + data.news[i].image + "'>";
            }
            newsHTML += "<div class='text-container'>";

            newsHTML += "<h2>" + data.news[i].title + "</h2>";
            newsHTML += "<p>" + data.news[i].author + "</p>";
            newsHTML += "<p>" + getDate(data.news[i].publication_date) + "</p>";
            newsHTML += "</div>"
            newsHTML += '</a>';
          }
          $("#news").html(newsHTML);

          <?php if (isset($user_id)) { ?>
            // Generate pagination buttons
            var paginationHTML = "";
            for (var i = 1; i <= data.total_pages; i++) {
              paginationHTML += "<button onclick=\"loadNews('" + search + "'," + i + ")\">" + i + "</button>";
            }
            $("#pagination").html(paginationHTML);
          <?php } ?>
        },
        error: function(data) {
          console.log(data.responseText);
        }
      });
    }

    $(document).ready(function() {
      // Load news on page load
      loadNews();

      // Handle search form submission
      $("#search-form").on("submit", function(e) {
        e.preventDefault();
        var search = $("#search").val();
        loadNews(search);
      });

    });
  </script>

  <!-- Include common customBox script -->
  <?php require_once '../common/customBox.php'; ?>
</body>

</html>