<?php
/**
 * Función para generar un token único para la descarga de un archivo.
 * @param int $file_id El ID del archivo asociado al token.
 * @return string El token generado.
 */
function generateDownloadToken($file_id) {
    // Generar un token único utilizando una función segura para generar tokens.
    $token = bin2hex(random_bytes(32));

    // Insertar el token en la tabla download_tokens.
    global $conn;
    $insert_sql = "INSERT INTO download_tokens (token, file_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("si", $token, $file_id);
    $insert_stmt->execute();

    // Devolver el token generado.
    return $token;
}

/**
 * Función para generar un token único para la descarga de un archivo.
 * @param int $file_id El ID del archivo asociado al token.
 * @return string|bool El token generado si se crea correctamente, de lo contrario false.
 */
function getDownloadToken($file_id) {
    // Generar un token único utilizando una función segura para generar tokens.
    $token = bin2hex(random_bytes(32));

    // Insertar el token en la tabla download_tokens.
    global $conn;
    $insert_sql = "INSERT INTO download_tokens (token, file_id) VALUES (?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("si", $token, $file_id);
    $insert_stmt->execute();

    // Verificar si se insertó correctamente y devolver el token generado.
    if ($insert_stmt->affected_rows > 0) {
        return $token;
    } else {
        return false;
    }
}

?>