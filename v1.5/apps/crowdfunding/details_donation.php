<?php
session_start();

// Verificar si el administrador está autenticado
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../authentication/admin_login.php");
    exit;
}

// Verificar si se proporciona un ID de donación en la URL
if (!isset($_GET['donation_id']) || !is_numeric($_GET['donation_id'])) {
    header("Location: donators.php");
    exit;
}

// Obtener el ID de la donación de la URL
$donation_id = intval($_GET['donation_id']);

// Incluir archivos de configuración y conexión a la base de datos
require "../../config/configFinal.php";
require "../../config/database.php";
require "../../config/config.php";

// Consulta SQL para obtener los detalles de la donación usando parámetros preparados
$sql = "SELECT d.id as donation_id, d.project_id, d.user_id, d.amount, d.donation_date, p.title as project_name, u.username, u.email
        FROM donations d
        INNER JOIN projects p ON d.project_id = p.id
        LEFT JOIN users u ON d.user_id = u.id
        WHERE d.id = ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $donation_id);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró la donación
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $donation_id = $row['donation_id'];
    $project_name = $row['project_name'];
    $user_id = $row['user_id'] == null ? "Anonymous":$row['user_id'];
    $username = $row['username'] ?? "Anonymous";
    $email = $row['email'] ?? "Anonymous";
    $amount = $row['amount'];
    $donation_date = $row['donation_date'];

    // Crear una tabla HTML para mostrar los detalles de la donación
    $donationDetailsHTML = "<h1>Donation Details - Donation ID: $donation_id</h1>";
    $donationDetailsHTML .= "<table class='donation-details'>";
    $donationDetailsHTML .= "<tr><th>Project Name</th><td>$project_name</td></tr>";
    $donationDetailsHTML .= "<tr><th>User ID</th><td>$user_id</td></tr>";
    $donationDetailsHTML .= "<tr><th>Username</th><td>$username</td></tr>";
    $donationDetailsHTML .= "<tr><th>Email</th><td>$email</td></tr>";
    $donationDetailsHTML .= "<tr><th>Amount</th><td>$amount</td></tr>";
    $donationDetailsHTML .= "<tr><th>Donation Date</th><td>$donation_date</td></tr>";
    $donationDetailsHTML .= "</table>";
} else {
    // No se encontraron detalles de la donación
    $donationDetailsHTML = "<p>No details found for Donation ID: $donation_id</p>";
}

// Cerrar la conexión a la base de datos y liberar recursos
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Details</title>
    <link rel="stylesheet" href="../../assets/css/admin.css">
    <style>
        .donation-details {
            width: 90%;
            max-width: 800px;
            border-collapse: collapse;
            margin: 20px auto;
        }
        .donation-details th,
        .donation-details td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .donation-details th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <?php require_once '../common/admin_header.php'; ?>
    <div class="container">
        <?php echo $donationDetailsHTML; ?>
    </div>
</body>
</html>
