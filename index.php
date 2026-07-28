<?php

// Medida 1: duración de la cookie de sesión: 30 minutos
session_set_cookie_params([
    "lifetime" => 1800,
    "path" => "/",
    "httponly" => true,
    "samesite" => "Strict"
]);

// Medida 2: duración de la sesión en el servidor: 30 minutos
ini_set("session.gc_maxlifetime", 1800);

session_start();

require_once "funciones.php";
require_once "viajes.php";

// Medida 3: control de inactividad
$tiempoMaximo = 1800;

if (isset($_SESSION["ultima_actividad"])) {

    if (time() - $_SESSION["ultima_actividad"] > $tiempoMaximo) {

        session_unset();
        session_destroy();

        header("Location: index.php");
        exit();
    }
}

$_SESSION["ultima_actividad"] = time();

$resultados = [];

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

// Regenerar ID de sesión una sola vez
if (!isset($_SESSION["iniciada"])) {
    session_regenerate_id(true);
    $_SESSION["iniciada"] = true;
}

// Agregar paquete al carrito
if (isset($_POST["agregarCarrito"])) {

    $idViaje = $_POST["idViaje"] ?? "";

    foreach ($viajes as $viaje) {

        if ($viaje->id == $idViaje) {

            $_SESSION["carrito"][$idViaje] = [
                "hotel" => $viaje->nombreHotel,
                "ciudad" => $viaje->ciudad,
                "pais" => $viaje->pais,
                "fecha" => $viaje->fechaViaje,
                "duracion" => $viaje->duracionViaje,
                "precio" => $viaje->precio
            ];

            break;
        }
    }
}

// Eliminar paquete del carrito
if (isset($_POST["eliminarCarrito"])) {

    $idEliminar = $_POST["idEliminar"] ?? "";

    if ($idEliminar !== "") {
        unset($_SESSION["carrito"][$idEliminar]);
    }
}

// Vaciar carrito
if (isset($_POST["vaciarCarrito"])) {
    $_SESSION["carrito"] = [];
}

