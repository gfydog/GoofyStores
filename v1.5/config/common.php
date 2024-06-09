<?php

/**
 * Función para obtener el ID de usuario, ya sea autenticado o temporal.
 */
function getUserID() {
    // Verificar si el usuario está autenticado.
    if (isset($_SESSION['user_id'])) {
        return $_SESSION['user_id'];
    } else {
        // Si el usuario no está autenticado, generar y devolver un ID temporal.
        if (!isset($_SESSION['temp_user_id'])) {
            $_SESSION['temp_user_id'] = rand(100000000, 999999999); // Generar un ID temporal si no existe.
        }
        return $_SESSION['temp_user_id'];
    }
}

function sanitizeHtml($input)
{
    return $input;
}

function get_date($timestamp) {
    // Traducción de los nombres de los meses
    $meses = [
        1 => 'enero',
        2 => 'febrero',
        3 => 'marzo',
        4 => 'abril',
        5 => 'mayo',
        6 => 'junio',
        7 => 'julio',
        8 => 'agosto',
        9 => 'septiembre',
        10 => 'octubre',
        11 => 'noviembre',
        12 => 'diciembre'
    ];

    // Obtener el día, mes y año del timestamp
    $dia = date('j', $timestamp);
    $numeroMes = date('n', $timestamp);
    $ano = date('Y', $timestamp);

    // Construir la cadena de fecha traducida
    $fechaTraducida = $dia . ' de ' . $meses[$numeroMes] . ', ' . $ano;

    return $fechaTraducida;
}

// Función para obtener la página de inicio desde la base de datos
function getHomePage() {
    global $conn;
    
    // Consulta SQL para obtener la página de inicio desde la base de datos
    $sql = "SELECT home FROM configurations LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if($row['home'] == './apps/crowdfunding/index.php'){
            return './apps/crowdfunding/index.php';
        }else if($row['home'] == './apps/public/news.php'){
            return './apps/public/news.php';
        }else{
            return './apps/public/index.php';
        }
    }

    // Si no se encuentra la configuración, devuelve la página predeterminada
    return './apps/public/index.php';
}

function copyDirectory($source, $destination) {
    if (is_dir($source)) {
        if (!is_dir($destination)) {
            mkdir($destination, 0777, true);
        }

        $files = scandir($source);

        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                $sourcePath = $source . DIRECTORY_SEPARATOR . $file;
                $destinationPath = $destination . DIRECTORY_SEPARATOR . $file;

                if (is_dir($sourcePath)) {
                    copyDirectory($sourcePath, $destinationPath);
                } else {
                    copy($sourcePath, $destinationPath);
                }
            }
        }
    }
}
?>