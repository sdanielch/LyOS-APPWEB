<?php

App::uses('Component', 'Controller', 'Model');

class RapidComponent extends Component
{
    public $components = array('Session', 'Auth');


    /*
        Esta función se encarga de mandar un mensaje de sesión al usuario de forma personalizada con BootStrap.
        La Forma de uso es, desde cualquier controlador que tenga el componente Rapid cargado, $this->Rapid->AlertFlash("A","B","C")
        Donde "A" es el título del mensaje, "B" el mensaje en cuestión y "C" la clase de bootstrap a usar, por defecto en Danger.
    */
    public function flash($titulo = "Alerta", $mensaje = "Mensaje por defecto", $clase = "danger")
    {
        $flash = <<<EOD
		<div class="rapidflash $clase">
		<h1>$titulo</h1><span>$mensaje</span></div>
EOD;
        /*
        $objeto->titulo = $titulo;
        $objeto->mensaje = $mensaje;
        $objeto->clase = $clase;*/
        $objeto = array(
            "titulo" => $titulo,
            "mensaje" => $mensaje,
            "clase" => $clase
        );
        $myjson = json_encode($objeto);

        //$this->Session->setFlash(__($myjson));
        $this->Session->setFlash($myjson, 'flashjson');
    }


    public function var_dump($data)
    {
        highlight_string("<?php\n " . var_export($data, true) . "?>");
        echo '<script>document.getElementsByTagName("code")[0].getElementsByTagName("span")[1].remove() ;document.getElementsByTagName("code")[0].getElementsByTagName("span")[document.getElementsByTagName("code")[0].getElementsByTagName("span").length - 1].remove() ; </script>';
        die();
    }




    // Esta función se encarga de saber el espacio consumido en un directorio (sin subdirectorios)
	public function tdirectory($path){
		$bytestotal = 0;
		$path = realpath($path);
		if($path!==false && $path!='' && file_exists($path)){
			foreach(new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)) as $object){
				$bytestotal += $object->getSize();
			}
		}
		return $bytestotal;
	}

	// Formatea una unidad en bruto a formato "humano"
	public function format_size($size) {
		$mod = 1024;
		$units = explode(' ','B KB MB GB TB PB');
		for ($i = 0; $size > $mod; $i++) {
			$size /= $mod;
		}

		return round($size, 2) . ' ' . $units[$i];
	}

    public function escape($text)
    {
        $tags = "/&lt;(\/|)(\w*)(\ |)(\w*)([\\\=]*)(?|(\")\"&quot;\"|)(?|(.*)?&quot;(\")|)([\ ]?)(\/|)&gt;/i";
        $replacement = "<$1$2$3$4$5$6$7$8$9$10>";
        $text = preg_replace($tags, $replacement, $text);
        $text = htmlspecialchars($text);
        $text = preg_replace("/=/", "=\"\"", $text);
        $text = preg_replace("/&quot;/", "&quot;\"", $text);
        $text = preg_replace("/=\"\"/", "=", $text);
        return $text;
    }

    public function youtube_id($url)
    {
        if (stristr($url, 'youtu.be/')) {
            preg_match('/(https:|http:|)(\/\/www\.|\/\/|)(.*?)\/(.{11})/i', $url, $final_ID);
            return $final_ID[4];
        } else {
            @preg_match('/(https:|http:|):(\/\/www\.|\/\/|)(.*?)\/(embed\/|watch.*?v=|)([a-z_A-Z0-9\-]{11})/i', $url, $IDD);
            return $IDD[5];
        }
    }

    public function colordump($var)
    {
        echo '<pre style="text-align:left">';
        highlight_string("<?php\n\$data =\n" . var_export($var, true) . ";\n?>");
        echo '</pre>';
    }

    // DEVUELVE EN UTF-8 UNA CADENA SI PREVIAMENTE NO ES UTF-8
    public function codificacion($content)
    {
        if (!mb_detect_encoding($content, 'UTF-8', true)) {
            return utf8_encode(strip_tags($content));
        } else {
            return strip_tags($content);
        }
    }

    public function redirect($url, $permanent = false)
    {
        if (headers_sent() === false) {
            header('Location: ' . $url, true, ($permanent === true) ? 301 : 302);
        }
        exit();
    }


    public function averagecolor($img)
    {
        $i = imagecreatefromjpeg($img);
        for ($x = 0; $x < imagesx($i); $x++) {
            for ($y = 0; $y < imagesy($i); $y++) {
                $rgb = imagecolorat($i, $x, $y);
                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;
                $rTotal += $r;
                $gTotal += $g;
                $bTotal += $b;
                $total++;
            }
        }
        $rAverage = round($rTotal / $total);
        $gAverage = round($gTotal / $total);
        $bAverage = round($bTotal / $total);
        return $rAverage . "," . $gAverage . "," . $bAverage;
    }

    public function uialert($titulo, $mensaje, $tipo = "info")
    {

        if ($tipo == "info") {
            $estilo = "background: #0062c3;  margin: -30px;  margin-bottom: 0; padding: 0 15px; color: #FFF;";

        } else if ($tipo == "error") {
            $estilo = "background: #e02d2d;  margin: -30px;  margin-bottom: 0; padding: 0 15px; color: #FFF;";

        } else {
            $estilo = "background: #2f2f2f;  margin: -30px;  margin-bottom: 0; padding: 0 15px; color: #FFF;";
        }
        $alerta = <<<EOD
		<button class="uk-button uk-button-default uk-margin-small-right" type="button" uk-toggle="target: #modal-close-default" style="display: none;" id="alerting">Errores</button>

<!-- This is the modal with the default close button -->
<div id="modal-close-default" class="uk-flex-top"  uk-modal style="backdrop-filter: blur(10px); ">
    <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical2" style="border-radius: 4px; background: rgba(255,255,255,0.8); overflow: auto; box-shadow: 0px 5px 20px 0px #000;">
        <button class="uk-modal-close-default" type="button" style="color: #FFFFFF;" uk-close></button>
        <h2 class="uk-modal-title" style="$estilo">$titulo</h2>
        <p>$mensaje.</p>
    </div>
</div>
<script language="JavaScript">
$(function() {
    $("#alerting").click();

});
</script>
EOD;

        $this->Session->setFlash(__($alerta));
    }