// Buscar viajes
if (isset($_POST["buscarViaje"])) {

    $hotel = trim($_POST["hotel"] ?? "");
    $ciudad = trim($_POST["ciudad"] ?? "");
    $pais = trim($_POST["pais"] ?? "");
    $fecha = $_POST["fecha"] ?? "";
    $duracion = trim($_POST["duracion"] ?? "");

    $_SESSION["ultimaBusqueda"] = [
        "hotel" => $hotel,
        "ciudad" => $ciudad,
        "pais" => $pais,
        "fecha" => $fecha,
        "duracion" => $duracion
    ];

    foreach ($viajes as $viaje) {

        if (
            ($hotel === "" || stripos($viaje->nombreHotel, $hotel) !== false) &&
            ($ciudad === "" || stripos($viaje->ciudad, $ciudad) !== false) &&
            ($pais === "" || stripos($viaje->pais, $pais) !== false) &&
            ($fecha === "" || $viaje->fechaViaje === $fecha) &&
            ($duracion === "" || $viaje->duracionViaje == $duracion)
        ) {
            $resultados[] = $viaje;
        }
    }
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

    <title>Agencia de Viajes</title>

    <link rel="stylesheet" href="styles.css">

    <script>

        function validarVueloFormulario() {

            const origen = document
                .getElementById("origen")
                .value
                .trim();

            const destino = document
                .getElementById("destino")
                .value
                .trim();

            const fecha = document
                .getElementById("fecha_vuelo")
                .value;

            const plazas = Number(
                document.getElementById("plazas_disponibles").value
            );

            const precio = Number(
                document.getElementById("precio_vuelo").value
            );

            if (origen === "" || destino === "" || fecha === "") {
                alert("Debe completar origen, destino y fecha.");
                return false;
            }

            if (origen.toLowerCase() === destino.toLowerCase()) {
                alert("El origen y el destino no pueden ser iguales.");
                return false;
            }

            if (plazas <= 0) {
                alert("Las plazas disponibles deben ser mayores que cero.");
                return false;
            }

            if (precio <= 0) {
                alert("El precio debe ser mayor que cero.");
                return false;
            }

            return true;
        }


        function validarHotelFormulario() {

            const nombre = document
                .getElementById("nombre_hotel")
                .value
                .trim();

            const ubicacion = document
                .getElementById("ubicacion_hotel")
                .value
                .trim();

            const habitaciones = Number(
                document.getElementById("habitaciones_disponibles").value
            );

            const tarifa = Number(
                document.getElementById("tarifa_noche").value
            );

            if (nombre === "" || ubicacion === "") {
                alert("Debe completar el nombre y la ubicación del hotel.");
                return false;
            }

            if (habitaciones <= 0) {
                alert(
                    "Las habitaciones disponibles deben ser mayores que cero."
                );
                return false;
            }

            if (tarifa <= 0) {
                alert("La tarifa por noche debe ser mayor que cero.");
                return false;
            }

            return true;
        }

    </script>

</head>

<body>

<header>

    <h1>Agencia de Viajes</h1>

    <p>
        Encuentra el paquete turístico ideal para tus próximas vacaciones.
    </p>

</header>


<div class="notification">

    <strong>Oferta del día:</strong>

    <?php echo generarNotificacion(); ?>

</div>


<!-- BUSCADOR DE PAQUETES -->

<form method="POST">

    <h2>Buscar paquete turístico</h2>

    <label for="hotel">Nombre del hotel</label>

    <input
        type="text"
        id="hotel"
        name="hotel"
        value="<?php
            echo htmlspecialchars(
                $_SESSION["ultimaBusqueda"]["hotel"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            );
        ?>"
    >

    <label for="ciudad">Ciudad</label>

    <input
        type="text"
        id="ciudad"
        name="ciudad"
        value="<?php
            echo htmlspecialchars(
                $_SESSION["ultimaBusqueda"]["ciudad"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            );
        ?>"
    >

    <label for="pais">País</label>

    <input
        type="text"
        id="pais"
        name="pais"
        value="<?php
            echo htmlspecialchars(
                $_SESSION["ultimaBusqueda"]["pais"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            );
        ?>"
    >

    <label for="fecha">Fecha de viaje</label>

    <input
        type="date"
        id="fecha"
        name="fecha"
        value="<?php
            echo htmlspecialchars(
                $_SESSION["ultimaBusqueda"]["fecha"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            );
        ?>"
    >

    <label for="duracion">Duración (días)</label>

    <input
        type="number"
        id="duracion"
        name="duracion"
        min="1"
        value="<?php
            echo htmlspecialchars(
                $_SESSION["ultimaBusqueda"]["duracion"] ?? "",
                ENT_QUOTES,
                "UTF-8"
            );
        ?>"
    >

    <button type="submit" name="buscarViaje">
        Buscar viaje
    </button>

</form>


<!-- RESULTADOS DE BÚSQUEDA -->

<div class="results-container">

    <h2>Resultados de la búsqueda</h2>

    <?php if (isset($_POST["buscarViaje"])) { ?>

        <?php if (count($resultados) > 0) { ?>

            <?php foreach ($resultados as $viaje) { ?>

                <div class="resultado">

                    <p>
                        <strong>Hotel:</strong>
                        <?php echo escaparHTML($viaje->nombreHotel); ?>
                    </p>

                    <p>
                        <strong>Ciudad:</strong>
                        <?php echo escaparHTML($viaje->ciudad); ?>
                    </p>

                    <p>
                        <strong>País:</strong>
                        <?php echo escaparHTML($viaje->pais); ?>
                    </p>

                    <p>
                        <strong>Fecha:</strong>
                        <?php echo escaparHTML($viaje->fechaViaje); ?>
                    </p>

                    <p>
                        <strong>Duración:</strong>
                        <?php echo (int) $viaje->duracionViaje; ?> días
                    </p>

                    <p>
                        <strong>Precio:</strong>
                        $<?php
                        echo number_format(
                            (float) $viaje->precio,
                            0,
                            ",",
                            "."
                        );
                        ?>
                    </p>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="idViaje"
                            value="<?php echo (int) $viaje->id; ?>"
                        >

                        <button
                            type="submit"
                            name="agregarCarrito"
                        >
                            Agregar al carrito
                        </button>

                    </form>

                </div>

            <?php } ?>

        <?php } else { ?>

            <p>No se encontraron paquetes turísticos.</p>

        <?php } ?>

    <?php } else { ?>

        <p>Complete el formulario para realizar una búsqueda.</p>

    <?php } ?>

</div>


<!-- ÚLTIMA BÚSQUEDA -->

<?php if (isset($_SESSION["ultimaBusqueda"])) { ?>

    <div class="results-container">

        <h2>Última búsqueda almacenada en la sesión</h2>

        <p>
            <strong>Hotel:</strong>
            <?php
            echo escaparHTML(
                $_SESSION["ultimaBusqueda"]["hotel"]
            );
            ?>
        </p>

        <p>
            <strong>Ciudad:</strong>
            <?php
            echo escaparHTML(
                $_SESSION["ultimaBusqueda"]["ciudad"]
            );
            ?>
        </p>

        <p>
            <strong>País:</strong>
            <?php
            echo escaparHTML(
                $_SESSION["ultimaBusqueda"]["pais"]
            );
            ?>
        </p>

        <p>
            <strong>Fecha:</strong>
            <?php
            echo escaparHTML(
                $_SESSION["ultimaBusqueda"]["fecha"]
            );
            ?>
        </p>

        <p>
            <strong>Duración:</strong>
            <?php
            echo escaparHTML(
                $_SESSION["ultimaBusqueda"]["duracion"]
            );
            ?>
            días
        </p>

        <form action="finalizar_sesion.php" method="POST">

            <button class="btn-cerrar" type="submit">
                Cerrar sesión
            </button>

        </form>

    </div>

<?php } ?>


<!-- CARRITO -->

<div class="results-container">

    <h2>Carrito de paquetes turísticos</h2>

    <?php if (!empty($_SESSION["carrito"])) { ?>

        <?php

        $total = 0;

        foreach ($_SESSION["carrito"] as $id => $paquete) {

            $total += (float) $paquete["precio"];

        ?>

            <div class="resultado">

                <p>
                    <strong>Hotel:</strong>
                    <?php echo escaparHTML($paquete["hotel"]); ?>
                </p>

                <p>
                    <strong>Ciudad:</strong>
                    <?php echo escaparHTML($paquete["ciudad"]); ?>
                </p>

                <p>
                    <strong>País:</strong>
                    <?php echo escaparHTML($paquete["pais"]); ?>
                </p>

                <p>
                    <strong>Fecha:</strong>
                    <?php echo escaparHTML($paquete["fecha"]); ?>
                </p>

                <p>
                    <strong>Duración:</strong>
                    <?php echo (int) $paquete["duracion"]; ?> días
                </p>

                <p>
                    <strong>Precio:</strong>
                    $<?php
                    echo number_format(
                        (float) $paquete["precio"],
                        0,
                        ",",
                        "."
                    );
                    ?>
                </p>

                <form method="POST">

                    <input
                        type="hidden"
                        name="idEliminar"
                        value="<?php echo (int) $id; ?>"
                    >

                    <button
                        class="btn-cerrar"
                        type="submit"
                        name="eliminarCarrito"
                    >
                        Eliminar paquete
                    </button>

                </form>

            </div>

        <?php } ?>

        <p>
            <strong>Total carrito:</strong>

            $<?php
            echo number_format(
                $total,
                0,
                ",",
                "."
            );
            ?>
        </p>

        <form method="POST">

            <button
                class="btn-cerrar"
                type="submit"
                name="vaciarCarrito"
            >
                Vaciar carrito
            </button>

        </form>

    <?php } else { ?>

        <p>El carrito está vacío.</p>

    <?php } ?>

</div>


<!-- GESTIÓN MYSQL -->

<section class="gestion-servicios">

    <h2>Gestión de vuelos y hoteles</h2>

    <div class="contenedor-formularios">

        <!-- FORMULARIO DE VUELOS -->

        <form
            id="formulario-vuelo"
            action="guardar_vuelo.php"
            method="POST"
            onsubmit="return validarVueloFormulario();"
        >

            <h2>Registrar vuelo</h2>

            <label for="origen">Origen</label>

            <input
                type="text"
                id="origen"
                name="origen"
                maxlength="100"
                required
            >

            <label for="destino">Destino</label>

            <input
                type="text"
                id="destino"
                name="destino"
                maxlength="100"
                required
            >

            <label for="fecha_vuelo">Fecha y hora del vuelo</label>

            <input
                type="datetime-local"
                id="fecha_vuelo"
                name="fecha"
                required
            >

            <label for="plazas_disponibles">
                Plazas disponibles
            </label>

            <input
                type="number"
                id="plazas_disponibles"
                name="plazas_disponibles"
                min="1"
                required
            >

            <label for="precio_vuelo">Precio</label>

            <input
                type="number"
                id="precio_vuelo"
                name="precio"
                min="1"
                step="0.01"
                required
            >

            <button type="submit">
                Guardar vuelo
            </button>

        </form>


        <!-- FORMULARIO DE HOTELES -->

        <form
            id="formulario-hotel"
            action="guardar_hotel.php"
            method="POST"
            onsubmit="return validarHotelFormulario();"
        >

            <h2>Registrar hotel</h2>

            <label for="nombre_hotel">
                Nombre del hotel
            </label>

            <input
                type="text"
                id="nombre_hotel"
                name="nombre"
                maxlength="150"
                required
            >

            <label for="ubicacion_hotel">
                Ubicación
            </label>

            <input
                type="text"
                id="ubicacion_hotel"
                name="ubicacion"
                maxlength="150"
                required
            >

            <label for="habitaciones_disponibles">
                Habitaciones disponibles
            </label>

            <input
                type="number"
                id="habitaciones_disponibles"
                name="habitaciones_disponibles"
                min="1"
                required
            >

            <label for="tarifa_noche">
                Tarifa por noche
            </label>

            <input
                type="number"
                id="tarifa_noche"
                name="tarifa_noche"
                min="1"
                step="0.01"
                required
            >

            <button type="submit">
                Guardar hotel
            </button>

        </form>

    </div>

    <div class="acciones">

        <a
            class="boton-enlace"
            href="listar_vuelos.php"
        >
            Ver vuelos registrados
        </a>

        <a
            class="boton-enlace"
            href="listar_hoteles.php"
        >
            Ver hoteles registrados
        </a>
        <a
            class="boton-enlace"
            href="registrar_reserva.php"
        >
            Registrar reserva
        </a>

        <a
            class="boton-enlace"
            href="listar_reservas.php"
        >
            Ver reservas
        </a>

        <a
            class="boton-enlace"
            href="reporte_reservas_hotel.php"
        >
            Reservas por hotel
        </a>

    </div>

</section>

</body>

</html>