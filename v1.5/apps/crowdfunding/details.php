<?php
// Start a session
session_start();

// Redirect to installation if the configuration file does not exist
if (!file_exists('../../config/configFinal.php')) {
  header("location: ../../install/index.php");
  exit;
}

// Incluir archivos de configuración y base de datos necesarios
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Obtener el ID del proyecto de la URL
if (isset($_GET['id'])) {
    $project_id = intval($_GET['id']);

    // Consulta para obtener los detalles del proyecto
    $sql = "SELECT projects.*, 
               (SELECT COUNT(*) FROM donations WHERE project_id = projects.id) AS contributors_count, 
               (SELECT COALESCE(SUM(amount), 0) FROM donations WHERE project_id = projects.id) AS total_raised,
               GROUP_CONCAT(project_images.image) AS images
        FROM projects 
        LEFT JOIN project_images ON projects.id = project_images.project_id
        WHERE projects.id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $project_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró el proyecto
    if ($result->num_rows > 0) {
        $project = $result->fetch_assoc();
    } else {
        header("location: index.php");
        exit;
    }
} else {
    header("location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <!-- Título de la página -->
    <title><?= htmlspecialchars($project['title']) ?></title>

    <meta charset="UTF-8">
    <!-- Configurar cabeceras de control de caché -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">

    <meta name="description" content="<?= htmlspecialchars($project['description']) ?>">
    <meta name="keywords" content="Crowdfunding, proyecto, contribución">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <!-- Google -->
    <meta name="google" content="nositelinkssearchbox" />
    <meta name="google" content="notranslate" />

    <!-- Facebook -->
    <meta property="og:url" content="<?= ROOT . "apps/crowdfunding/details.php?id=" . $project['id'] ?>" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="<?= htmlspecialchars($project['title']) ?>" />
    <meta property="og:description" content="<?= htmlspecialchars($project['description']) ?>" />
    <!-- Usar la primera imagen del proyecto como imagen en miniatura en Facebook -->
    <meta property="og:image" content="<?php
                                        if (isset($project['images'])) {
                                            $project_images = explode(',', $project['images']);
                                            echo SHORTROOT . $project_images[0];
                                        } else {
                                            echo SHORTROOT . "assets/images/logo.png";
                                        }
                                        ?>" />

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:site" content="@crowdfunding">
    <meta name="twitter:title" content="<?= htmlspecialchars($project['title']) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($project['description']) ?>">
    <!-- Usar la primera imagen del proyecto como imagen en miniatura en Twitter -->
    <meta name="twitter:image" content="<?php
                                        if (isset($project['images'])) {
                                            $project_images = explode(',', $project['images']);
                                            echo SHORTROOT . $project_images[0];
                                        } else {
                                            echo SHORTROOT . "assets/images/logo.png";
                                        }
                                        ?>" />

    <!-- Incluir favicon -->
    <link rel="icon" type="image/png" href="<?php
                                            if (!empty($project['icon'])) {
                                                echo SHORTROOT . "assets/images/" . $project['icon'];
                                            } else {
                                                echo SHORTROOT . "assets/images/logo.png";
                                            }
                                            ?>" />

    <!-- Incluir la librería jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="./assets/js/app.js"></script>
    <!-- Estilos CSS -->
    <link rel="stylesheet" href="../../assets/css/all.css">
    <link rel="stylesheet" href="./assets/css/c.css">
</head>

<body>
    <!-- Incluir el encabezado común -->
    <?php require_once '../common/header.php'; ?>

    <section class="product-section">
        <?php
        // Mostrar la información del proyecto
        echo "<div class='product-info'>"; ?>


        <h1 class="hh1"><?php echo $project['title']; ?></h1>
        <p class='product-description'><?php echo $project['description']; ?></p>
        <?php
        $currentDate = new DateTime();
        // Project end date
        $endDate = new DateTime($project['end_date']);
        // Calculate the difference between the dates
        $interval = $currentDate->diff($endDate);

        // Build the text indicating how much time is left until the project ends
        $fallbackText = "Finished";
        if ($currentDate < $endDate) {
            $remainingText = [];
            if ($interval->m > 0) {
                $remainingText[] = $interval->m. " month" . ($interval->m > 1 ? "s" : "");
            }
            if ($interval->d > 0) {
                $remainingText[] = $interval->d . " day" . ($interval->d > 1 ? "s" : "");
            }
            if ($interval->h > 0) {
                $remainingText[] = $interval->h . " hour" . ($interval->h > 1 ? "s" : "");
            }
            if ($interval->i > 0) {
                $remainingText[] = $interval->i . " minute" . ($interval->i > 1 ? "s" : "");
            }
            $remainingText = implode(", ", $remainingText);
        } else {
            $remainingText = $fallbackText;
        }

        if ($remainingText == "Finished") {
            echo "<p>Finished</p>";
        } else { ?>
            <div class='p-type'>Contributors: <?php echo $project['contributors_count']; ?></div>
            <p class='product-price'><?php echo "$" . $project['total_raised'] . " of $" . $project['goal']; ?></p>
            <div class="gfy-progress-bar">
                <div class="gfy-progress" style="width: <?php echo ($project['total_raised'] / $project['goal']) * 100; ?>%"></div>
            </div>
            <p>
            <?php echo "<div class='p-type'>" . $remainingText . " left</div>";
            ?>
            <form action="donation.php" method="post">
                <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                <div class="donation-buttons">
                    <button type="submit" name="amount" value="5" class="donation-button">$5</button>
                    <button type="submit" name="amount" value="10" class="donation-button">$10</button>
                    <button type="submit" name="amount" value="20" class="donation-button">$20</button>
                </div>
            </form>
            </p>
            
        <?php } ?>
        </div>
        <?php
        // Mostrar imágenes del proyecto
        echo "<div class='product-images'>";
        echo "<div class='image-gallery'>";

        // Mostrar la imagen principal del proyecto
        if (isset($project['images'])) {
            $project_images = explode(',', $project['images']);
            echo "<img src='../../project_images/" . $project_images[0] . "' alt='" . htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') . "' id='mainImage'>";
        }

        // Mostrar todas las imágenes como miniaturas
        echo "<div class='thumbnail-container'>";
        foreach ($project_images as $image) {
            echo "<img src='../../project_images/" . $image . "' alt='" . htmlspecialchars($project['title'], ENT_QUOTES, 'UTF-8') . "' class='thumbnail'>";
        }
        echo "</div>";  // end thumbnail-container

        echo "</div>";  // end image-gallery
        echo "</div>";  // end project-images
        ?>
    </section>

</body>

</html>