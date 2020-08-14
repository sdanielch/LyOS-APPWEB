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
		$base = Router::url('/', true) . "app/webroot/img/" . "/";

		foreach ($directorios as $key2 => $f) {
			
			if($f["directory"] == true) {
				echo "<div class='files isfolder' data-name='".$f['name']."'>
			<img src='". $base ."folder.png' style='width: 100%;' />
			<br />

			<span>".$f['name']."</span>
			</div>";	
		} else {

				echo "<div class='files isfile' data-name='".$f['name']."'>
			<img src='". $base ."file.png' style='width: 100%;' />
			<br />

			<span>".$f['name']."</span>
			</div>";

			}
			

		}

		exit;
	}

}
}
