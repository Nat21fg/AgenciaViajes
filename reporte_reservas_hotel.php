<?php

require_once "conexion.php";
require_once "funciones.php";

$resultado = null;
$consultado = false;

if(isset($_POST["consultarReporte"])){

    $consultado = true;

    $sql = "SELECT
                h.id_hotel,
                h.nombre,
                h.ubicacion,
                COUNT(r.id_reserva)
                    AS cantidad_reservas
            FROM HOTEL h

            INNER JOIN RESERVA r
                ON h.id_hotel = r.id_hotel

            GROUP BY
                h.id_hotel,
                h.nombre,
                h.ubicacion

            HAVING COUNT(r.id_reserva) > 2

            ORDER BY
                cantidad_reservas DESC,
                h.nombre ASC";

    $resultado = $conexion->query($sql);
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Reservas por hotel</title>

    <link
        rel="stylesheet"
        href="styles.css"
    >

</head>

<body>

<header>

    <h1>Agencia de Viajes</h1>

    <p>
        Consulta avanzada de reservas asignadas
        por hotel.
    </p>

</header>

<form method="POST">

    <h2>Consultar hoteles más reservados</h2>

    <p>
        La consulta mostrará los hoteles que tienen
        más de dos reservas registradas.
    </p>

    <button
        type="submit"
        name="consultarReporte"
    >
        Consultar reservas por hotel
    </button>

</form>

<?php if($consultado) { ?>

    <div class="tabla-container">

        <h2>
            Hoteles con más de dos reservas
        </h2>

        <?php if(
            $resultado &&
            $resultado->num_rows > 0
        ) { ?>

            <table>

                <thead>

                    <tr>

                        <th>ID</th>
                        <th>Hotel</th>
                        <th>Ubicación</th>
                        <th>Cantidad de reservas</th>

                    </tr>

                </thead>

                <tbody>

                    <?php while(
                        $hotel = $resultado->fetch_assoc()
                    ) { ?>

                        <tr>

                            <td>
                                <?php
                                echo (int)
                                    $hotel["id_hotel"];
                                ?>
                            </td>

                            <td>
                                <?php
                                echo escaparHTML(
                                    $hotel["nombre"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo escaparHTML(
                                    $hotel["ubicacion"]
                                );
                                ?>
                            </td>

                            <td>
                                <?php
                                echo (int)
                                    $hotel["cantidad_reservas"];
                                ?>
                            </td>

                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        <?php } else { ?>

            <p>
                No existen hoteles con más de dos reservas.
            </p>

        <?php } ?>

        <div class="acciones">

            <a
                class="boton-enlace"
                href="listar_reservas.php"
            >
                Ver todas las reservas
            </a>

            <a
                class="boton-enlace"
                href="index.php"
            >
                Volver al inicio
            </a>

        </div>

    </div>

<?php } ?>

</body>

</html>

<?php

if($resultado){

    $resultado->free();
}

$conexion->close();

?>