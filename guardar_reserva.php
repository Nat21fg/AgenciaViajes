<?php

require_once "conexion.php";
require_once "funciones.php";

if($_SERVER["REQUEST_METHOD"] !== "POST"){

    header("Location: registrar_reserva.php");
    exit();
}

$idCliente = filter_input(
    INPUT_POST,
    "id_cliente",
    FILTER_VALIDATE_INT
);

$idVuelo = filter_input(
    INPUT_POST,
    "id_vuelo",
    FILTER_VALIDATE_INT
);

$idHotel = filter_input(
    INPUT_POST,
    "id_hotel",
    FILTER_VALIDATE_INT
);

$fechaReserva = limpiarTexto(
    $_POST["fecha_reserva"] ?? ""
);

$errores = [];

if($idCliente === false || $idCliente <= 0){

    $errores[] =
        "El identificador del cliente no es válido.";
}

if($idVuelo === false || $idVuelo <= 0){

    $errores[] =
        "Debe seleccionar un vuelo válido.";
}

if($idHotel === false || $idHotel <= 0){

    $errores[] =
        "Debe seleccionar un hotel válido.";
}

if($fechaReserva === ""){

    $errores[] =
        "Debe ingresar la fecha de reserva.";
}

$fechaMySQL = convertirFechaMySQL(
    $fechaReserva
);


/* Comprobar disponibilidad del vuelo */

if(empty($errores)){

    $sqlVuelo = "SELECT plazas_disponibles
                 FROM VUELO
                 WHERE id_vuelo = ?";

    $consultaVuelo = $conexion->prepare(
        $sqlVuelo
    );

    $consultaVuelo->bind_param(
        "i",
        $idVuelo
    );

    $consultaVuelo->execute();

    $resultadoVuelo =
        $consultaVuelo->get_result();

    $vuelo =
        $resultadoVuelo->fetch_assoc();

    if(!$vuelo){

        $errores[] =
            "El vuelo seleccionado no existe.";

    }elseif(
        (int) $vuelo["plazas_disponibles"] <= 0
    ){

        $errores[] =
            "El vuelo no tiene plazas disponibles.";
    }

    $consultaVuelo->close();
}


/* Comprobar disponibilidad del hotel */

if(empty($errores)){

    $sqlHotel = "SELECT habitaciones_disponibles
                 FROM HOTEL
                 WHERE id_hotel = ?";

    $consultaHotel = $conexion->prepare(
        $sqlHotel
    );

    $consultaHotel->bind_param(
        "i",
        $idHotel
    );

    $consultaHotel->execute();

    $resultadoHotel =
        $consultaHotel->get_result();

    $hotel =
        $resultadoHotel->fetch_assoc();

    if(!$hotel){

        $errores[] =
            "El hotel seleccionado no existe.";

    }elseif(
        (int) $hotel["habitaciones_disponibles"] <= 0
    ){

        $errores[] =
            "El hotel no tiene habitaciones disponibles.";
    }

    $consultaHotel->close();
}


/* Mostrar errores */

if(!empty($errores)){

    ?>

    <!DOCTYPE html>
    <html lang="es">

    <head>

        <meta charset="UTF-8">

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1.0"
        >

        <title>Error de reserva</title>

        <link
            rel="stylesheet"
            href="styles.css"
        >

    </head>

    <body>

    <header>

        <h1>Agencia de Viajes</h1>

        <p>No fue posible registrar la reserva.</p>

    </header>

    <div class="results-container">

        <h2>Errores encontrados</h2>

        <?php foreach($errores as $error) { ?>

            <p>
                <?php echo escaparHTML($error); ?>
            </p>

        <?php } ?>

        <a
            class="boton-enlace"
            href="registrar_reserva.php"
        >
            Volver al formulario
        </a>

    </div>

    </body>

    </html>

    <?php

    exit();
}


/* Insertar reserva */

$sql = "INSERT INTO RESERVA (
            id_cliente,
            fecha_reserva,
            id_vuelo,
            id_hotel
        ) VALUES (?, ?, ?, ?)";

try{

    $sentencia = $conexion->prepare(
        $sql
    );

    $sentencia->bind_param(
        "isii",
        $idCliente,
        $fechaMySQL,
        $idVuelo,
        $idHotel
    );

    $sentencia->execute();

    $sentencia->close();
    $conexion->close();

    header(
        "Location: listar_reservas.php?registro=ok"
    );

    exit();

}catch(mysqli_sql_exception $error){

    error_log(
        "Error al guardar reserva: " .
        $error->getMessage()
    );

    die(
        "No fue posible registrar la reserva."
    );
}