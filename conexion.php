<?php

// Muestra errores durante el desarrollo
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// Parámetros de conexión
$servidor = "localhost";
$usuario = "root";
$contrasena = "";
$baseDatos = "AGENCIA";
$puerto = 3306;

try {

    $conexion = new mysqli(
        $servidor,
        $usuario,
        $contrasena,
        $baseDatos,
        $puerto
    );

    // Configurar codificación
    $conexion->set_charset("utf8mb4");

} catch (mysqli_sql_exception $e) {

    die("Error al conectar con la base de datos: " . $e->getMessage());

}

?>