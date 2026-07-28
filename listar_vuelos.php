<?php

require_once "conexion.php";
require_once "funciones.php";

/* Consulta simple para mostrar todos los vuelos */
$sql = "SELECT
            id_vuelo,
            origen,
            destino,
            fecha,
            plazas_disponibles,
            precio
        FROM VUELO
        ORDER BY fecha ASC";

try {

    $resultado = $conexion->query($sql);

} catch (mysqli_sql_exception $error) {

    error_log(
        "Error al consultar vuelos: " .
        $error->getMessage()
    );

    die("No fue posible consultar los vuelos registrados.");
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Vuelos registrados</title>

    <link rel="stylesheet" href="styles.css">
</head>

<body>

<header>

    <h1>Agencia de Viajes</h1>

    <p>Listado de vuelos registrados en la base de datos.</p>

</header>

<div class="tabla-container">

    <h2>Vuelos disponibles</h2>

    <?php if (
        isset($_GET["registro"]) &&
        $_GET["registro"] === "ok"
    ) { ?>

        <div class="mensaje-exito">
            El vuelo fue registrado correctamente.
        </div>

    <?php } ?>

    <?php if ($resultado->num_rows > 0) { ?>

        <table>

            <thead>

                <tr>
                    <th>ID</th>
                    <th>Origen</th>
                    <th>Destino</th>
                    <th>Fecha</th>
                    <th>Plazas disponibles</th>
                    <th>Precio</th>
                </tr>

            </thead>

            <tbody>

                <?php while ($vuelo = $resultado->fetch_assoc()) { ?>

                    <tr>

                        <td>
                            <?php echo (int) $vuelo["id_vuelo"]; ?>
                        </td>

                        <td>
                            <?php echo escaparHTML($vuelo["origen"]); ?>
                        </td>

                        <td>
                            <?php echo escaparHTML($vuelo["destino"]); ?>
                        </td>

                        <td>
                            <?php
                            echo date(
                                "d/m/Y H:i",
                                strtotime($vuelo["fecha"])
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo (int) $vuelo["plazas_disponibles"];
                            ?>
                        </td>

                        <td>
                            $<?php
                            echo number_format(
                                (float) $vuelo["precio"],
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

        <p>No existen vuelos registrados en la base de datos.</p>

    <?php } ?>

    <div class="acciones">

        <a class="boton-enlace" href="index.php">
            Volver al inicio
        </a>

        <a class="boton-enlace" href="index.php#formulario-vuelo">
            Registrar otro vuelo
        </a>

    </div>

</div>

</body>

</html>

<?php

$resultado->free();
$conexion->close();

?>