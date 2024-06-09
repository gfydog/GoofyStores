<?php
session_start();

// Verificar si el usuario está autenticado como administrador
if (!isset($_SESSION['admin_id'])) {
    die('Ups!');
}

// Incluir archivos de configuración
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Obtener el ID del proyecto desde la variable GET
$project_id = isset($_GET['project_id']) ? $_GET['project_id'] : null;

// Validar el ID del proyecto
if (!$project_id || !is_numeric($project_id)) {
    echo "Invalid project ID";
    exit;
}

// Definir variables para la paginación
$records_per_page = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$offset = ($page - 1) * $records_per_page;

// Consulta SQL para obtener todos los donadores del proyecto, incluidas las donaciones donde el user_id es NULL
$sql = "SELECT d.user_id, u.username, d.amount, d.id
        FROM donations d 
        LEFT JOIN users u ON d.user_id = u.id 
        WHERE d.project_id = ? 
        ORDER BY d.donation_date DESC 
        LIMIT ?, ?";

// Preparar la consulta
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $project_id, $offset, $records_per_page);
$stmt->execute();
$result = $stmt->get_result();

$donators = [];

if ($result) { 
    while ($row = $result->fetch_assoc()) {
        // Agregar los datos del donador a la lista
        $donators[] = [
            'donation_id' => $row['id'],
            'user_id' => $row['user_id'],
            'username' => htmlspecialchars($row['username'] ?? 'Anonymous', ENT_QUOTES, 'UTF-8'), // Si el usuario es NULL, mostrar "Anonymous"
            'amount' => $row['amount']
        ];
    }
} else {
    // Manejar el caso en que la consulta falló
    echo "Error: " . $conn->error; // Mensaje de error para depuración
}

// Responder con un resultado JSON que contiene los donadores.
echo json_encode($donators);

// Cerrar la conexión con la base de datos.
$stmt->close();
$conn->close();
?>
