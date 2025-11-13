<?php
// VARIABLES INICIALES
session_start();
$locacion = $_SESSION["locacion"];
$ciudad = $_SESSION["ciudad"];
$url = $_SESSION["url"];
$ip_server = $_SESSION["ip_server"];
$duration = $_SESSION["duration"];
$controllerversion  = $_SESSION["controllerversion"];
$color_boton_index  = $_SESSION["color_boton_index"];
$debug = false;
// >>
// RECEPCION DE VARIABLES
$mac = $_POST["id"];
$ap = $_POST["ap"];
$name = $_POST['nombre'];
$nacimiento= $_POST['anio']."-".$_POST['mes']."-".$_POST['dia'];
$email = $_POST['email'];
// >>
require __DIR__ . '/vendor/autoload.php';
$site_id = $_SESSION["site_id"];
$controlleruser     = $_SESSION["controlleruser"];
$controllerpassword = $_SESSION["controllerpassword"];
$controllerurl      = $_SESSION["controllerurl"];
$unifi_conx = new UniFi_API\Client($controlleruser, $controllerpassword, $controllerurl, $site_id, $controllerversion);
$set_debug_mode   = $unifi_conx->set_debug($debug);
$loginresults     = $unifi_conx->login();
$auth_result = $unifi_conx->authorize_guest($mac, $duration, $up = null, $down = null, $MBytes = null, $ap);
// MANDALAWEBSERVICES

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://mandalawebservices.com/hotspot/connect.php',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS => array('name' => $name, 'email' => $email, 'nacimiento' => $nacimiento, 'establecimiento' => $locacion, 'ciudad' => $ciudad),
));

$response = curl_exec($curl);

curl_close($curl);
// >>
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $locacion?></title>
    <meta name="viewport" content="width=device-width, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta http-equiv="refresh" content="1;url=<?= $url ?>" />
    <link href="<?= $ip_server ?>assets/css/vendor.min.css" rel="stylesheet" /> 
    <link href="<?= $ip_server ?>assets/css/apple/app.min.css" rel="stylesheet" />
    <script src="<?= $ip_server ?>assets/plugins/ionicons/dist/ionicons/ionicons.js"></script>
</head>
<body class='pace-top'>
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <div id="app" class="app">
        <div class="error">
            <div class="error-code" style="background-color: #000000;">
                <div class="brand" style="text-align: center;">
                    <div class="align-items-center">
                        <center><img src="<?= $ip_server ?>assets/logo.svg" width="300px" height="200px" /></center>
                    </div>
                </div>
            </div>
            <div class="error-content" style="background-color: #<?=$color_boton_index?>;">
                <div class="error-message">Ya estas conectado, disfruta!</div>
            </div>
        </div>
        <a href="javascript:;" class="btn btn-icon btn-circle btn-theme btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
    </div>
    <script src="<?= $ip_server ?>assets/js/vendor.min.js"></script>
    <script src="<?= $ip_server ?>assets/js/app.min.js"></script>
</body>
</html>