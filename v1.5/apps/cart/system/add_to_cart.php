<?php
// Start a PHP session.
session_start();

// Include necessary configuration files.
require "../../../config/common.php";
require "../../../config/configFinal.php";
require "../../../config/database.php";
require "../../../config/config.php";

// Verificar si el HTTP request method es POST.
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener el ID de usuario.
    $user_id = getUserID();
    
    // Obtener los datos del producto del formulario POST.
    $product_id = intval($_POST['product_id']);
    $quantity = 1;

    // Consulta SQL para verificar si el producto está en stock.
    $stock_sql = "SELECT stock_quantity, type FROM products WHERE id = ?";
    $stock_stmt = $conn->prepare($stock_sql);
    $stock_stmt->bind_param("i", $product_id);
    $stock_stmt->execute();
    $stock_result = $stock_stmt->get_result();

    if ($stock_result->num_rows > 0) {
        $stock_row = $stock_result->fetch_assoc();
        $stock_quantity = $stock_row['stock_quantity'];

        // Si el producto está en stock o es un producto digital, proceder a agregarlo al carrito.
        if ($stock_quantity > 0 || $stock_row['type'] == 2) {
            // Consulta SQL para verificar si el producto ya está en el carrito del usuario.
            $cart_sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
            $cart_stmt = $conn->prepare($cart_sql);
            $cart_stmt->bind_param("ii", $user_id, $product_id);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();

            if ($cart_result->num_rows > 0) {
                // Si el producto ya está en el carrito, actualizar la cantidad.
                $cart_row = $cart_result->fetch_assoc();
                $new_quantity = $cart_row['quantity'] + 1;
                
                // Verificar si hay suficiente stock disponible.
                if ($stock_quantity >= $new_quantity && $stock_row['type'] == 1) {
                    // Actualizar la cantidad en el carrito.
                    $update_sql = "UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?";
                    $update_stmt = $conn->prepare($update_sql);
                    $update_stmt->bind_param("iii", $new_quantity, $user_id, $product_id);
                    $update_stmt->execute();
                    echo "Success! Added to the cart";
                } else {
                    echo "Oops! This product is out of stock.";
                }
            } else {
                // Si el producto no está en el carrito, insertarlo.
                $insert_sql = "INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)";
                $insert_stmt = $conn->prepare($insert_sql);
                $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
                $insert_stmt->execute();
                echo "Success! Added to the cart";
            }
        } else {
            // Si el producto está fuera de stock, responder con un mensaje de error.
            echo "This product is out of stock.";
        }
    } else {
        // Si el producto no existe, responder con un mensaje de error.
        echo "Product not found.";
    }
} else {
    // Si el método de solicitud HTTP no es POST, responder con un mensaje de error.
    echo "Method not allowed";
}

// Cerrar la conexión a la base de datos.
$conn->close();
?>
