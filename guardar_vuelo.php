<?php

require_once "conexion.php";
require_once "funciones.php";

/* Solo permite solicitudes enviadas mediante POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

/* Recuperar y limpiar datos del formulario */
$origen = limpiarTexto($_POST["origen"] ?? "");
$destino = limpiarTexto($_POST["destino"] ?? "");
$fecha = limpiarTexto($_POST["fecha"] ?? "");
$plazasDisponibles = $_POST["plazas_disponibles"] ?? "";
$precio = $_POST["precio"] ?? "";

/* Validar datos */
$errores = validarVuelo(
    $origen,
    $destino,
    $fecha,
    $plazasDisponibles,
    $precio
);

if (!empty($errores)) {
    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Error al registrar vuelo</title>

        <link rel="stylesheet" href="styles.css">
    </head>

    <body>

        <header>
            <h1>Agencia de Viajes</h1>
            <p>No fue posible registrar el vuelo.</p>
        </header>

        <div class="results-container">

            <h2>Revise los siguientes datos</h2>

            <?php foreach ($errores as $error) { ?>

                <p>
                    <?php echo escaparHTML($error); ?>
                </p>

            <?php } ?>

            <a class="boton-enlace" href="index.php">
                Volver al formulario
            </a>

        </div>

    </body>

    </html>

    <?php

    exit();
}

/* Convertir la fecha al formato aceptado por MySQL */
$fechaMySQL = convertirFechaMySQL($fecha);

/* Convertir valores numéricos */
$plazasDisponibles = (int) $plazasDisponibles;
$precio = (float) $precio;

/* Consulta preparada */
$sql = "INSERT INTO VUELO (
            origen,
            destino,
            fecha,
            plazas_disponibles,
            precio
        ) VALUES (?, ?, ?, ?, ?)";

try {

    $sentencia = $conexion->prepare($sql);

    $sentencia->bind_param(
        "sssid",
        $origen,
        $destino,
        $fechaMySQL,
        $plazasDisponibles,
        $precio
    );

    $sentencia->execute();

    $sentencia->close();
    $conexion->close();

    header("Location: listar_vuelos.php?registro=ok");
    exit();

} catch (mysqli_sql_exception $error) {

    error_log(
        "Error al registrar vuelo: " .
        $error->getMessage()
    );

    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Error de registro</title>

        <link rel="stylesheet" href="styles.css">
    </head>

    <body>

        <header>
            <h1>Agencia de Viajes</h1>
        </header>

        <div class="results-container">

            <h2>Error al registrar el vuelo</h2>

            <p>
                Ocurrió un problema al guardar la información en la base de datos.
            </p>

            <a class="boton-enlace" href="index.php">
                Volver al formulario
            </a>

        </div>

    </body>

    </html>

    <?php
}