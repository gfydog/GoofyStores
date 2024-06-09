<?php

/**
 * Este script PHP maneja la adición de nuevos proyectos a través de una interfaz de administrador.
 * Verifica si un administrador ha iniciado sesión, permite cargar imágenes de proyecto,
 * y proporciona campos de formulario para ingresar los detalles del proyecto.
 *
 * Versión de PHP 7
 *
 * @category Subida_de_Proyectos
 * @package  Interfaz_de_Administrador
 */

// Iniciar una sesión PHP.
session_start();

// Verificar si un administrador ha iniciado sesión, redirigir a la página de inicio de sesión de administrador si no.
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../../authentication/admin_login.php");
    exit;
}

// Incluir archivos de configuración y establecer una conexión a la base de datos.
require_once "../../../config/configFinal.php";
require_once "../../../config/database.php";
require_once "../../../config/config.php";

// Definir el directorio de destino para las subidas de archivos.
$target_dir = "../../../project_images/";

// Crear el directorio de destino si no existe.
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0755, true);
}

// Obtener la información del proyecto desde los datos POST.
$title = htmlspecialchars($_POST['title']);
$description = $_POST['description'];
$goal = floatval($_POST['goal']);
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Verificar si se proporcionan imágenes de proyecto.
if (isset($_FILES['project_images'])) {
    $project_images = $_FILES['project_images'];
    $image_count = count($project_images['name']);
    $uploaded_images = [];

    // Procesar y validar las imágenes de proyecto cargadas.
    for ($i = 0; $i < $image_count; $i++) {
        $image_name = basename($project_images['name'][$i]);
        $image_type = pathinfo($image_name, PATHINFO_EXTENSION);
        $image_size = $project_images['size'][$i];

        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_type, $allowed_extensions)) {
            die("Tipo de archivo no permitido. Solo se permiten imágenes JPG, JPEG, PNG y GIF.");
        }

        if ($image_size > 10000000) {
            die("El tamaño del archivo de imagen es demasiado grande. El tamaño máximo permitido es de 10 MB.");
        }

        $path_db = uniqid() . "_" . $image_name;
        $upload_path = $target_dir . $path_db;

        if (move_uploaded_file($project_images['tmp_name'][$i], $upload_path)) {
            $uploaded_images[] = $path_db;
        } else {
            die("Error al cargar la imagen del proyecto.");
        }
    }
} else {
    die("No se proporcionaron imágenes de proyecto.");
}

// Insertar detalles del proyecto en la base de datos.
$sql = "INSERT INTO projects (title, description, goal, start_date, end_date) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssdss", $title, $description, $goal, $start_date, $end_date);

if ($stmt->execute()) {
    $project_id = $conn->insert_id;

    // Insertar las imágenes cargadas en la tabla project_images.
    $sql_image = "INSERT INTO project_images (project_id, image) VALUES (?, ?)";
    $stmt_image = $conn->prepare($sql_image);
    $stmt_image->bind_param("is", $project_id, $image);

    foreach ($uploaded_images as $image) {
        $stmt_image->bind_param("is", $project_id, $image);
        if (!$stmt_image->execute()) {
            echo "Error: " . $sql_image . "<br>" . $conn->error;
        }
    }

    header("Location: ../admin.php?success");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

// Cerrar la conexión a la base de datos.
$conn->close();
?>
