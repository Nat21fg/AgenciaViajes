<?php

/* Genera una oferta aleatoria para mostrar al usuario */
function generarNotificacion()
{
    $ofertas = [
        "¡20% de descuento en paquetes turísticos a Cancún!",
        "Reserva hoy y obtén una noche de hotel gratis.",
        "Vuelos a Buenos Aires desde $79.990.",
        "15% de descuento en paquetes familiares.",
        "Reserva antes del viernes y recibe traslado al aeropuerto sin costo."
    ];

    return $ofertas[array_rand($ofertas)];
}


/* Clase que representa un paquete turístico */
class FiltroViaje
{
    public $id;
    public $nombreHotel;
    public $ciudad;
    public $pais;
    public $fechaViaje;
    public $duracionViaje;
    public $precio;

    public function __construct($id, $nombreHotel, $ciudad, $pais, $fechaViaje, $duracionViaje, $precio)
    {
        $this->id = $id;
        $this->nombreHotel = $nombreHotel;
        $this->ciudad = $ciudad;
        $this->pais = $pais;
        $this->fechaViaje = $fechaViaje;
        $this->duracionViaje = $duracionViaje;
        $this->precio = $precio;
    }

    public function obtenerInformacion()
    {
        return [
            "id" => $this->id,
            "Hotel" => $this->nombreHotel,
            "Ciudad" => $this->ciudad,
            "País" => $this->pais,
            "Fecha" => $this->fechaViaje,
            "Duración" => $this->duracionViaje . " días",
            "Precio" => $this->precio
        ];
    }
}
function limpiarTexto($valor)
{
    return trim((string) $valor);
}


/* Comprueba que un texto no esté vacío.*/
function textoObligatorio($valor)
{
    return limpiarTexto($valor) !== "";
}


/*Comprueba que un número entero sea mayor que cero.*/
function enteroPositivo($valor)
{
    return filter_var($valor, FILTER_VALIDATE_INT) !== false
        && (int) $valor > 0;
}


/*Comprueba que un número decimal sea mayor que cero.*/
function decimalPositivo($valor)
{
    return filter_var($valor, FILTER_VALIDATE_FLOAT) !== false
        && (float) $valor > 0;
}


function convertirFechaMySQL($fecha)
{
    $fecha = limpiarTexto($fecha);

    if ($fecha === "") {
        return "";
    }

    $fechaMySQL = str_replace("T", " ", $fecha);

    if (strlen($fechaMySQL) === 16) {
        $fechaMySQL .= ":00";
    }

    return $fechaMySQL;
}


/*Valida los datos de un vuelo.Devuelve un arreglo con los errores encontrados.*/
function validarVuelo(
    $origen,
    $destino,
    $fecha,
    $plazasDisponibles,
    $precio
) {
    $errores = [];

    $origen = limpiarTexto($origen);
    $destino = limpiarTexto($destino);
    $fecha = limpiarTexto($fecha);

    if (!textoObligatorio($origen)) {
        $errores[] = "Debe ingresar el origen del vuelo.";
    }

    if (!textoObligatorio($destino)) {
        $errores[] = "Debe ingresar el destino del vuelo.";
    }

    if (
        $origen !== ""
        && $destino !== ""
        && strcasecmp($origen, $destino) === 0
    ) {
        $errores[] = "El origen y el destino no pueden ser iguales.";
    }

    if (!textoObligatorio($fecha)) {
        $errores[] = "Debe ingresar la fecha y hora del vuelo.";
    }

    if (!enteroPositivo($plazasDisponibles)) {
        $errores[] = "Las plazas disponibles deben ser mayores que cero.";
    }

    if (!decimalPositivo($precio)) {
        $errores[] = "El precio del vuelo debe ser mayor que cero.";
    }

    return $errores;
}


/*Valida los datos de un hotel. Devuelve un arreglo con los errores encontrados.*/
function validarHotel(
    $nombre,
    $ubicacion,
    $habitacionesDisponibles,
    $tarifaNoche
) {
    $errores = [];

    $nombre = limpiarTexto($nombre);
    $ubicacion = limpiarTexto($ubicacion);

    if (!textoObligatorio($nombre)) {
        $errores[] = "Debe ingresar el nombre del hotel.";
    }

    if (!textoObligatorio($ubicacion)) {
        $errores[] = "Debe ingresar la ubicación del hotel.";
    }

    if (!enteroPositivo($habitacionesDisponibles)) {
        $errores[] =
            "Las habitaciones disponibles deben ser mayores que cero.";
    }

    if (!decimalPositivo($tarifaNoche)) {
        $errores[] = "La tarifa por noche debe ser mayor que cero.";
    }

    return $errores;
}

/*Escapa textos antes de mostrarlos dentro del HTML.*/
function escaparHTML($valor)
{
    return htmlspecialchars(
        (string) $valor,
        ENT_QUOTES,
        "UTF-8"
    );
}