public function dd($array){
echo highlight_string("<?php\n\$data =\n" . var_export($array, true) . ";\n?>");
}


/** COMO SUBIR FICHEROS AL SERVIDOR CON ESTA FUNCION
* $file = $_FILE
* $fname = campo "name" del file que queremos tratar
* $subdirectorio = $this->Auth->User('id'); // NORMALMENTE LA ID DEL USUARIO EN CUESTION
* $name = prefijo del fichero.
* AVISO: ESTA FUNCION NO GUARDA NADA EN BASE DE DATOS, PERO DEVUELVE EL NOMBRE DEL FICHERO EN CASO DE HABERSE SUBIDO, SI DEVUELVE
* FALSE QUIERE DECIR QUE ALGO FALLÓ A MITAD DE SUBIDA. CON ESTO SE HA DE TRATAR UN IF Y MANDAR LA INFORMACIÓN DEVUELTA A LA BD.
*/

public function subir_al_servidor($files, $fname, $subdirectorio = "Ficheros", $name = "") {

        // Cargamos la librería que nos permite conocer la extensión según el tipo de archivo

        $mimes = new \Mimey\MimeTypes;

        // Según su mimetype capturamos su extensión
        $extension_real = $mimes->getExtension($files[$fname]['type']);



            $target_dir = WWW_ROOT . "users/" . $this->Auth->User('id') . "/" . $subdirectorio . "/"; //DIRECTORIO DE SUBIDA

        // COMPROBAMOS EL FORMATO PARA LUEGO GUARDAR SU EXTENSION

                $ftype = "." . $extension_real;
                $fide = $extension_real;

        // DESTINO DEL FICHERO
            $nomfichero = $name;
            $target_file = $target_dir . $nomfichero; //FICHERO
            $uploadOk = 1;
            $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // Comprobamos si el subdirectorio de usuario existe
			$udir = WWW_ROOT . "users/" . $this->Auth->User('id');
            $dir = WWW_ROOT . "users/" . $this->Auth->User('id') . "/" . $subdirectorio;
	if (!file_exists($udir)) {
		mkdir($udir, 0777, true);
	}
	if (!file_exists($dir)) {
		mkdir($dir, 0777, true);
	}

        // Comprobamos si el fichero existe
            if (file_exists($target_file)) {
                $uploadOk = 0;
            }
        // Comprobamos el tamaño del fichero
            if ($files[$fname]["size"] > 50000000) {
                $uploadOk = 0;
            }

        // Comprobamos si $uploadOk esta a 0
        if ($uploadOk == 0) {
            return FALSE;
        // Si todo esta OK intentamos subir el fichero.
        } else {

            if (move_uploaded_file($files[$fname]["tmp_name"], $target_file)) {

				// SI SE HA SUBIDO AL SERVIDOR Y SE TRATA DE UN FICHERO DE SONIDO TRATAMOS DE CONVERTIRLO A UN FORMATO ABIERTO OGG CON FFMPEG

            	if ($ftype == ".mp3" || $ftype == ".wav" || $ftype == ".wma" || $ftype == ".opus") {
            		$randn = rand() . rand();
                    chdir($target_dir); // Nos movemos al repositorio
                    shell_exec('ffmpeg -v 10 -y -i '. $target_file .' -acodec libvorbis ' . $target_dir . $randn .'.ogg'); // Convertimos el fichero
                    unlink($target_file); // Borramos el fichero original
                    chdir("/mnt/TB/www/html"); // Volvemos al directorio original
                    return array("nombre" => $subdirectorio . "/" . $randn.".ogg", "formato" => $fide, "miniatura" => $target_dir . $randn .'.ogg');
                }


                // SI ES UN FORMATO DE VIDEO MP4 CAPTURAMOS UNA IMAGEN DE PORTADA

				else if($ftype == ".mp4" || $ftype == ".avi" || $ftype == ".wmv") {


					/*$randn = $name . rand() . rand();
					$commands = array(
						'ffmpeg -i "'. $target_file .'" -ss 00:00:05.000 -vframes 1 "'. $target_dir . $randn . '.png"',
					);
					$output = '';
					foreach($commands AS $command){
						$tmp = shell_exec($command);
						$output .= htmlentities(trim($tmp)) . "<br />";
					}*/
					return array("nombre" => $subdirectorio . "/" . $nomfichero, "formato" => $fide);
				}


                // SI ES UN FORMATO DE IMAGEN CAPTUAMOS UNA MINIATURA

                else if ($ftype == ".jpg" || $ftype == ".jpeg" || $ftype == ".gif" || $ftype == ".png" || $ftype == ".bmp") {

					/*$randn = $name . rand() . rand();
					$commands = array(
						'ffmpeg -i "'. $target_file .'" -q:v 1 -vf scale="640:-1" "'. $target_dir . $randn . $ftype .'"',
					);
					$output = '';
					foreach($commands AS $command){
						$tmp = shell_exec($command);
						$output .= htmlentities(trim($tmp)) . "<br />";
					}*/
					return array("nombre" => $subdirectorio . "/" . $nomfichero, "formato" => $fide);

				}


                // SI NO COINCIDE CON LOS FORMATOS PERMITIDOS DEVOLVEMOS UN ERROR (FALSE)

                else {
					return false;
				}


            } else {

                // SI NO SE HA PODIDO SUBIR DEVOLVEMOS UN ERROR (FALSE)
                return false;

            }
        }

    }


	public function comentarios($url = NULL) {
		if ($url == NULL) { $url = $_SERVER['REQUEST_URI']; }
		$model = ClassRegistry::init('User');
		$comentarios = $model->get_comentarios($url);
		$arraydecomentarios = array();
		foreach ($comentarios as $key => $com) {
			$arraydecomentarios[] = array("nombre" => $com['uc']['dni'],
				"apellidos" => "",
				"foto" => Router::url('/', true)   . "app/webroot/users/". $com['com']['usuario'] . "/" .$com['uc']['fotoperfil'],
				"fecha" =>$com['com']['date'],
				"mensaje" => json_decode($this->codificacion($com['com']['msg'])),
				"idn" => rand() . rand(),
				"uid" => $com['u']['id'],
				"idmsg" => $com['com']['id'] );

		}

		$liger =  "<br /><br />" . count($arraydecomentarios) . " <span class='jpp'>comentarios</span>. <br /><br /><div class='comentarios' style='

	overflow: auto;
    max-height: 300px;
    display: block;
    background: white;
    border: 0px #18222b solid;
    border-radius: 6px;
    padding-right: 10px;

'>";
		$liger = $liger .  "<div class='comentarius'>";
		$liger = $liger .  "<style>
                .over {
                    position: relative;
                    width: 64px;
                    height:  64px;
                    background-color: #111;
                    background-repeat: no-repeat;
                    background-position: center center;
                    -webkit-background-size: contain;
                    -moz-background-size: contain;
                    -o-background-size: contain;
                    background-size: contain;
                    border-radius: 100% !important;
                    display: inline-block;
                    overflow: hidden;

                }
                .capa1 {
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background-color: #000;
                    background-repeat: no-repeat;
                    background-position: center center;
                    -webkit-background-size: 100% 100% ;
                    -moz-background-size: 100% 100% ;
                    -o-background-size: 100% 100% ;
                    background-size: 100% 100% ;
                    filter: blur(4px);
                    display: inline-block;
                    border-radius: 100% !important;
                }

                .capa2 {
                    position: absolute;
                    top: 0; left: 0; right: 0; bottom: 0;
                    background-color: transparent;
                    background-repeat: no-repeat;
                    background-position: center center;
                    -webkit-background-size: contain;
                    -moz-background-size: contain;
                    -o-background-size: contain;
                    background-size: contain;
                    border-radius: 100%;
                    display: inline-block;

                }

                @media only screen and (max-width: 767px) {
                    .ui.table:not(.unstackable) tr {
                        padding-top: 0px !important;
                        padding-bottom: 0px !important;

                        box-shadow: 0 -1px 0 0 rgba(0,0,0,.1) inset!important;
                    }
                }

                @media only screen and (max-width: 767px) {
                    .ui.table:not(.unstackable) tr>td, .ui.table:not(.unstackable) tr>th {
                        background: 0 0;
                        border: none!important;
                        padding: 0!important;
                        -webkit-box-shadow: none!important;
                        box-shadow: none!important;
                    }
                }

                .det1 {
                    border-radius: 0 !important;
                }


                .ui.basic.table tbody tr {
                    border: 1px #CCC solid;
                    border-bottom: 10px #444 solid;
                   /* border-top: 1px #CCC solid; */
                }

                                .ui.basic.table {
                    background: 0 0;
                     border: 0px solid rgba(34,36,38,.15);
                    -webkit-box-shadow: none;
                    box-shadow: none;
                }

                </style>";
		foreach ($arraydecomentarios as $key => $com) {

			$fechacomentario = new DateTime($com['fecha']);

			$mensaje = str_replace($nodavi."ytb", "", $com['mensaje']);
			$mensaje = str_replace("ytbhtt", "", $mensaje);

			$regex = "@(https?://([-\w\.]+[-\w])+(:\d+)?(/([\w/_\.#-]*(\?\S+)?[^\.\s])?).*$)@";


			$lkasjd = $this->youtube_id($mensaje);


			if (isset($lkasjd)) {
				$deiva = "<iframe style='width: 90%; max-width: 500px; height: 140px;' src=\"https://www.youtube.com/embed/".$lkasjd."\" frameborder=\"0\" allow=\"accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture\" allowfullscreen></iframe>";
			} else {
				$deiva = "";
			}



			$mensaje = preg_replace($regex, ' ', $mensaje);





			$role = $this->Auth->User('role');

			// idmsg
			if ($role == "admin") {
				$role_html = "<div class='removecomentario' data-com='".$com['idmsg']."'> <i class=\"fas fa-trash-alt\"></i> </div>


                        <script>
                                            jQuery(function($) {

                        $('.removecomentario').off().on('click', function(e) {
                            var comentario = $(this).parent().parent();
                             $.get( '".Router::url('/', true)."admin/borrarcomentario/".$com['idmsg']."', function() {
                                  console.log('Llamada completada');
                                })
                                  .done(function() {
                                    console.log( 'Comentario borrado correctamente' );
                                    comentario.hide(300);
                                  })
                                  .fail(function() {
                                    alert( 'Ocurrió un error al establecer la comunicación con el controlador' );
                                  })
                                  .always(function() {
                                    console.log( 'Comando finalizado' );
                                  });

                        });
                                            });
                        </script>

                        ";
			} else {
				$role_html = "";
			}



			//$mensaje = preg_replace('/'.$nodavi.'ytb/i', '', $com['mensaje']);

			$liger = $liger .  "<div style='border: 1px #151515 solid;  margin-bottom: 10px; border-radius: 6px; overflow: hidden;'>


					<div class='name' style='background: #151515; color: #FFF;  display: flex;
  align-items: center;
  justify-content: flex-start;
  border-bottom: 2px #bf3636 solid;'>

                    <div class='over det1' style='border-radius: 100% !important; margin-right: 10px; width: 32px; height: 32px;'><div class='capa1' style='background-image: url(\"".$com['foto']."\")'></div>
						<div class='capa2' style='background-image: url(\"".$com['foto']."\")'></div>
					</div>


                    ". $com['nombre'] . " " . $com['apellidos'] . $role_html . "</div>
                    <div style='display:block; padding: 20px; box-sizing: border-box ;   word-wrap: break-word;
 background: #EEE'>".$mensaje .  $deiva . "







                    </div>
                    <div style='display:block; font-size: 12px; text-align: right; background: #151515; color: #FFF;'>

                    <span class='redon2s' style='margin-right: 10px'>".$fechacomentario->format('d/m/Y H:i')."</span></div>
                    </div>";

		}
		$liger = $liger .  "</div></div>";



			$variabletipo = false;


		$comi = rand() . rand();




		$formulario = "
                    <br /><form method=\"post\" action=\"". Router::url('/', true) . "servicios/comenta\">
                    <input type=\"hidden\" name=\"dato\" />
                    <input type=\"hidden\" name=\"url\" value=" . $url . " />



                    <script>
                    jQuery(function($) {
                    function dave() {
                        console.log(this)
                        var person = prompt(\"Video de Youtube\", \"Inserta aquí la URL de youtube\");
                        if (person != null) {
    						// PERSON ES LA RESPUESTA!!
    						$('.".$comi."').val($('.".$comi."').val() + ' ytb'+person+'ytb  ');
    						$('.btnyoutube').hide();
  						}
                    }

                    $(\".".$comi."\").emojioneArea({
					  hideSource: true,
					});
                    });


                    </script>
                    ";


		//<textarea name=\"texto\" required=\"required\" style='border-radius: 6px 6px 0 0; min-height: 100px;' placeholder=\"Escribe aquí tu comentario :)\" class=\"uk-textarea\"></textarea>


		if($variabletipo == "txt") {
			// EN CASO DE QUERER ESTAR CONTINUANDO UN PROYECTO DE TEXTO
			$formulario = $formulario . "<textarea name=\"texto\" required=\"required\" style='border-radius: 6px 6px 0 0; min-height: 100px;' placeholder=\"Continua con la historia...\" class=\" ".$comi."\"></textarea>";
		} else {
			// EN OTROS CASOS
			$formulario = $formulario . "<textarea name=\"texto\" required=\"required\" style='border-radius: 6px 6px 0 0; min-height: 100px;' placeholder=\"Escribe aquí tu comentario :)\" class=\"uk-textarea ".$comi."\"></textarea>";
		}



		$formulario = $formulario . "

					<div class=\"uk-button-group uk-width-1\">
				<input type=\"submit\" value=\"Enviar\" class=\"uk-button uk-button-primary uk-width-1\" style='border-radius: 0 0 0 6px;' uk-tooltip=\"Enviar sin video de YouTube Incrustado\" />
				<button class=\"uk-button uk-button-secondary btnyoutube\" onclick='dave(this)' style='border-radius: 0 0 6px 0; background: #d83e3e;' uk-tooltip=\"Enviar con video de YouTube\"><i class=\"fas fa-video\"></i></button>
				</div>
                </form><br /><br />
                ";



		$is_completed = $model->test_registrado($this->Auth->User('id')); // Comprobamos si el usuario EN SESION esta registrado
		if (empty($is_completed)) {
			return $liger;
		} else {
			$liger = $liger . $formulario;
			return $liger;
		}





	}


	public function panelSections() {
		$base = Router::url('/', true);
	$array = array(
			"<i class=\"fas fa-house-user\"></i> Inicio" => $base . "Panel",
			"<i class=\"fas fa-route\"></i> Hacer ruta" => $base . "panel/goroute",
			"<i class=\"fas fa-layer-group\"></i> Configurar API" => $base . "panel/setapi",
			"<i class=\"fas fa-road\"></i> Trayectos" => $base . "panel/trayectos",
			"<i class=\"fas fa-user-circle\"></i> Mi cuenta" => $base . "panel/micuenta"
			//"<i class=\"fas fa-chart-line\"></i> Estadisticas" => $base . "panel/estadisticas"

		);

	return $array;
	}





}


