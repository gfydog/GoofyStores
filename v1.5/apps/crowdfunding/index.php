<?php
// Start a session
session_start();

// Redirect to installation if the configuration file does not exist
if (!file_exists('../../config/configFinal.php')) {
  header("location: ../../install/index.php");
  exit;
}

require_once "../../vendor/autoload.php";
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Comprobación si el usuario está autenticado
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Obtener información de los proyectos
$sql = "SELECT projects.*, 
               (SELECT COUNT(*) FROM donations WHERE project_id = projects.id) AS contributors_count, 
               (SELECT COALESCE(SUM(amount), 0) FROM donations WHERE project_id = projects.id) AS total_raised,
               GROUP_CONCAT(project_images.image) AS images
        FROM projects 
        LEFT JOIN project_images ON projects.id = project_images.project_id
        WHERE end_date >= CURDATE()
        GROUP BY projects.id";
$result = $conn->query($sql);

if (!$result) {
    echo "Error en la consulta: " . $conn->error;
    exit;
}

$projects = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $row['images'] = explode(',', $row['images']); // Convertir la cadena de imágenes en un array
        $projects[] = $row;
    }
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crowdfunding</title>

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../assets/css/all.css">
    <link rel="stylesheet" href="./assets/css/c.css">

</head>

<body>

    <?php require_once '../common/header.php'; ?>
    <div class="campaigns">
        <?php foreach ($projects as $index => $project) : ?>
            <div class="campaign">
                <div id="carouselExampleIndicators<?php echo $index; ?>" class="carousel slide" data-ride="carousel">
                    <ol class="carousel-indicators">
                        <?php foreach ($project['images'] as $key => $image) : ?>
                            <li data-target="#carouselExampleIndicators<?php echo $index; ?>" data-slide-to="<?php echo $key; ?>" <?php echo $key === 0 ? 'class="active"' : ''; ?>></li>
                        <?php endforeach; ?>
                    </ol>
                    <div class="carousel-inner">
                        <?php foreach ($project['images'] as $key => $image) : ?>
                            <div class="carousel-item <?php echo $key === 0 ? 'active' : ''; ?>">
                                <img class="d-block w-100" src="../../project_images/<?php echo $image; ?>" alt="<?php echo $project['title']; ?>">
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators<?php echo $index; ?>" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators<?php echo $index; ?>" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>
                <a href="./details.php?id=<?php echo $project['id']; ?>">
                    <h2><?php echo $project['title']; ?></h2>
                    <?php
                    // Current date
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
                            $remainingText[] = $interval->m . " month" . ($interval->m > 1 ? "s" : "");
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
                    ?>

                    <?php if ($remainingText == "Finished") {
                        echo "<p style='text-align: center;'>Finished</p>";
                    } else { ?>
                        <p>Contributors: <?php echo $project['contributors_count']; ?></p>
                        <p><?php echo "$" . $project['total_raised'] . " of $" . $project['goal']; ?></p>
                        <div class="gfy-progress-bar">
                            <div class="gfy-progress" style="width: <?php echo ($project['total_raised'] / $project['goal']) * 100; ?>%"></div>
                        </div>
                        <p><?php echo $remainingText . " left";
                            ?>
                        </p>
                </a>
                <form action="donation.php" method="post">
                    <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                    <div class="donation-buttons">
                        <button type="submit" name="amount" value="5" class="donation-button">$5</button>
                        <button type="submit" name="amount" value="10" class="donation-button">$10</button>
                        <button type="submit" name="amount" value="20" class="donation-button">$20</button>
                    </div>
                </form>
            <?php } ?>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Admin site link -->
    <div class="div-link">
        <a href="../authentication/admin_login.php" class="pass">Admin Site</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>