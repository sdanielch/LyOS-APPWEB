<?php
// Desactivar toda notificación de error
error_reporting(0);
// Notificar solamente errores de ejecución
error_reporting(E_ERROR | E_WARNING | E_PARSE);
define("MAX_RESULTS", 15);
    
     if (isset($_POST['submit']) )
     {
        $keyword = $_POST['keyword'];
               
        if (empty($keyword))
        {
            $response = array(
                  "type" => "error",
                  "message" => "Por favor ingrese la palabra clave."
                );
        } 
    }
         
?>
<!doctype html>
<html lang="es">
        <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
       

        <!-- Bootstrap core CSS -->
        <link href="dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Custom styles for this template -->
        <link href="assets/sticky-footer-navbar.css" rel="stylesheet">
        <style>
.videos-data-container {
	background: rgba(255,255,255,0.2);
	border: #282828 1px solid;
	padding: 1px;
	border-radius: 2px;
}
.response {
	padding: 10px;
	margin-top: 10px;
	border-radius: 2px;
}
.error {
	background: #fdcdcd;
	border: #ecc0c1 1px solid;
}
.success {
	background: #c5f3c3;
	border: #bbe6ba 1px solid;
}
.result-heading {
	margin: 10px 0px;
	padding: 10px 10px 5px 0px;
	border-bottom: #e0dfdf 1px solid;
}
iframe {
	border: 0px;
}
.video-tile {
	display: inline-block;
	margin: 10px 10px 20px 10px;
}
.videoDiv {
	width: 240px;
	height: 150px;
	display: inline-block;
}
.videoTitle {
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
}
.videoDesc {
	text-overflow: ellipsis;
	white-space: nowrap;
	overflow: hidden;
}
.videoInfo {
	width: 240px;
}
body {background-color: rgba(0,0,0,0.8); color: #EFEFEF;}
</style>
        </head>

        <body>


<!-- Begin page content -->

<div class="container">
          <h3 class="mt-5">YouTube</h3>
          <hr>
          <div class="row">
    <div class="col-12 col-md-12">
    <!-- Contenido -->
    
    <form id="keywordForm" method="post" action="">
              <div class="form-row align-items-center">
        <div class="col-auto"> Buscar vídeo: </div>
        <div class="form-row align-items-center">
                  <div class="col-auto">
            <input class="form-control mb-2" type="search" id="keyword" name="keyword"  placeholder="Ingrese palabra a buscar">
          </div>
                  <div class="col-auto">
            <input class="btn btn-danger mb-2" type="submit" name="submit" value="Busqueda">
          </div>
                </div>
      </div>
              </div>
            </form>
    <?php if(!empty($response)) { ?>
    <div class="response <?php echo $response["type"]; ?>"> <?php echo $response["message"]; ?> </div>
    <?php }?>
    <?php
            if (isset($_POST['submit']) )
            {
                                         
              if (!empty($keyword))
              {
			  $apikey = 'AIzaSyC17uD7yboTlRlgxomUsMqn-c9T7R_y_vE';
              $googleApiUrl = 'https://www.googleapis.com/youtube/v3/search?part=snippet&q='.$keyword.'&maxResults='.MAX_RESULTS.'&key='.$apikey;

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_HEADER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
                curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch, CURLOPT_VERBOSE, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($ch);

                curl_close($ch);
                $data = json_decode($response);
                $value = json_decode(json_encode($data), true);
            ?>
    <div class="result-heading">Acerca de <?php echo MAX_RESULTS; ?> Resultados</div>
    <div class="videos-data-container" id="SearchResultsDiv">
              <?php
                for ($i = 0; $i < MAX_RESULTS; $i++) {
                    $videoId = $value['items'][$i]['id']['videoId'];
                    $title = $value['items'][$i]['snippet']['title'];
                    $description = $value['items'][$i]['snippet']['description'];
                    ?>
              <div class="video-tile">
        <div  class="videoDiv">
                  <iframe id="iframe" allowFullScreen="allowFullScreen" style="width:100%;height:100%" src="https://www.youtube.com/embed/<?php echo $videoId; ?>" data-autoplay-src="https://www.youtube.com/embed/<?php echo $videoId; ?>?autoplay=1&allowfullscreen=yes&fullscreen=yes"></iframe>
                </div>
        <div class="videoInfo">
                  <div class="videoTitle"><b><?php echo $title; ?></b></div>
                  <div class="videoDesc"><?php echo $description; ?></div>
                </div>
      </div>
              <?php 
                    }
                } 
           
            }
            ?>
              
              <!-- Fin Contenido --> 
            </div>
  </div>
          <!-- Fin row --> 
          
        </div>
<!-- Fin container -->

<script src="assets/jquery-1.12.4-jquery.min.js"></script> 
<script src="assets/jquery.validate.min.js"></script> 
<script src="assets/ValidarRegistro.js"></script> 
<!-- Bootstrap core JavaScript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 

<script>window.jQuery || document.write('<script src="assets/js/vendor/jquery-slim.min.js"><\/script>')</script> 
<script src="assets/js/vendor/popper.min.js"></script> 
<script src="dist/js/bootstrap.min.js"></script>
</body>
</html>