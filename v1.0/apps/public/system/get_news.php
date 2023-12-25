<?php
header("Content-Type: application/json");

require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Get query parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build the SQL query for news retrieval
$sql = "SELECT id, title, author, status, publication_date FROM news
        WHERE (title LIKE ? OR content LIKE ?)";

$sql .= " ORDER BY publication_date DESC";
$sql .= " LIMIT ? OFFSET ?";

// Prepare and execute the SQL query
$stmt = $conn->prepare($sql);
$param = "%" . $search . "%";

$stmt->bind_param("ssii", $param, $param, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

$news = array();
while ($row = $result->fetch_assoc()) {
    // Add news information to the response array
    $news[] = [
        'id' => $row['id'],
        'title' => htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8'),
        'author' => htmlspecialchars($row['author'], ENT_QUOTES, 'UTF-8'),
        'status' => $row['status'],
        'publication_date' => $row['publication_date']
    ];
}

// Get total pages for pagination
$sql_total = "SELECT COUNT(*) as total FROM news
              WHERE (title LIKE ? OR content LIKE ?)";

$stmt_total = $conn->prepare($sql_total);
$stmt_total->bind_param("ss", $param, $param);
$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_assoc();
$total_pages = ceil($row_total['total'] / $limit);

// Prepare the response array
$response = [
    'news' => $news,
    'total_pages' => $total_pages
];

// Encode the response array as JSON and echo it
echo json_encode($response);

// Close the database connection
$conn->close();
?>
