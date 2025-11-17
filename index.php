<?php
// VARIABLES INICIALES
session_start();
$_SESSION["locacion"] = $locacion = "Houdinni";
$_SESSION["ciudad"] = $ciudad = 'MAD';

// --- NUEVA LÓGICA DE CACHÉ ---
$cache_file = 'config_cache.json';
$cache_lifetime = 300; // 300 segundos = 5 minutos

$response_data = null;

// 1. Verificar si el archivo de caché existe y es válido
if (file_exists($cache_file) && (time() - filemtime($cache_file) < $cache_lifetime)) {
    // Leer desde el caché
    $response_data = json_decode(file_get_contents($cache_file));
} else {
    // 2. Si no hay caché o ha expirado, hacer la llamada cURL
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://mandalawebservices.com/hotspot/configuration.php',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 5, // Aumenta el timeout para evitar esperas infinitas
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => array('locacion' => $locacion, 'ciudad' => $ciudad),
    ));

    $curl_response = curl_exec($curl);
    $response_data = json_decode($curl_response);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    curl_close($curl);

    // 3. Escribir el resultado al caché solo si la llamada fue exitosa (código 200)
    if ($http_code == 200 && $response_data) {
        file_put_contents($cache_file, $curl_response);
    }
}

// 4. Asignar variables de sesión y locales a partir de los datos obtenidos (de API o caché)
if ($response_data) {
    $_SESSION["ip_server"] = $ip_server = $response_data->ip_server;
    $_SESSION["url"] = $response_data->url;
    $_SESSION["duration"] = $response_data->duration;
    $_SESSION["controllerversion"] = $response_data->controllerversion;

    $_SESSION["site_id"] = $response_data->site_id;
    $_SESSION["controlleruser"] = $response_data->controlleruser;
    $_SESSION["controllerpassword"] = $response_data->controllerpassword;
    $_SESSION["controllerurl"] = $response_data->controllerurl;
    $_SESSION["color_boton_index"] = $response_data->color_boton_index;

    // Asegúrate de usar $response_data de ahora en adelante
    $response = $response_data;
} else {
    // Manejo de errores: Si la API o el caché fallan
    // Puedes redirigir a una página de error o usar valores por defecto
    die("Error al obtener la configuración inicial. Por favor, inténtelo de nuevo más tarde.");
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?= $locacion ?></title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    
    <link href="/assets/css/vendor.min.css" rel="stylesheet" />
    <link href="/assets/css/apple/app.min.css" rel="stylesheet" />
    <script src="/assets/plugins/ionicons/dist/ionicons/ionicons.js"></script>
    <script src="/assets/js/jquery-3.7.1.min.js"></script> 
    </script>
</head>
<body class='pace-top'>
    <div id="loader" class="app-loader">
        <span class="spinner"></span>
    </div>
    <div id="app" class="app">
        <div class="login login-v2 fw-bold">
            <div class="login-cover">
                <div class="login-cover-img" style="background-color: #<?=$response->background_color?>;" data-id="login-cover-image"></div>
                <div class="login-cover-bg"></div>
            </div>
            <div class="login-container">
                <div class="login-header">
                    <div class="brand" style="text-align: center;">
                        <div class="align-items-center">
                            <center><img src="/assets/logo.svg" width="300px" height="200px" /></center>
                        </div>
                        <small>Accede a nuestra WiFi</small>
                    </div>
                </div>
                <div class="login-content">
                    <form method="post" name="form_name" id="form_id" action="http://<?= $ip_server ?>/connecting.php">
                        <div class="form-floating mb-20px">
                            <input type="text" class="form-control fs-13px h-45px border-0" placeholder="Nombre" name="nombre" required />
                            <label for="nombre" class="d-flex align-items-center text-gray-600 fs-13px">Nombre</label>
                        </div>
                        <div class="form-floating mb-20px">
                            <input type="email" class="form-control fs-13px h-45px border-0" placeholder="Email" name="email" required />
                            <label for="nombre" class="d-flex align-items-center text-gray-600 fs-13px">Email</label>
                        </div>
                        <div class="form-floating mb-20px">
                            <div class="row">
                                <label for="nombre" class="d-flex align-items-center text-gray-600 fs-13px mb-10px">Fecha de nacimiento</label>
                              <div class="col">
                                <input type="number" name= "dia" class="form-control fs-13px h-50px border-0" placeholder="Día" required>
                              </div>
                              <div class="col">
                                <select class="form-control fs-13px h-50px border-0" name="mes">
                                    <option value="01">Enero</option>
                                    <option value="02">Febrero</option>
                                    <option value="03">Marzo</option>
                                    <option value="04">Abril</option>
                                    <option value="05">Mayo</option>
                                    <option value="06">Junio</option>
                                    <option value="07">Julio</option>
                                    <option value="08">Agosto</option>
                                    <option value="09">Septiembre</option>
                                    <option value="10">Octubre</option>
                                    <option value="11">Noviembre</option>
                                    <option value="12">Diciembre</option>
                                </select>
                               <input type="text" name="id" value="<?=$_GET["id"]?>" hidden>
                                <input type="text" name="ap" value="<?=$_GET["ap"]?>" hidden>

                              </div>
                              <div class="col">
                                <select class="form-control fs-13px h-50px border-0" name="anio">
                                    <?php 
                                        if ($response->tipo == 'R') { //RESTAURANTE
                                            for ($i=2010; $i >= 1970 ; $i--) { 
                                                echo '<option value="' . $i . '">' . $i . '</option>';
                                            } 
                                        }else if ($response->tipo == 'D') { //DISCO
                                            for ($i=date('Y') - 18; $i >= 1970 ; $i--) { 
                                                echo '<option value="' . $i . '">' . $i . '</option>';
                                            } 
                                        }
                                    ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-floating mb-10px mt-10px">
                                <input type="checkbox" checked class="" style="margin-top: 10px;" name="terms" required />
                                <label for="terms" style="margin-left: 10px;" class="d-flex align-items-center text-gray-600 fs-13px">Acepto los Terminos y Condiciones</label>
                            </div>
                        </div>
                        <h4 id="text_espere" style="display: none;">Espere un momento por favor...</h4>
                        <div id="class_btn" class="mb-20px">
                            <button id="boton" class="btn d-block w-100 h-50px btn-lg" style="background-color:#<?=$response->color_boton_index?>; color:#<?=$response->color_boton_texto?>">Obtener WiFi</button>
                            
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <a href="javascript:;" class="btn btn-icon btn-circle btn-theme btn-scroll-to-top" data-toggle="scroll-to-top"><i class="fa fa-angle-up"></i></a>
    </div>
    <script>
        $(document).ready(function(){

            $("#boton").click(function(e){
                if ($("[name=nombre]").val() == '') {
                    alert('Nombre');
                }else if ($("[name=email]").val() == '') {
                    alert('Email');
                }else if ($("[name=dia]").val() == '') {
                    alert('Día');
                }else if (!$("[name=terms]").is(":checked")) {
                    alert('Debe aceptar los terminos y condiciones');
                }else{
                    $("#boton").attr('disabled', true);
                    $("#class_btn").hide();
                    $("#text_espere").css({'display':''});
                    $("#form_id").submit();

                    // window.open('<?= $ip_server ?>connecting.php/?nombre=' + $("[name=nombre]").val() + '&email=' + $("[name=email]").val() + '&dia=' + $("[name=dia]").val() + '&mes=' + $("[name=mes]").val() + '&anio=' + $("[name=anio]").val() ,'_self');
                }
                });

        })
    </script>
    <script src="/assets/js/vendor.min.js"></script>
    <script src="/assets/js/app.min.js"></script>
    <script src="/assets/js/demo/login-v2.demo.js"></script>
</body>
</html>