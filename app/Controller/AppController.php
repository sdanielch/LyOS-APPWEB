<?php
//session_start();

//require('../../app/Vendor/vendor/autoload.php');
require APP . DS . 'Vendor' . DS . 'vendor' . DS . 'autoload.php';


App::uses('Controller', 'Controller');

class AppController extends Controller
{

	public $components = array(
		'Session', 'Rapid', 'DebugKit.Toolbar',
		'Auth' => array(
			'loginRedirect' => array('controller' => 'Lovers', 'action' => 'index'),
			'logoutRedirect' => "/",
			'authenticate' => array(
				'Form' => array(
					'passwordHasher' => 'Blowfish'
				)
			),
			'authorize' => array('Controller')
		)
	);

	public function isAuthorized($user)
	{
		if (isset($user['role']) && $user['role'] === 'admin') {
			return true;
		}
		return false;
	}

	public function beforeFilter()
	{


		//var_dump(Configure::read('correo_dominio')); exit;


		// Calculamos la ruta de la APP (Esto nos va a servir luego para las imagenes y los menus)
		$baseapp = Router::url('/', true);

		//echo $baseapp; exit;
		// Comprobamos si hay un idioma en sesión
		if ($this->Session->read('idioma2') == NULL) {
			// Si no lo hay establecemos Español de España por defecto
			$this->Session->write('idioma2', 'es-ES');
		}

		/* CONTROL DE ACCESOS SIN LOGUIN POR CONTROLADOR */
		switch ($this->name) {
			case 'Users':
				$this->Auth->allow('add', 'logout', 'captcha', 'i10n', 'activar', 'pruebas', 'login','facebookcb', 'eaccount', 'pruebass', 'authgoogle');
				break;
			case 'Lovers':
				$this->Auth->allow('search');
				break;
			case 'Pages':
				$this->Auth->allow('cookies', 'pdatos', 'privacidad', 'avisolegal');
				break;
			case 'Web':
				$this->Auth->allow('index', 'entry', 'rapidtest', 'davinci', 'precios');
				break;
			case 'Apis':
				$this->Auth->allow('songkick', 'ticketmaster', 'tv', 'weather', 'imgweather', 'google');
				break;
			case 'General':
				$this->Auth->allow();
				break;
			case 'Forum':
				$this->Auth->allow('index', 'hilo');
				break;
		}

		/* Fix para dar acceso a los que SI estan logueados y de paso mandarle variables que no disponen los usuarios sin sesión */

		$testmdt = false;


		$this->loadModel('User');




		$is_completed2 = null;

		if (!empty($this->Auth->user())) {
			$this->Auth->allow();
			$this->set("menu", $this->Rapid->panelSections());
			$gcloudapi = $this->User->query("SELECT * FROM users WHERE id=" . $this->Auth->User('id'));
			$this->set("apigcloud", $gcloudapi);
			$this->set("usertype", $gcloudapi[0]['users']['cpd']);
			$this->set("userref", $gcloudapi[0]['users']['referido']);
			$this->set("wallpaper", $baseapp . "users/" . $this->Auth->User('id') . "/" . $this->User->query("SELECT * FROM users WHERE id=" . $this->Auth->User('id'))[0]['users']['background']);
			$this->set("estilo", $this->User->query("SELECT * FROM users WHERE id=" . $this->Auth->User('id'))[0]['users']['theme']);

			//debug($this->User->query("SELECT * FROM users WHERE id=" . $this->Auth->User('id')));
			//debug($baseapp . "users/" . $this->Auth->User('id') . "/" . $this->User->query("SELECT * FROM users WHERE id=" . $this->Auth->User('id'))[0]['users']['background']); exit;
			if($gcloudapi[0]['users']['apigcloud'] == "" || $gcloudapi[0]['users']['apigcloud'] == NULL) {
				$this->set("apicompleted", false);
			} else {
				$this->set("apicompleted", true);
			}




			$is_completed = $this->User->test_registrado($this->Auth->User('id')); // Comprobamos si el usuario EN SESION esta registrado
			if (empty($is_completed)) {
				$is_completed2 = false; // Devuelve false si el usuario no esta registrado del todo
				$pfoto = $baseapp . "img/wall.jpg";
			} else {
				// En caso de contener información se devuelve la información del usuario, es fácil el IF con el FALSE
				$is_completed2 = $is_completed;

				// FUNCION PARA DEVOLVER LA IMAGEN DE PERFIL DEL USUARIO
				$ppfoto = $is_completed[0]['uc']['fotoperfil'];
				if (preg_match("/^NOFOTO/", $ppfoto) == false) {
					$pfoto = $baseapp . "users/" . $this->Auth->user('id') . "/" . $ppfoto;
				} else {
					$pfoto = $baseapp . "img/wall.jpg";
				}



			}

			$this->set('is_completed', $is_completed2);



		}


		// Comprobamos si hay en sesión un usuario activo
		if (empty($this->Auth->user())) {
			$usuario = null;
			$pfoto = $baseapp . "img/wall.jpg";
			$role = false;
			$fotobase = null;
			$this->set("wallpaper", $baseapp . "img" . "/default-background.jpg");
			$this->set("estilo", "light");
		} else {
			$usuario = $this->Auth->user('id');
			$username = $this->Auth->user('username');
			$role = $this->Auth->User('role');

			$datos = $this->User->duser($usuario);

			//$ppfoto = $is_completed[0]['uc']['fotoperfil'];
			if (file_exists(ROOT . DS . APP_DIR . DS . "webroot" . DS . "users" . DS . $usuario . DS . $datos[0]['uc']['fotoperfil'])) {
				$fotobase = Router::url('/', true) . "users/" .$usuario . "/" . $datos[0]['uc']['fotoperfil'];
			}else {
				$fotobase = "https://i0.wp.com/geekazos.com/wp-content/uploads/2015/02/fb2.jpg?fit=1280%2C720";
			}



		}


		if (isset($username)) {
			$this->set('username', $username);
		}
		$this->set('mpt', $testmdt);
		$this->set('fotobase', $fotobase);
		$this->set('role', $role);
		$this->set('url', $baseapp);
		$this->set('base', $baseapp);
		$this->set('img', $baseapp . "img" . "/");
		$this->set('files', $baseapp . "files" . "/");
		$this->set('script', $baseapp . "js" . "/");
		$this->set('fundamental_libs', $baseapp . "fundamental_libs" . "/");
		$this->set('iapps', $baseapp . "iapps" . "/");
		$this->set('usuario', $usuario);
		$this->set('pfoto', $pfoto);
		$this->set('debug', Configure::read('debug'));
		// URL RELATIVA PARA CUANDO ENVIEMOS COMENTARIOS
		$this->set('related', $_SERVER['REQUEST_URI']);
		$this->set('is_completed', $is_completed2);







		$downloader = APP . WEBROOT_DIR . DS . "visitas.txt";

		$num1 = intval(file_get_contents($downloader)) + 1;

		$file = fopen($downloader, "w");
		fwrite($file, $num1);
		fclose($file);


		$this->set('contador', $num1);




	}

}

// Test from PHPStorm
