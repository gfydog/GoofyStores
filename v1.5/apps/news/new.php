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
</head>

<body>
  <?php require_once '../common/admin_header.php'; ?>
  <h1>Create News</h1>
  <form action="./system/new.php" method="post">
    <label for="title">Title:</label>
    <input type="text" name="title" id="title" required><br>

    <label for="content">Url Image:</label>
    <input type="text" name="image" id="image" required><br>

    <label for="description">Short Description:</label>
    <input type="text" name="description" id="description"><br>

    <label for="content">Content (HTML):</label>
    <div>

      <textarea name="content" id="content" class="article-textarea">

  <section class="article-section">

    <div class='article-info'>

      <div class='article-type'>Hola mundo!</div>

      <h1>Título</h1>

      <p class='article-description'>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>
      
      <p class='article-price'>Adelante!</p>

      <button class='article-button' onclick="alert('Hi!')">JS</button>

    </div>

  </section>


  <style>
    .article-section {
      background-color: #f0f0f0;
      padding: 20px 0px;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      align-items: center;
      max-width: 100%;
    }

    .article-info {
      margin: 5px auto;
      color: #666;
      max-width: 800px;
      width: 90%;
    }

    .article-type{
      font-weight: bold;
      font-size: 12px;
      color: #999;
      text-align: center;
    }

    .article-section h1 {
      font-family: "Segoe UI", Tahoma, sans-serif;
      color: #0079C1;
      margin-bottom: 15px;
      text-align: center;
    }

    .article-description {
      font-family: "Segoe UI", Tahoma, sans-serif;
      font-size: 1.1em;
      color: #666;
      margin-bottom: 15px;
    }

    .article-price {
      font-family: "Segoe UI", Tahoma, sans-serif;
      color: #666;
      font-weight: bold;
      font-size: 1.3em;
      margin-bottom: 15px;
      font-weight: bold;
    }

    .article-button {
      padding: 15px 40px;
      background-color: #00457C;
      color: #fff;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-family: "Segoe UI", Tahoma, sans-serif;
      transition: background-color 0.3s;
      margin-bottom: 20px;
      font-size: 15px;
    }

    .retro-button:hover {
      background-color: #0079C1;
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


</body>

</html>