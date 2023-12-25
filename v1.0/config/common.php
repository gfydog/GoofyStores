<?php
function sanitizeHtml($input)
{
    $cleanHtml = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $input);
    $cleanHtml = preg_replace('/<iframe\b[^>]*>*(.*?)(<\/iframe>)*/is', '', $input);
    $cleanHtml = preg_replace('/\s*on\w+="[^"]*"/i', '', $cleanHtml);
    $cleanHtml = preg_replace("/\s*on\w+='[^']*'/i", '', $cleanHtml);

    return $cleanHtml;
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
        if($row['home'] == './apps/public/index.php'){
            return './apps/public/index.php';
        }else{
            return './apps/public/news.php';
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