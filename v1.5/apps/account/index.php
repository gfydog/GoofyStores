<?php
/**
 * Description: This file displays invoices for the logged-in user with pagination.
 * Programmer: Raúl Méndez Rodríguez
 * Company: Goofy Technology Group
 * Website: https://gfy.dog
 */

session_start();

// Check if user is authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: ../authentication/login.php"); // Redirect user to login page if not authenticated
    exit;
}

// Include configuration files and database connection
require_once "../../config/configFinal.php";
require_once "../../config/database.php";
require_once "../../config/config.php";

// Define number of results per page and get current page
$results_per_page = 10;
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;

// Calculate offset for SQL query
$offset = ($current_page - 1) * $results_per_page;

// Get current user ID
$user_id = $_SESSION['user_id'];

// SQL query to retrieve user's invoices with pagination
$sql = "SELECT p.id AS purchase_id, p.total, p.purchase_date, GROUP_CONCAT(pf.quantity, ' x ', f.name, '<br>') AS items, SUM(pf.quantity) AS total_items
        FROM purchases p
        INNER JOIN purchase_files pf ON p.id = pf.purchase_id
        INNER JOIN products f ON pf.file_id = f.id
        WHERE p.user_id = ?
        GROUP BY p.id
        ORDER BY p.purchase_date DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $results_per_page, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Count total rows for pagination
$total_rows_sql = "SELECT COUNT(DISTINCT p.id) AS total_rows
                   FROM purchases p
                   INNER JOIN purchase_files pf ON p.id = pf.purchase_id
                   INNER JOIN products f ON pf.file_id = f.id
                   WHERE p.user_id = ?";
$stmt_total_rows = $conn->prepare($total_rows_sql);
$stmt_total_rows->bind_param("i", $user_id);
$stmt_total_rows->execute();
$result_total_rows = $stmt_total_rows->get_result();
$total_rows = $result_total_rows->fetch_assoc()['total_rows'];

// Calculate total pages
$total_pages = ceil($total_rows / $results_per_page);

// Close connections and free resources
$stmt->close();
$stmt_total_rows->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Invoices</title>
    <link rel="stylesheet" href="../../assets/css/all.css">
    <style>
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .pagination a {
            color: black;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
        }

        .pagination a.active {
            background-color: #00457C;
            color: white;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <?php require_once '../common/header.php'; ?>
    <div class="container">
        <div class="invoice">
            <h2>Invoice</h2>
            <div class="container-table">
                <?php if ($result->num_rows > 0) : ?>
                    <table id="productTable">
                        <tr>
                            <th>Invoice ID</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total Items</th>
                            <th>Total Amount</th>
                        </tr>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?php echo $row['purchase_id']; ?></td>
                                <td><?php echo $row['purchase_date']; ?></td>
                                <td><?php echo $row['items']; ?></td>
                                <td><?php echo $row['total_items']; ?></td>
                                <td><?php echo "$" . number_format($row['total'], 2); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                    <div class="pagination">
                        <?php for ($page = 1; $page <= $total_pages; $page++) : ?>
                            <a href="?page=<?php echo $page; ?>" <?php if ($page == $current_page) echo 'class="active"'; ?>><?php echo $page; ?></a>
                        <?php endfor; ?>
                    </div>
                <?php else : ?>
                    <p>No invoices found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</body>

</html>
