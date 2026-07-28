<?php

require_once "conexion.php";
require_once "funciones.php";

/* Consultar vuelos disponibles */

$sqlVuelos = "SELECT
                id_vuelo,
                origen,
                destino,
                fecha,
                plazas_disponibles,
                precio
              FROM VUELO
              WHERE plazas_disponibles > 0
              ORDER BY fecha ASC";

$vuelos = $conexion->query($sqlVuelos);


/* Consultar hoteles disponibles */

$sqlHoteles = "SELECT
                id_hotel,
                nombre,
                ubicacion,
                habitaciones_disponibles,
                tarifa_noche
               FROM HOTEL
               WHERE habitaciones_disponibles > 0
               ORDER BY nombre ASC";

$hoteles = $conexion->query($sqlHoteles);

?>

<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Registrar reserva</title>

    <link rel="stylesheet" href="styles.css">

    <script>

        function validarReservaFormulario(){

            const cliente = document
                .getElementById("id_cliente")
                .value;

            const vuelo = document
                .getElementById("id_vuelo")
                .value;

            const hotel = document
                .getElementById("id_hotel")
                .value;

            if(cliente === "" || Number(cliente) <= 0){

                alert(
                    "Debe ingresar un identificador de cliente válido."
                );

                return false;
            }

            if(vuelo === ""){

                alert("Debe seleccionar un vuelo.");

                return false;
            }

            if(hotel === ""){

                alert("Debe seleccionar un hotel.");

                return false;
            }

            return true;
        }

    </script>

</head>

<body>

<header>

    <h1>Agencia de Viajes</h1>

    <p>Registro de reservas de vuelos y hoteles.</p>

</header>

<form
    action="guardar_reserva.php"
    method="POST"
    onsubmit="return validarReservaFormulario();"
>

    <h2>Registrar reserva</h2>

    <label for="id_cliente">
        Identificador del cliente
    </label>

    <input
        type="number"
        id="id_cliente"
        name="id_cliente"
        min="1"
        required
    >

    <label for="fecha_reserva">
        Fecha de reserva
    </label>

    <input
        type="datetime-local"
        id="fecha_reserva"
        name="fecha_reserva"
        required
    >

    <label for="id_vuelo">
        Vuelo
    </label>

    <select
        id="id_vuelo"
        name="id_vuelo"
        required
    >

        <option value="">
            Seleccione un vuelo
        </option>

        <?php while ($vuelo = $vuelos->fetch_assoc()) { ?>

            <option
                value="<?php echo (int) $vuelo["id_vuelo"]; ?>"
            >

                <?php
                echo escaparHTML(
                    $vuelo["origen"] .
                    " → " .
                    $vuelo["destino"] .
                    " | " .
                    date(
                        "d/m/Y H:i",
                        strtotime($vuelo["fecha"])
                    ) .
                    " | Plazas: " .
                    $vuelo["plazas_disponibles"]
                );
                ?>

            </option>

        <?php } ?>

    </select>

    <label for="id_hotel">
        Hotel
    </label>

    <select
        id="id_hotel"
        name="id_hotel"
        required
    >

        <option value="">
            Seleccione un hotel
        </option>

        <?php while ($hotel = $hoteles->fetch_assoc()) { ?>

            <option
                value="<?php echo (int) $hotel["id_hotel"]; ?>"
            >

                <?php
                echo escaparHTML(
                    $hotel["nombre"] .
                    " | " .
                    $hotel["ubicacion"] .
                    " | Habitaciones: " .
                    $hotel["habitaciones_disponibles"]
                );
                ?>

            </option>

        <?php } ?>

    </select>

    <button type="submit">
        Guardar reserva
    </button>

</form>

<div class="acciones">

    <a
        class="boton-enlace"
        href="listar_reservas.php"
    >
        Ver reservas
    </a>

    <a
        class="boton-enlace"
        href="index.php"
    >
        Volver al inicio
    </a>

</div>

</body>

</html>

<?php

$vuelos->free();
$hoteles->free();
$conexion->close();

?>