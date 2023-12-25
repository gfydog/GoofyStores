<?php
// Inicia la sesión si aún no ha sido iniciada
session_start();

// Incluye los archivos necesarios (configuración, base de datos, etc.)
require_once "config/configFinal.php";
require_once "config/database.php";
require_once "config/common.php";

// Obtiene la página de inicio
$homePage = getHomePage();

// Redirige a la página de inicio
header("Location: $homePage");
exit;
?>