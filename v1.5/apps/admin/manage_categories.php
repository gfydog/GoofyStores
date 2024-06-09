<?php
/**
 * Description: This file allows administrators to manage categories.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

// Start a PHP session to check if the admin is logged in.
session_start();

// If the admin is not logged in, redirect to the admin login page.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Include necessary configuration and database files.
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="manifest" href="../../manifest.php">
</head>
<body>
    <?php require_once '../common/admin_header.php'; ?>
    <div class="admin-container">
        <h1>Manage Categories</h1>
        <!-- Form to add a new category -->
        <form id="add-category-form">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required>
            <button type="submit">Add Category</button>
        </form>
        <br>
        <div id="categories"></div>
    </div>
    <script>
        // Function to load and display categories
        function loadCategories() {
            $.ajax({
                url: "../public/system/get_categories.php",
                dataType: "json",
                success: function(data) {
                    let categoriesHTML = "";
                    for (let i = 0; i < data.length; i++) {
                        categoriesHTML += "<div>";
                        categoriesHTML += "<span>" + data[i].name + "</span>";
                        categoriesHTML += " <button onclick='deleteCategory(" + data[i].id + ");'>Delete</button>";
                        categoriesHTML += "</div>";
                    }
                    $("#categories").html(categoriesHTML);
                }
            });
        }

        // Function to delete a category
        function deleteCategory(categoryId) {
            $.ajax({
                url: "./system/delete_category.php",
                method: "POST",
                data: {
                    id: categoryId
                },
                dataType: "json",
                success: function(data) {
                    if (data.success) {
                        loadCategories();
                    } else {
                        alert("Error");
                    }
                }
            });
        }

        // Handle form submission to add a new category
        $("#add-category-form").on("submit", function(e) {
            e.preventDefault();
            let categoryName = $("#category_name").val();

            $.ajax({
                url: "./system/add_category.php",
                method: "POST",
                data: {
                    name: categoryName
                },
                dataType: "json",
                success: function(data) {
                    if (data.success) {
                        loadCategories();
                        $("#category_name").val('');
                    } else {
                        alert("Error");
                    }
                }
            });
        });

        // Load categories on page load
        $(document).ready(function() {
            loadCategories();
        });
    </script>
</body>
</html>
