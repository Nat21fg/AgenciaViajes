
<?php
// notificacion
function generarNotificacion() {
    $ofertas = [
        "¡20% de descuento en paquetes a Cancún!",
        "Reserva hoy y obtén una noche de hotel gratis.",
        "Vuelos a Buenos Aires desde $79.990.",
        "15% de descuento en paquetes familiares."
    ];

    return $ofertas[array_rand($ofertas)];
}


// clase

class FiltroViaje{

    public $nombreHotel;
    public $ciudad;
    public $pais;
    public $fechaViaje;
    public $duracionViaje;

    public function __construct($nombreHotel,$ciudad,$pais,$fechaViaje,$duracionViaje){

        $this->nombreHotel=$nombreHotel;
        $this->ciudad=$ciudad;
        $this->pais=$pais;
        $this->fechaViaje=$fechaViaje;
        $this->duracionViaje=$duracionViaje;

    }

    public function obtenerInformacion(){

        return[
            "Hotel"=>$this->nombreHotel,
            "Ciudad"=>$this->ciudad,
            "País"=>$this->pais,
            "Fecha"=>$this->fechaViaje,
            "Duración"=>$this->duracionViaje." días"
        ];

    }

}

// viajes
$viajes = [

    new FiltroViaje(
        "Hotel 1 Paris",
        "Paris",
        "Francia",
        "2026-07-15",
        7
    ),

    new FiltroViaje(
        "Hotel 2 Paris",
        "Paris",
        "Francia",
        "2026-06-15",
        5
    ),

    new FiltroViaje(
        "Hotel 1 Madrid",
        "Madrid",
        "Espana",
        "2026-08-10",
        8
    ),

    new FiltroViaje(
        "Hotel 1 Sao Paulo",
        "Sao Paulo",
        "Brasil",
        "2026-06-21",
        6
    ),

    new FiltroViaje(
        "Hotel 1 Cancun",
        "Cancun",
        "Mexico",
        "2026-07-15",
        7
    ),

    new FiltroViaje(
        "Hotel 1 Roma",
        "Roma",
        "Italia",
        "2026-09-05",
        10
    )

];

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Agencia de Viajes</title>
</head>
<body>

    <div class="notification">
        <h3>Oferta Especial</h3>
        <script> alert("<?php echo generarNotificacion(); ?>");</script>
    </div>

    <form action="" method="POST">

        <label>Ciudad</label><br>
        <input type="text" name="ciudad" required><br><br>

        <label>Fecha de viaje</label><br>
        <input type="date" name="fecha" required><br><br>

        <input type="submit" value="Buscar viaje">

    </form>

    <div class="results-container">

        <?php

        if($_SERVER["REQUEST_METHOD"]=="POST"){

            $ciudad = trim($_POST["ciudad"]);
            $fecha = $_POST["fecha"];

            $encontrado = false;

            echo "<h2>Resultados de la búsqueda</h2>";

            foreach($viajes as $viaje){

                if (
                    strcasecmp($viaje->ciudad, $ciudad) == 0 &&
                    $viaje->fechaViaje == $fecha
                ){
                    echo "<p><strong>Hotel:</strong> $viaje->nombreHotel</p>";
                    echo "<p><strong>Ciudad:</strong> $viaje->ciudad</p>";
                    echo "<p><strong>País:</strong> $viaje->pais</p>";
                    echo "<p><strong>Fecha:</strong> $viaje->fechaViaje</p>";
                    echo "<p><strong>Duración:</strong> $viaje->duracionViaje días</p>";
                    echo "<hr>";

                    $encontrado = true;
                }
            }

            if(!$encontrado){
                echo "<p>No se encontraron viajes.</p>";
                
            }

        }

        ?>

    </div>

</body>

</html>