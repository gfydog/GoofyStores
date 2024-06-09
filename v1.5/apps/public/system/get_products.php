<?php
/**
 * This PHP script fetches and returns products with images based on search, filter, order, and pagination.
 */

header("Content-Type: application/json");

require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Get query parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? intval($_GET['filter']) : '';
$order = isset($_GET['order']) ? $_GET['order'] : '';
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build the SQL query for product retrieval
$sql = "SELECT p.id, p.name, p.description, p.price, p.created_at, type FROM products p
        WHERE  (p.name LIKE ? OR p.description LIKE ?)";

if ($filter > 0) {
    $sql .= " AND p.category_id = ?";
}

// Order by
switch ($order) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'name_asc':
        $sql .= " ORDER BY p.name ASC";
        break;
    case 'name_desc':
        $sql .= " ORDER BY p.name DESC";
        break;
    default:
        $sql .= " ORDER BY p.created_at DESC";
}

$sql .= " LIMIT ? OFFSET ?";

// Prepare and execute the SQL query
$stmt = $conn->prepare($sql);
$param = "%" . $search . "%";

if ($filter > 0) {
    $stmt->bind_param("ssiii", $param, $param, $filter, $limit, $offset);
} else {
    $stmt->bind_param("ssii", $param, $param, $limit, $offset);
}

$stmt->execute();
$result = $stmt->get_result();

$products = array();
while ($row = $result->fetch_assoc()) {
    // Query the database to select product images
    $sqlImages = "SELECT * FROM product_images where product_id='" . $row['id'] . "'";
    $result2 = $conn->query($sqlImages);
    $images = $result2->fetch_all(MYSQLI_ASSOC);

    // Add product information to the response array
    $products[] = [
        'id' => $row['id'],
        'name' => htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'),
        'description' => htmlspecialchars($row['description'], ENT_QUOTES, 'UTF-8'),
        'price' => $row['price'],
        'images' => $images,
        'type' => $row['type']
    ];
}

// Get total pages for pagination
$sql_total = "SELECT COUNT(*) as total FROM products p
              WHERE  (p.name LIKE ? OR p.description LIKE ?)";

if ($filter > 0) {
    $sql_total .= " AND p.category_id = ?";
}

if ($filter > 0) {
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("ssi", $param, $param, $filter);
} else {
    $stmt_total = $conn->prepare($sql_total);
    $stmt_total->bind_param("ss", $param, $param);
}

$stmt_total->execute();
$result_total = $stmt_total->get_result();
$row_total = $result_total->fetch_assoc();
$total_pages = ceil($row_total['total'] / $limit);

// Prepare the response array
$response = [
    'products' => $products,
    'total_pages' => $total_pages
];

// Encode the response array as JSON and echo it
echo json_encode($response);

// Close the database connection
$conn->close();
?>
