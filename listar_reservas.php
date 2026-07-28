<?php

require_once "conexion.php";
require_once "funciones.php";

$sql = "SELECT
            id_reserva,
            id_cliente,
            fecha_reserva,
            id_vuelo,
            id_hotel
        FROM RESERVA
        ORDER BY fecha_reserva DESC";

$resultado = $conexion->query($sql);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reservas registradas</title>

    <link
        rel="stylesheet"
        href="styles.css"
    >

</head>

<body>

<header>

    <h1>Agencia de Viajes</h1>

    <p>
        Reservas registradas en la base de datos.
    </p>

</header>

<div class="tabla-container">

    <h2>Listado de reservas</h2>

    <?php if(
        isset($_GET["registro"]) &&
        $_GET["registro"] === "ok"
    ) { ?>

        <div class="mensaje-exito">

            La reserva fue registrada correctamente.

        </div>

    <?php } ?>

    <?php if($resultado->num_rows > 0) { ?>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>ID vuelo</th>
                    <th>ID hotel</th>

                </tr>

            </thead>

            <tbody>

                <?php while(
                    $reserva = $resultado->fetch_assoc()
                ) { ?>

                    <tr>

                        <td>
                            <?php
                            echo (int)
                                $reserva["id_reserva"];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo (int)
                                $reserva["id_cliente"];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo date(
                                "d/m/Y H:i",
                                strtotime(
                                    $reserva["fecha_reserva"]
                                )
                            );
                            ?>
                        </td>

                        <td>
                            <?php
                            echo (int)
                                $reserva["id_vuelo"];
                            ?>
                        </td>

                        <td>
                            <?php
                            echo (int)
                                $reserva["id_hotel"];
                            ?>
                        </td>

                    </tr>

                <?php } ?>

            </tbody>

        </table>

    <?php } else { ?>

        <p>No existen reservas registradas.</p>

    <?php } ?>

    <div class="acciones">

        <a
            class="boton-enlace"
            href="registrar_reserva.php"
        >
            Registrar reserva
        </a>

        <a
            class="boton-enlace"
            href="reporte_reservas_hotel.php"
        >
            Ver reporte por hotel
        </a>

        <a
            class="boton-enlace"
            href="index.php"
        >
            Volver al inicio
        </a>

    </div>

</div>

</body>

</html>

<?php

$resultado->free();
$conexion->close();

?>