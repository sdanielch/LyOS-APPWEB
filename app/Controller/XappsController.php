<?php

App::uses('AppController', 'Controller');

class XappsController extends AppController
{
	public $layout = "ajax";
	public function index() {
		$this->set('title_for_layout', "Inicio");

	}

	public function controlpanel() {
		$this->loadModel('User');

		$wallpapers = array();

		$ficheros1  = scandir(WWW_ROOT . "users/" . $this->Auth->User('id') . "/Wallpapers");
		if ($ficheros1 != false) {
			foreach ($ficheros1 AS $key => $file) {
				if ($key != 0 && $key != 1) {
					$wallpapers[] = array("nombre" =>$file, "url" => Router::url('/', true) . "users/" . $this->Auth->User('id') . "/Wallpapers/" . $file);
				}
			}
		}

		$this->set("wallpapers", $wallpapers);
	}

	public function wallchange() {
		$this->loadModel('User');
		$this->User->query("UPDATE users SET `theme`='".$_POST['exampleRadios']."'  WHERE  `id`=" . $this->Auth->User('id') . ";");
		$baseapp = Router::url('/', true);
		$userr = $this->Auth->User('id');
		if(isset($_POST['wallpaper'])) {
			$this->User->query("UPDATE users SET `background`='Wallpapers/".$_POST['wallpaper']."'  WHERE  `id`=" . $this->Auth->User('id') . ";");

			echo "
			<script>
			$(function() {
				$('#background').css({
					\"background\": \"#282828 url('".$baseapp. "users/" . $userr . "/Wallpapers/" . $_POST['wallpaper'] . "') center center no-repeat\",
					\"position\": \"fixed\",
					\"top\": 0,
					\"left\": 0,
					\"right\": 0,
					\"bottom\": 0,
					\"z-index\": 1,
					\"background-size\": \"cover\"
					})
					});
					</script>
					";

				}


				if(isset($_FILES['img'])) {
					$subida = $this->Rapid->subir_al_servidor($_FILES, "img", "Wallpapers", $_FILES['img']['name']);
					if ($subida != false) {
						$this->User->query("UPDATE users SET `background`='".$subida['nombre']."'  WHERE  `id`=" . $this->Auth->User('id') . ";");

						echo "
						<script>
						$(function() {
							$('#background').css({
								\"background\": \"#282828 url('".$baseapp. "users/" . $userr . "/" . $subida['nombre'] . "') center center no-repeat\",
								\"position\": \"fixed\",
								\"top\": 0,
								\"left\": 0,
								\"right\": 0,
								\"bottom\": 0,
								\"z-index\": 1,
								\"background-size\": \"cover\"
								})
								});
								</script>
								";
							}

						}
						echo "Cambios realizados";

						exit;
					}

/*
 * Navegador de archivos
 */
public function browser() {
	$this->loadModel('User');

	if(isset($_POST["directorio"])) {

		$directorio =  WWW_ROOT . "users/" . $this->Auth->User('id') . $_POST["directorio"];

		//var_dump($directorio); exit;


		$directorios = array();

		$ficheros1  = scandir($directorio);
		if ($ficheros1 != false) {
			foreach ($ficheros1 AS $key => $file) {
				if ($key != 0 && $key != 1) {
					if(is_dir($directorio . "/" . $file)) {
						$directorios[] = array("name" => $file, "directory" => true);
					} else {
						$directorios[] = array("name" => $file, "directory" => false);
					}
				}
			}
		}
		$base = Router::url('/', true) . "app/webroot/img/";
		$base2 = Router::url('/', true) . "app/webroot/" . "/";
		$directorio2 = $base2 . "users/" . $this->Auth->User('id') . $_POST["directorio"];
		$being = Router::url('/', true);

		$ruta_relativa = "users/" . $this->Auth->User('id') . $_POST["directorio"];

		foreach ($directorios as $key2 => $f) {

			$ruta_relativa2 = "users/" . $this->Auth->User('id') . $_POST["directorio"] . "/" . $f['name'];

			if($f["directory"] == true) {
				echo "<div class='files isfolder' id='EB".$f['name']."' data-name='".$f['name']."' data-relative='".$ruta_relativa2."'>
				<img src='". $base ."folder.png' style='width: 100%;' />
				<br />

				<span>".$f['name']."</span>
				</div>";
			} else {

				$file_ext = ltrim(strstr($f['name'], '.'), '.');
				if ($file_ext == "jpg" || $file_ext == "jpeg" || $file_ext == "JPG" || $file_ext == "JPEG" || $file_ext == "png" || $file_ext == "PNG" || $file_ext == "gif" || $file_ext == "GIF" || $file_ext == "bmp" || $file_ext == "BMP") {

					$imagen = $directorio2 . "/" . $f['name'];

					echo "<div class='files isfile' data-name='".$f['name']."' data-imagen='".$base."image.png' data-relative='".$ruta_relativa2."'>
				<div style='position: absolute; top: 0; left: 0; bottom: 0; right: 0; background: url(\"".$imagen."\") center center no-repeat; background-size: 100% 100%; filter: blur(8px); z-index: -1;'></div>
				<div style='position: absolute; top: 0; left: 0; bottom: 0; right: 0; background: url(\"".$imagen."\") center center no-repeat; background-size: contain; filter: blur(0px); z-index: 0;'></div>


				<span style='position: absolute; bottom: 0; left: 0; white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis; right: 0; z-index: 10; background: rgba(0,0,0,.75); backdrop-filter: blur(8px) saturate(180%);'>".$f['name']."</span>
				</div>";




				} else {

					echo "<div class='files isfile' data-name='".$f['name']."' data-imagen='".$base."file.png' data-relative='".$ruta_relativa2."'>
				<img src='". $base ."file.png' style='width: 100%;' />
				<br />

				<span>".$f['name']."</span>
				</div>";

				}



			}


		}


		// Esto siempre se va a mostrar, es el formulario de subida
		echo "
<div class='js-upload22 uk-placeholder uk-text-center' style='margin-top: 5px;'>
										<span uk-icon='icon: cloud-upload'></span>
										<span class='uk-text-middle'>Arrastra tus archivos al cuadro para subir ficheros a este directorio</span>
										<div uk-form-custom>
											<input type='file' id='slct' name='gallery' accept='image/*,video/*' multiple>
											<input type='hidden' name='directorio' value='".$_POST["directorio"]."' />
										<span class='uk-link' style='display: none'>pincha aquí para seleccionarlos</span>
										</div>
									</div>
									<div id='errores' style='color: #FF0000; display: none; width: 100%; '></div>
									<progress id='js-progressbar22' class='uk-progress' value='0' max='100'
											  hidden></progress>


		<style>
		 .dfile {
		 position: absolute;
		 top: 5px;
		 right: 5px;
		 z-index: 30;
		 background: rgba(200,10,10,0.25);
		 border-radius: 50px;
		 width: 34px;
		 height: 34px;
		 padding: 5px;
		 transition: 300ms;
		 }
		 .dfile:hover, .dfile:active {
		 background: rgba(200,10,10,0.85);
		 }
		</style>

		<script>



		$('.files').append('<div class=\"dfile\"> <i class=\"fas fa-trash-alt\"></i> </div>');
		$('.dfile').on('click', function (e) {
		        e.preventDefault();
			console.warn($(this).parent().attr('data-relative'));

			$.post( '".$being."xapps/deletefile', { fichero: $(this).parent().attr('data-relative') })
			  .done(function( data ) {
				console.warn(data);
				h2 = historial.join(\"/\");
				ira(h2);
			  });

			return false;
		});

		$('#EBWallpapers .dfile').hide();

		var bar22 = document.getElementById('js-progressbar22');

											UIkit.upload('.js-upload22', {

												url: '".$being."xapps/uploadtoserver/',
												multiple: true,
												method: 'POST',
												name: 'gallery',
												mime: 'image/*|video/*|audio/*|*',
												'msg-invalid-mime': 'Tipo de fichero no válido: %s',
												'msg-invalid-name': 'Nombre de fichero no válido: %s',

												beforeSend: function (environment) {
													//console.log('beforeSend', arguments);

													// The environment object can still be modified here.
													// var {data, method, headers, xhr, responseType} = environment;

												},
												beforeAll: function () {
													//console.log('beforeAll', arguments);
												},
												load: function () {
													//console.log('load', arguments);
												},
												error: function () {
													console.log('Error en la subida :' + arguments[0].response);
												},
												complete: function (e) {
													//jacintocasio

													console.log(e);
												},

												loadStart: function (e) {
													//console.log('loadStart', arguments);

													bar22.removeAttribute('hidden');
													bar22.max = e.total;
													bar22.value = e.loaded;
												},

												progress: function (e) {
													//console.log('progress', arguments);

													bar22.max = e.total;
													bar22.value = e.loaded;
												},

												loadEnd: function (e) {
													//console.log('loadEnd', arguments);

													bar22.max = e.total;
													bar22.value = e.loaded;
												},

												completeAll: function (e) {
													//console.log('completeAll', arguments);

													setTimeout(function () {
														bar22.setAttribute('hidden', 'hidden');
														h2 = historial.join(\"/\");
														ira(h2);
													}, 1000);

													//console.log(e);
												},

												fail: function (e) {
													console.log('Alguno de los archivos seleccionados no reunen los requisitos de subida: ' + e);
													$('#errores').html('Alguno de los archivos seleccionados no reunen los requisitos de subida: ' + e).show(300);

												}

											});

		</script>

		";
		/*
		 * Guardamos en sesión el directorio actual
		 */
		$_SESSION['directorio'] = $_POST["directorio"];

		exit;
	}

}


public function uploadtoserver() {
	//var_dump($_SESSION['directorio']);
	/*
	 * Recuperamos el directorio de sesión
	 */
	$subida = $this->Rapid->subir_al_servidor($_FILES, "gallery", $_SESSION['directorio'], $_FILES['gallery']['name']);
	if ($subida != false) {
		echo "OK";
	} else {
		echo "NOP";
	}
	exit;
}

public function deletefile() {
	delete_files(WWW_ROOT . "/" .$_POST['fichero']);
	echo "Fichero eliminado";
	exit;
}



public function fyoutube() {

}


public function newfolder() {
	mkdir(WWW_ROOT . "users/" . $this->Auth->User('id') . $_POST['actual'] . "/" . $_POST['nuevo'], 0777, true);
	exit;
}




}
