<?php
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
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <link rel="manifest" href="../../manifest.php">

    <!-- Incluye la biblioteca CodeMirror CSS desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.css">
    <!-- Incluye el tema blanco (light) para CodeMirror desde CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/eclipse.css">
</head>

<body>
    <?php require_once '../common/admin_header.php'; ?>
    <h1>Create News</h1>
    <form action="./system/new.php" method="post">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" required><br>
        <label for="content">Content (HTML):</label>
        <div style="border: 1px solid #333;">
        <textarea name="content" id="content"><hdr>
  <h1>Título de la Noticia</h1>
  <h2>Subtítulo de la Noticia</h2>

  <p class="author">Autor: <a href="#">Nombre del Autor</a></p>
  <p class="date">Fecha: 01 de enero de 2023</p>

  <img src="https://via.placeholder.com/800x400" alt="Imagen de prueba" />

  <p>
    Contenido de la noticia aquí. Puedes utilizar las etiquetas HTML básicas
    como párrafos, listas, negritas, cursivas, etc.
  </p>

  <p>Puedes añadir más contenido y personalizar según tus necesidades.</p>

  <ul>
    <li>Elemento de lista 1</li>
    <li>Elemento de lista 2</li>
    <li>Elemento de lista 3</li>
  </ul>

  <p>
    Enlaces importantes: <a href="https://www.ejemplo.com">Sitio de Ejemplo</a>,
    <a href="https://www.otro-ejemplo.com">Otro Sitio de Ejemplo</a>
  </p>

  <p>¡Gracias por leer!</p>
</hdr>

<style>
  body {
    font-family: "Arial", sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    color: #333;
    line-height: 1.6;
  }

  hdr {
    color: #fff;
    padding: 10px;
    text-align: center;
  }

  section {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
    background-color: #fff;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
  }

  h1,
  h2 {
    color: #333;
  }

  p {
    font-size: 1.1em;
    line-height: 1.4;
  }

  a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
  }

  .author {
    font-style: italic;
    color: #777;
  }

  .date {
    color: #777;
  }

  img {
    max-width: 100%;
    height: auto;
    margin-bottom: 20px;
  }
</style>
</textarea>
        </div>
        <br>
        <label for="author">Author:</label>
        <input type="text" name="author" id="author"><br>
        
        <label for="tags">Tags (comma-separated):</label>
        <input type="text" name="tags" id="tags"><br>

        <label for="is_featured">Is Featured:</label>
        <input type="checkbox" name="is_featured" id="is_featured"><br>

        <button type="submit">Create News</button>
    </form>

    <!-- Incluye la biblioteca CodeMirror JS desde CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/codemirror.js"></script>
    <!-- Incluye el modo HTML para CodeMirror desde CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/mode/htmlmixed/htmlmixed.js"></script>
    <!-- Incluye el tema blanco (light) para CodeMirror desde CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.63.0/theme/eclipse.js"></script>

    <script>
        var codeEditor = CodeMirror.fromTextArea(document.getElementById("content"), {
            mode: "htmlmixed",  // Modo de código HTML
            theme: "eclipse",   // Tema blanco (light)
        });
    </script>

</body>

</html>
