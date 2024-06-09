<?php

/**
 * PHP script for editing project details in the admin interface.
 *
 * This script is responsible for editing project information, such as title, description, goal, start date, and end date, in the admin interface. It retrieves the project details from the database and allows the admin to update the information.
 *
 * PHP version 7
 *
 * @category Admin_Project_Editing
 * @package  Admin_Interface
 */

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

// Retrieve project details based on the provided project ID.
if (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);
    $sql = "SELECT * FROM projects WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $title = $row['title'];
        $description = $row['description'];
        $goal = $row['goal'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
    } else {
        die("The project does not exist.");
    }
} else {
    die("Project ID was not provided.");
}
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

<body class="admin-background">
    <?php require_once '../common/admin_header.php'; ?>
    <div class="admin-container">
        <h1>Edit Project</h1>
        <input type="hidden" id="project_id" value="<?php echo $project_id; ?>">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" value="<?php echo $title; ?>" required>
        </div>
        <div class="form-group">
            <label for="description">Description:</label>
            <textarea name="description" id="description" required><?php echo $description; ?></textarea>
        </div>
        <div class="form-group">
            <label for="goal">Goal:</label>
            <input type="number" step="0.01" name="goal" id="goal" value="<?php echo $goal; ?>" required>
        </div>
        <div class="form-group">
            <label for="start_date">Start Date:</label>
            <input type="date" name="start_date" id="start_date" value="<?php echo $start_date; ?>" required>
        </div>
        <div class="form-group">
            <label for="end_date">End Date:</label>
            <input type="date" name="end_date" id="end_date" value="<?php echo $end_date; ?>" required>
        </div>
        <div class="form-group">
            <button onclick="updateProject();" class="admin-btn">Update Project</button>
        </div>
    </div>
    <div class="admin-container">
        <h2>Project Images</h2>
        <form action="./system/add_images.php" method="POST" enctype="multipart/form-data" class="image-form">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <div class="form-group">
                <input type="file" name="project_images[]" id="project_images" multiple accept="image/*">
            </div>
            <div class="form-group">
                <input type="submit" value="Add Images" class="admin-btn">
            </div>
        </form>
        <div id="images"></div>
    </div>
    <script>
        function updateProject() {
            let project_id = $('#project_id').val();
            let title = $('#title').val();
            let description = $('#description').val();
            let goal = $('#goal').val();
            let start_date = $('#start_date').val();
            let end_date = $('#end_date').val();

            let data = {
                project_id: project_id,
                title: title,
                description: description,
                goal: goal,
                start_date: start_date,
                end_date: end_date
            };

            $.ajax({
                type: "POST",
                url: "./system/update_project.php",
                data: data,
                dataType: "json",
                success: function(response) {
                    console.log(response);
                    if (response.success) {
                        showAlert("Proyecto actualizado correctamente.");
                    } else {
                        showAlert("Error al actualizar el proyecto.");
                    }
                },
            });
        }

        function deleteImage(project_id) {
            if (confirm("¿Está seguro de que desea eliminar esta imagen?")) {
                window.location.href = "./system/delete_image.php?id=" + project_id;
            }
        }

        function showImages() {
            let project_id = <?php echo $project_id; ?>;
            $.ajax({
                type: "GET",
                url: "../public/system/get_images_project.php",
                data: {
                    id: project_id
                },
                dataType: "json",
                success: function(response) {
                    let imagesHTML = "";
                    for (let i = 0; i < response.length; i++) {
                        imagesHTML += "<img src='../../" + response[i].image + "' style='width: 100px;'>";
                        imagesHTML += " <button onclick='deleteImage(" + response[i].id + ");'>Eliminar</button><br>";
                    }
                    $("#images").html(imagesHTML);
                }
            });
        }

        $(document).ready(function() {
            showImages();
        });
    </script>
    <?php require_once '../common/customBox.php'; ?>
</body>

</html>