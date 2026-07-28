<?php

require_once "conexion.php";
require_once "funciones.php";

/* Consulta simple para mostrar todos los hoteles */
$sql = "SELECT
            id_hotel,
            nombre,
            ubicacion,
            habitaciones_disponibles,
            tarifa_noche
        FROM HOTEL
        ORDER BY nombre ASC";

try {

    $resultado = $conexion->query($sql);

} catch (mysqli_sql_exception $error) {

    error_log(
        "Error al consultar hoteles: " .
        $error->getMessage()
    );

    die("No fue posible consultar los hoteles registrados.");
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Hoteles registrados</title>

    <link rel="stylesheet" href="styles.css">

</head>

<body>

<header>

    <h1>Agencia de Viajes</h1>

    <p>Listado de hoteles registrados en la base de datos.</p>

</header>

<div class="tabla-container">

    <h2>Hoteles disponibles</h2>

    <?php if (
        isset($_GET["registro"]) &&
        $_GET["registro"] == "ok"
    ) { ?>

        <div class="mensaje-exito">

            El hotel fue registrado correctamente.

        </div>

    <?php } ?>

    <?php if ($resultado->num_rows > 0) { ?>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Nombre</th>

                    <th>Ubicación</th>

                    <th>Habitaciones disponibles</th>

                    <th>Tarifa por noche</th>

                </tr>

            </thead>

            <tbody>

                <?php while ($hotel = $resultado->fetch_assoc()) { ?>

                    <tr>

                        <td>

                            <?php echo (int) $hotel["id_hotel"]; ?>

                        </td>

                        <td>

                            <?php echo escaparHTML($hotel["nombre"]); ?>

                        </td>

                        <td>

                            <?php echo escaparHTML($hotel["ubicacion"]); ?>

                        </td>

                        <td>

                            <?php
                            echo (int) $hotel["habitaciones_disponibles"];
                            ?>

                        </td>

                        <td>

                            $<?php
                            echo number_format(
                                (float) $hotel["tarifa_noche"],
                                0,
                                ",",
                                "."
                            );
                            ?>

                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    <?php } else { ?>

        <p>No existen hoteles registrados en la base de datos.</p>

    <?php } ?>

    <div class="acciones">

        <a class="boton-enlace" href="index.php">

            Volver al inicio

        </a>

        <a class="boton-enlace" href="index.php#formulario-hotel">

            Registrar otro hotel

        </a>

    </div>

</div>

</body>

</html>

<?php

$resultado->free();

$conexion->close();

?>