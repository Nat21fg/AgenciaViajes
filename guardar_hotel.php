<?php

require_once "conexion.php";
require_once "funciones.php";

/* Solo permite solicitudes enviadas mediante POST */
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit();
}

/* Recuperar y limpiar datos del formulario */
$nombre = limpiarTexto($_POST["nombre"] ?? "");
$ubicacion = limpiarTexto($_POST["ubicacion"] ?? "");
$habitacionesDisponibles = $_POST["habitaciones_disponibles"] ?? "";
$tarifaNoche = $_POST["tarifa_noche"] ?? "";

/* Validar datos */
$errores = validarHotel(
    $nombre,
    $ubicacion,
    $habitacionesDisponibles,
    $tarifaNoche
);

if (!empty($errores)) {
    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Error al registrar hotel</title>

        <link rel="stylesheet" href="styles.css">
    </head>

    <body>

        <header>
            <h1>Agencia de Viajes</h1>
            <p>No fue posible registrar el hotel.</p>
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

/* Convertir valores numéricos */
$habitacionesDisponibles = (int) $habitacionesDisponibles;
$tarifaNoche = (float) $tarifaNoche;

/* Consulta preparada */
$sql = "INSERT INTO HOTEL (
            nombre,
            ubicacion,
            habitaciones_disponibles,
            tarifa_noche
        ) VALUES (?, ?, ?, ?)";

try {

    $sentencia = $conexion->prepare($sql);

    $sentencia->bind_param(
        "ssid",
        $nombre,
        $ubicacion,
        $habitacionesDisponibles,
        $tarifaNoche
    );

    $sentencia->execute();

    $sentencia->close();
    $conexion->close();

    header("Location: listar_hoteles.php?registro=ok");
    exit();

} catch (mysqli_sql_exception $error) {

    error_log(
        "Error al registrar hotel: " .
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

            <h2>Error al registrar el hotel</h2>

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
