<?php
// app/Controller/UsersController.php
App::uses('AppController', 'Controller');


class UsersController extends AppController
{

	public $layout = "ajax";
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->Auth->allow('add', 'logout', 'captcha', 'i10n', 'activar');
	}

	public function index()
	{
		$this->User->recursive = 0;

		$this->set('users', $this->paginate());

	}

	// Con esta función cambiamos el idioma global en sesión de la aplicación.
	public function i10n() {
		_t("",true, $_GET['t'], true);
		$this->Rapid->uialert(_t("11", true ), _t("12",true), "info");
		return $this->redirect($_GET['url']);
	}

	public function view($id = null)
	{
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Usuario no válido, ya existe en nuestra plataforma'));
		}
		$this->set('user', $this->User->findById($id));
	}

	public function add()
	{
		$this->layout = 'default';

		// comprobamos que la llamada ha sido por POST
		if ($this->request->is('post')) {
			// Ahora vamos comprobando uno por uno que los datos introducidos son viables para crear el usuario
			//$this->Rapid->var_dump($this->request->data['usuario']); exit;

			if (filter_var($this->request->data['usuario'], FILTER_VALIDATE_EMAIL)) {
				// Si la dirección de correo electrónico es válida procedemos a seguir validando el formulario
				// Ahora comprobamos si el usuario elegido existe en la base de datos, de existir devolvemos un error
				if (null == $this->User->rev_bas($this->request->data['usuario'])[0]['users']['username']) {
					// Si el usuario no existe podemos seguir, ahora comprobamos que las contraseñas sean correctas
					if ($this->request->data['password1'] == $this->request->data['password2']) {
						// Las contraseñas son iguales, procedemos a comprobar si estas entran dentro de la seguridad permitida
						function validar_clave($clave, &$error_clave)
						{
							if (strlen($clave) < 6) {
								$error_clave = _t("20", true);
								return false;
							}
							if (strlen($clave) > 16) {
								$error_clave = _t("21", true);
								return false;
							}
							if (!preg_match('`[a-z]`', $clave)) {
								$error_clave = _t("22", true);
								return false;
							}
							if (!preg_match('`[A-Z]`', $clave)) {
								$error_clave = _t("23", true);
								return false;
							}
							if (!preg_match('`[0-9]`', $clave)) {
								$error_clave = _t("24", true);
								return false;
							}
							$error_clave = "";
							return true;
						}

						$error_encontrado = "";
						if (validar_clave($this->request->data['password1'], $error_encontrado)) {
							// Si la validación de la contraseña es correcta pasamos comprobar el reCAPTCHA 3 de Google:
							$recaptcha = $_POST["g-recaptcha-response"];

							$url = 'https://www.google.com/recaptcha/api/siteverify';
							$data = array(
								'secret' => '6LcRJIsUAAAAAFciQUUpO7ADQoAcFi-LxfG29bZm',
								'response' => $recaptcha
							);
							$options = array(
								'http' => array(
									'method' => 'POST',
									'content' => http_build_query($data),
									'header' => 'Content-Type: application/x-www-form-urlencoded'
								)
							);
							$context = stream_context_create($options);
							$verify = file_get_contents($url, false, $context);
							$captcha_success = json_decode($verify);

							if ($captcha_success->success) {
									// Si finalmente el usuario supera el Captcha de Google, procedemos a crear el usuario.


								/*
								* Para esto madaremos un email, es importante que si no se envia el email no se cree la cuenta
								*/


								$mensajeT1 = _t("19", true);
								$mensajeT2 = str_replace("%VAR1%", $this->request->data['usuario'], $mensajeT1);
								$mensajeT = str_replace("%VAR2%", encriptar($this->request->data['usuario']), $mensajeT2);
								$mail = new PHPMailer\PHPMailer\PHPMailer();
								$mail->isSendMail();
								$mail->SMTPDebug = 0;
								$mail->CharSet = 'UTF-8';
								$mail->Encoding = 'base64';
								$mail->Timeout=60;
								$mail->IsSMTP();
								$mail->Helo = Configure::read('correo_dominio');
								$mail->Host = Configure::read('correo_host_smpt');
								$mail->Port = 25;
								$mail->SMTPAutoTLS = false;
								$mail->SMTPSecure = false;
								$mail->SMTPAuth = true;
								$mail->Username = Configure::read('correo_username');
								$mail->Password = Configure::read('correo_password');
								$mail->setFrom(Configure::read('correo_from'), Configure::read('correo_from_name'));
								$mail->addAddress($this->request->data['usuario']);
								$mail->Subject = 'Activación de su cuenta';
								$mail->Body = $mensajeT;
								$mail->IsHTML(true);

								if (!$mail->send()) {
									$this->Rapid->uialert(_t("26", true), _t("25", true));
									//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
									return $this->redirect(Router::url('/', true));
								} else {

									// si se ha enviado el correo procedemos a crear la cuenta para su posterior activación

									$this->User->nuevo_usuario($this->request->data['usuario'], $this->request->data['password1']);

									$this->Rapid->uialert(_t("27", true), _t("28", true), "info");
									//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
									return $this->redirect(Router::url('/', true));

								}


							} else {
									//$this->Session->setFlash(__('Ha ocurrido un error con el sistema anti-spam, debes pinchar sobre "No soy un robot".'));
								$this->Rapid->uialert(_t("29", true), _t("30",true), "error");
								return $this->redirect(array('action' => 'add'));
							}


								// Enviamos el email de activación...


						} else {


							$this->Rapid->uialert(_t("31",true), $error_encontrado, "error");

								//$this->Session->setFlash(__($alerta));
							return $this->redirect(array('action' => 'add'));
						}

					} else {

						$this->Rapid->uialert(_t("31",true), _t("32",true), "error");
						return $this->redirect(array('action' => 'add'));
					}

				} else {
					$this->Rapid->uialert(_t("33",true), _t("34",true), "error");
					return $this->redirect(array('action' => 'add'));
				}
			} else {
				$this->Rapid->uialert(_t("35",true), _t("36",true), "error");
				return $this->redirect(array('action' => 'add'));
			}

		}
	}



	public function activar($email = NULL) {

		if ($email == NULL){
			$this->Rapid->uialert(_t("26",true), _t("37",true), "error");
			//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
			return $this->redirect(Router::url('/', true));
		}
		$setON = desencriptar($email);
		$this->User->activar_usuario($setON);
		$this->Rapid->uialert(_t("38",true), _t("39",true), "info");
		//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
		return $this->redirect(Router::url('/', true));

	}





	public function facebookcb() {
	}

	public function authgoogle() {
		$this->layout = 'ajax';

	}


	public function eaccount() {
		if (isset($this->request->data['User']['username'])) {
			$snuser = desencriptar($this->request->data['User']['username']);
			$snemail = desencriptar($this->request->data['User']['password']);
			if (filter_var($snemail, FILTER_VALIDATE_EMAIL)) {
				echo "Bienvenido " . $snuser . ", se han leido tus datos correctamente, su email es " . $snemail;



				$infopost = $this->User->rev_bas($snemail);

				if ($infopost == NULL) {
					// QUE EL USUARIO NO ESTE REGISTRADO Y TENGAMOS QUE REGISTRARLO, Y CON ESA INFORMACIÓN INICIE SESIÓN
					$this->User->nuevo_usuario($snemail, $snuser);
					$this->User->activar_usuario($snemail);
					$this->request->data['User']['username'] = $snemail;
					$this->request->data['User']['password'] = $snuser;

					if ($this->Auth->login()) {



						function getRealIP3() {
							if (!empty($_SERVER['HTTP_CLIENT_IP']))
								return $_SERVER['HTTP_CLIENT_IP'];
							if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
								return $_SERVER['HTTP_X_FORWARDED_FOR'];
							return $_SERVER['REMOTE_ADDR'];
						}


						//echo getRealIP(); exit;
						$ipreal = getRealIP3();
						$this->User->ponip($this->Auth->User('id'), $ipreal);






						if ( $this->Auth->User('active') == "N") {
						// SI ES NO, SE DESLOGUEA Y SE MANDA EL MENSAJE AL INDEX
							$this->Rapid->uialert(_t("16", true), _t("18", true), "error");
							return $this->redirect($this->Auth->logout());
						}

						if ( $this->Auth->User('ban') == "S") {
						// SI ES NO, SE DESLOGUEA Y SE MANDA EL MENSAJE AL INDEX
							$this->Rapid->uialert("Usuario no permitido", "Por motivos de seguridad, tu cuenta ha sido bloqueada, tus publicaciones y comentarios han sido deshabilitados, para recuperar tu cuenta, contacta con info@mallorcamoves.es ", "error");
							return $this->redirect($this->Auth->logout());
						}




						$this->Rapid->uialert("¡Bienvenido a LineOS!", "Ya eres parte de la comunidad, actualmente solo puedes iniciar sesión con redes sociales, actualiza tu contraseña desde el panel de control para poder acceder (si quieres) de forma manual.", "info");
						return $this->redirect($this->Auth->redirectUrl());
					} else {
						$this->Rapid->uialert(_t("16", true), _t("17", true), "error");
						//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
						return $this->redirect(Router::url('/', true));
					}


				} else {

					// QUE EL USUARIO ESTE REGISTRADO Y QUERAMOS INICIAR SESIÓN
					// Como el sistema de constraseñas va por hasheo y no podemos saber la contraseña del usuario

					$this->request->data['User']['username'] = $snemail;
					$this->request->data['User']['password'] = desencriptar($infopost[0]['users']['snp']);


					if ($this->Auth->login()) {


						function getRealIP4() {
							if (!empty($_SERVER['HTTP_CLIENT_IP']))
								return $_SERVER['HTTP_CLIENT_IP'];
							if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
								return $_SERVER['HTTP_X_FORWARDED_FOR'];
							return $_SERVER['REMOTE_ADDR'];
						}


						//echo getRealIP(); exit;
						$ipreal = getRealIP4();
						$this->User->ponip($this->Auth->User('id'), $ipreal);



						if ( $this->Auth->User('active') == "N") {
						// SI ES NO, SE DESLOGUEA Y SE MANDA EL MENSAJE AL INDEX
							$this->Rapid->uialert(_t("16", true), _t("18", true), "error");
							return $this->redirect($this->Auth->logout());
						}

						if ( $this->Auth->User('ban') == "S") {
						// SI ES NO, SE DESLOGUEA Y SE MANDA EL MENSAJE AL INDEX
							$this->Rapid->uialert("Usuario no permitido", "Por motivos de seguridad, tu cuenta ha sido bloqueada, tus publicaciones y comentarios han sido deshabilitados, para recuperar tu cuenta, contacta con info@mallorcamoves.es ", "error");
							return $this->redirect($this->Auth->logout());
						}


						$this->Rapid->uialert("Usuario logueado", "Has iniciado sesión con éxito usando una red social.", "info");
						return $this->redirect($this->Auth->redirectUrl());
					} else {
						$this->Rapid->uialert(_t("16", true), _t("17", true), "error");
						//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
						return $this->redirect(Router::url('/', true));
					}


				}




				exit;
			} else {
				$this->Rapid->uialert("Error","Parece que existe un intento de hacking en su red, por favor, por su seguridad registrese de forma manual.", "error");
			}
		} else {
			$this->Rapid->uialert("Error al recibir datos","Ha ocurrido un error al recibir los datos de su red social, intente iniciar sesión desde su red social más adelante", "error");
		}
		//return $this->redirect(array('controller' => 'Web', 'action' => 'entry'));
		return $this->redirect(Router::url('/', true));
	}





	public function login()
	{
		if ($this->request->is('post')) {
				// Vamos a retocar esto para que si el usuario esta inactivo no le deje entrar a la plataforma

			$infopost = $this->User->rev_bas($this->request->data['User']['username']);

			if ($infopost == NULL) {
					// Si ha ocurrido esto esque no encuentra el usuario en la base de datos
				$this->Rapid->uialert(_t("16", true), _t("17", true), "error");
				//return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));
				return $this->redirect(Router::url('/', true));
			} else {
					// LLegados a este punto el usuario existe, así que vamos a comprobar si puede o no iniciar
					// sesión tomando en cuenta si esta activo o no
				// Vamos a dejar que se inicie sesión, pero en el caso de que no este activo lo deslogueamos




				function getRealIP2() {
					if (!empty($_SERVER['HTTP_CLIENT_IP']))
						return $_SERVER['HTTP_CLIENT_IP'];
					if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
						return $_SERVER['HTTP_X_FORWARDED_FOR'];
					return $_SERVER['REMOTE_ADDR'];
				}


						//echo getRealIP(); exit;
				$ipreal = getRealIP2();




				if ($this->Auth->login()) {


					$this->User->ponip($this->Auth->User('id'), $ipreal);

					if ( $infopost[0]['users']['active'] == "N") {
						// SI ES NO, SE DESLOGUEA Y SE MANDA EL MENSAJE AL INDEX
						$this->Rapid->uialert(_t("16", true), _t("18", true), "error");
						return $this->redirect($this->Auth->logout());
					}

					if ( $infopost[0]['users']['ban'] == "S") {
						// SI ES NO, SE DESLOGUEA Y SE MANDA EL MENSAJE AL INDEX
						$this->Rapid->uialert("Usuario no permitido", "Por motivos de seguridad, tu cuenta ha sido bloqueada, tus publicaciones y comentarios han sido deshabilitados, para recuperar tu cuenta, contacta con info@mallorcamoves.es ", "error");
						return $this->redirect($this->Auth->logout());
					}


					// en caso contrario se le lleva directamente al dashboard

					// ACTUALIZAMOS LA ÚLTIMA ENTRADA EN BASE DE DATOS
					$this->User->query("UPDATE users SET `modified`='" . date("Y-m-d H:i:s") . "' WHERE `id`= " . $this->Auth->User('id'));


					$this->Rapid->uialert("Usuario logueado","Has iniciado sesión con éxito", "info");
					//return $this->redirect($this->Auth->redirectUrl());
					//return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));
					return $this->redirect(Router::url('/', true));
				}
				$this->Rapid->uialert(_t("16", true), _t("17", true), "error");
				//return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));
				return $this->redirect(Router::url('/', true));
			}
		} else {
			//return $this->redirect(array('controller' => 'Web', 'action' => 'index'));

		}

	}


	// Función para terminar el registro
	public function finish() {

		$this->set('title_for_layout', "Registro final");


		if (!isset($this->User->test_registrado($this->Auth->User('id'))[0]['uc']['gbl_id'])) {
			/*
			 * SI EL USUARIO NO TIENE UN PERFIL SE LE CREA
			 */
			if (isset($_POST['envio'])) { // Si recibimos datos, los tratamos por POST


				if (isset($_POST['autobiografia'])) {
					$autobiografia = $_POST['autobiografia'];
				} else {
					$autobiografia = "";
				}

				if (isset($_POST['localidad'])) {
					$localidad = $_POST['localidad']; // 18002
				} else {
					$localidad = "null"; // null
				}



				$res_sub = $this->Rapid->subir_al_servidor($_FILES, "fotoperfil", $this->Auth->User('id'), "profile");


				if ($res_sub != FALSE) {
					$this->User->completar_usuario(
						$this->Auth->User('id'),
						strip_tags($_POST['u-name']), 		// Nombre
						strip_tags($_POST['u-surname']),	// Appelidos
						strip_tags($_POST['u-date']),		// Fecha de nacimiento
						strip_tags($_POST['dni']),			// Nickname
						strip_tags($localidad),	// Localidad
						strip_tags($res_sub['nombre']),		// Foto de perfil
						json_encode($_POST['lobby']),		// Sitios del lobby
						strip_tags($_POST['busco']),		// IMPORTANTE Qué busca
						strip_tags($_POST['ojos']),			// Color de ojos
						strip_tags($_POST['estudios']),		// Estudios
						strip_tags($_POST['complexion']),	// Complexión física
						strip_tags($_POST['autovisual']),	// Como se define
						strip_tags($_POST['rasgo-a']),		// Su mejor cualidad física
						strip_tags($_POST['rasgo-b']),		// Su mejor cualidad en personalidad
						strip_tags($_POST['rasgo-r']),		// Cantidad de romántico/a
						json_encode($_POST['hobbies']), // Hobbies
						strip_tags($_POST['pelo']),			// Color del pelo
						strip_tags($_POST['lpelo']),		// Largo del pelo
						strip_tags($_POST['civil']),		// Estado civil
						strip_tags($_POST['hijos']),		// Numero de hijos
						strip_tags($_POST['vivo']),			// Tipo de vivienda
						strip_tags($_POST['trabajo']),		// Trabajo
						strip_tags($_POST['animales']),		// Gusto por animales
						json_encode($_POST['idiomas']),	// Idiomas
						strip_tags($_POST['comidas']),		// Comidas
						strip_tags($autobiografia),			// Autobiografia
						strip_tags($_POST['soy']),			// Genero sexual
						strip_tags($_POST['soy2']),			// Condicion sexual
						strip_tags($_POST['altura']));		// Altura
					$this->Rapid->uialert("Registro completado", "Se ha completado su registro en la plataforma", "info");
					return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));

				} else {
					$this->User->completar_usuario(
						$this->Auth->User('id'),
						strip_tags($_POST['u-name']), 		// Nombre
						strip_tags($_POST['u-surname']),	// Appelidos
						strip_tags($_POST['u-date']),		// Fecha de nacimiento
						strip_tags($_POST['dni']),			// Nickname
						strip_tags($localidad),				// Localidad
						strip_tags("NOFOTO"),			// Foto de perfil
						json_encode($_POST['lobby']),		// Sitios del lobby
						strip_tags($_POST['busco']),		// IMPORTANTE Qué busca
						strip_tags($_POST['ojos']),			// Color de ojos
						strip_tags($_POST['estudios']),		// Estudios
						strip_tags($_POST['complexion']),	// Complexión física
						strip_tags($_POST['autovisual']),	// Como se define
						strip_tags($_POST['rasgo-a']),		// Su mejor cualidad física
						strip_tags($_POST['rasgo-b']),		// Su mejor cualidad en personalidad
						strip_tags($_POST['rasgo-r']),		// Cantidad de romántico/a
						json_encode($_POST['hobbies']), // Hobbies
						strip_tags($_POST['pelo']),			// Color del pelo
						strip_tags($_POST['lpelo']),		// Largo del pelo
						strip_tags($_POST['civil']),		// Estado civil
						strip_tags($_POST['hijos']),		// Numero de hijos
						strip_tags($_POST['vivo']),			// Tipo de vivienda
						strip_tags($_POST['trabajo']),		// Trabajo
						strip_tags($_POST['animales']),		// Gusto por animales
						json_encode($_POST['idiomas']),	// Idiomas
						strip_tags($_POST['comidas']),		// Comidas
						strip_tags($autobiografia),			// Autobiografia
						strip_tags($_POST['soy']),			// Genero sexual
						strip_tags($_POST['soy2']),			// Condicion sexual
						strip_tags($_POST['altura']));		// Altura
					$this->Rapid->uialert("Registro completado", "Se ha completado su registro en la plataforma sin foto de perfil, por favor, entre a su zona personal y suba una imagen de perfil", "info");
					return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));
				}


				exit;
			}
		} else {
			/*
			 * SI EL USUARIO SI TIENE UN PERFIL SE LE EDITA
			 */

			if (isset($_POST['envio'])) { // Si recibimos datos, los tratamos por POST


				if (isset($_POST['autobiografia'])) {
					$autobiografia = $_POST['autobiografia'];
				} else {
					$autobiografia = "";
				}

				if (isset($_POST['localidad'])) {
					$localidad = $_POST['localidad']; // 18002
				} else {
					$localidad = "null"; // null
				}



				$res_sub = $this->Rapid->subir_al_servidor($_FILES, "fotoperfil", $this->Auth->User('id'), "profile");


				if ($res_sub != FALSE) {
					$this->User->editar_finish(
						$this->Auth->User('id'),
						strip_tags($_POST['u-name']), 		// Nombre
						strip_tags($_POST['u-surname']),	// Appelidos
						strip_tags($_POST['u-date']),		// Fecha de nacimiento
						strip_tags($_POST['dni']),			// Nickname
						strip_tags($localidad),	// Localidad
						strip_tags($res_sub['nombre']),		// Foto de perfil
						json_encode($_POST['lobby']),		// Sitios del lobby
						strip_tags($_POST['busco']),		// IMPORTANTE Qué busca
						strip_tags($_POST['ojos']),			// Color de ojos
						strip_tags($_POST['estudios']),		// Estudios
						strip_tags($_POST['complexion']),	// Complexión física
						strip_tags($_POST['autovisual']),	// Como se define
						strip_tags($_POST['rasgo-a']),		// Su mejor cualidad física
						strip_tags($_POST['rasgo-b']),		// Su mejor cualidad en personalidad
						strip_tags($_POST['rasgo-r']),		// Cantidad de romántico/a
						json_encode($_POST['hobbies']), // Hobbies
						strip_tags($_POST['pelo']),			// Color del pelo
						strip_tags($_POST['lpelo']),		// Largo del pelo
						strip_tags($_POST['civil']),		// Estado civil
						strip_tags($_POST['hijos']),		// Numero de hijos
						strip_tags($_POST['vivo']),			// Tipo de vivienda
						strip_tags($_POST['trabajo']),		// Trabajo
						strip_tags($_POST['animales']),		// Gusto por animales
						json_encode($_POST['idiomas']),	// Idiomas
						strip_tags($_POST['comidas']),		// Comidas
						strip_tags($autobiografia),			// Autobiografia
						strip_tags($_POST['soy']),			// Genero sexual
						strip_tags($_POST['soy2']),			// Condicion sexual
						strip_tags($_POST['altura']));		// Altura
					$this->Rapid->uialert("Registro completado", "Se ha completado su registro en la plataforma", "info");
					return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));

				} else {
					$this->User->editar_finish(
						$this->Auth->User('id'),
						strip_tags($_POST['u-name']), 		// Nombre
						strip_tags($_POST['u-surname']),	// Appelidos
						strip_tags($_POST['u-date']),		// Fecha de nacimiento
						strip_tags($_POST['dni']),			// Nickname
						strip_tags($localidad),				// Localidad
						strip_tags("NOFOTO"),			// Foto de perfil
						json_encode($_POST['lobby']),		// Sitios del lobby
						strip_tags($_POST['busco']),		// IMPORTANTE Qué busca
						strip_tags($_POST['ojos']),			// Color de ojos
						strip_tags($_POST['estudios']),		// Estudios
						strip_tags($_POST['complexion']),	// Complexión física
						strip_tags($_POST['autovisual']),	// Como se define
						strip_tags($_POST['rasgo-a']),		// Su mejor cualidad física
						strip_tags($_POST['rasgo-b']),		// Su mejor cualidad en personalidad
						strip_tags($_POST['rasgo-r']),		// Cantidad de romántico/a
						json_encode($_POST['hobbies']), // Hobbies
						strip_tags($_POST['pelo']),			// Color del pelo
						strip_tags($_POST['lpelo']),		// Largo del pelo
						strip_tags($_POST['civil']),		// Estado civil
						strip_tags($_POST['hijos']),		// Numero de hijos
						strip_tags($_POST['vivo']),			// Tipo de vivienda
						strip_tags($_POST['trabajo']),		// Trabajo
						strip_tags($_POST['animales']),		// Gusto por animales
						json_encode($_POST['idiomas']),	// Idiomas
						strip_tags($_POST['comidas']),		// Comidas
						strip_tags($autobiografia),			// Autobiografia
						strip_tags($_POST['soy']),			// Genero sexual
						strip_tags($_POST['soy2']),			// Condicion sexual
						strip_tags($_POST['altura']));		// Altura
					$this->Rapid->uialert("Registro completado", "Se ha completado su registro en la plataforma sin foto de perfil, por favor, entre a su zona personal y suba una imagen de perfil", "info");
					return $this->redirect(array('controller' => 'Panel', 'action' => 'index'));
				}


				exit;
			}
		}




    }


    public function logout()
    {
		$this->Rapid->uialert("Usuario deslogueado","Ha salido usted de la sesión", "error");
    	return $this->redirect($this->Auth->logout());
    }

    public function inicio()
    {
			//$this->Rapid->flash("Haciendo prueba");
			// ESTE ES EL INICIO DEL DASHBOARD (QUEDA PENDIENTE HACER EL MENU LATERAL MEDIANTE CONTROLADOR)
    	$this->set('title_for_layout', "Dashboard");

    }



	public function megusta($like) {
		$check = $this->User->checkmegusta($this->Auth->User('id'), $like);
		//$whois = $this->User->test_registrado($like);
		$whois2 = $this->User->test_registrado($this->Auth->User('id'));


		if ($check) {
			// Si tiene me gusta se lo quitamos
			$this->User->nomegusta($this->Auth->User('id'), $like);
			$this->User->put_notificacion($like, "Lo sentimos, ya no le gustas a " . $whois2[0]['uc']['dni'], "users/profile/" . $this->Auth->User('id'), $this->Auth->User('id'));
			echo "DEL";
		} else {
			// Si no tiene me gusta se lo damos
			$this->User->megusta($this->Auth->User('id'), $like);
			$this->User->put_notificacion($like, "Enhorabuena! le gustas a " . $whois2[0]['uc']['dni'], "users/profile/" . $this->Auth->User('id'), $this->Auth->User('id'));
			echo "INS";
		}


		$notificaciones = array();
		foreach ($this->User->get_notificaciones($this->Auth->User('id')) as $notificacion) {
			if ($notificacion['notificaciones']['leido'] == 0) {
				$notificaciones['noleidas'][] = array("titulo" => $notificacion['notificaciones']['notificacion'], "fecha" => $notificacion['notificaciones']['fecha'], "icono" => $notificacion['notificaciones']['icono'], "url" => $notificacion['notificaciones']['url']);
			} else {
				$notificaciones['leidas'][] = array("titulo" => $notificacion['notificaciones']['notificacion'], "fecha" => $notificacion['notificaciones']['fecha'], "icono" => $notificacion['notificaciones']['icono'], "url" => $notificacion['notificaciones']['url']);
			}
		}

		$myfile = fopen(ROOT . DS . APP_DIR . DS . "webroot" . DS . "users" . DS . $this->Auth->User('id') . DS . "notificacion.json", "w") or die("No se puede abrir el fichero");
		fwrite($myfile, json_encode($notificaciones));
		chmod($myfile, 0777);
		fclose($myfile);


		exit;
	}



	public function checkmegusta($like) {
		$check = $this->User->checkmegusta($this->Auth->User('id'), $like);
		if ($check) {
			// TIENE UN ME GUSTA!
			echo "LOVE";
		} else {
			// NO TIENE UN ME GUSTA
			echo "FAIL";
		}
		exit;
	}
	/*
	 * Con esta funcion cambiamos la foto de perfil via AJAX
	 */
	public function fpdate() {
		if (isset($_POST['fichero'])) {
			echo $_POST['fichero'];
			$this->User->updatefpd($this->Auth->User('id'), $_POST['fichero']);
		}
		exit;
		}





    public function profile($usuario = NULL) {
		// Primero obtenemos todos los datos del usuario TANTO EL QUE ESTA EN SESIÓN COMO EL QUE QUEREMOS RECOGER SUS DATOS
    	if ($usuario == NULL) {
    		$usuario = $this->Auth->User('id');
			$this->set('perfil', $this->User->duser($this->Auth->user('id'))[0]);
			$this->set('uac', true);
    	} else {
			$this->set('uac', false);
		}
    	$datos = $this->User->duser($usuario);
		$this->set('galeria', $this->User->MDTfiles($usuario));
		$this->set('neac', $usuario);




		// PARTE DONDE COGEMOS TODA ESPAÑA PARA SELECCIONARLA LUEGO VIA SCRIPT
		$base = Router::url('/', true);
		//$pro = $base . "files" . DS . "provincias.json";
		$mun = $base . "files" . DS . "municipios.json";
		//$provicincias = json_decode(file_get_contents($pro), true);
		$municipios = json_decode(file_get_contents($mun), true);

		// La parte lógica y jodida de como combinar los municipios con las localidades TODO: REVISAR
		// Tenemos dos arrays, uno con las ID de los municios y otro con cada una de las localidades
		// lo único que podemos cotejar es las dos primeras cifras del codigo postal de cada localidad
		// que es lo que coincide, hay que revisar esto porque igual me he colado y no es así, pero
		// en cualquier caso hay otro recurso disponible por javascript.

		/*$arrm = array();
		foreach ($municipios as $muni) {
			$arrm[] = array(
				"municipio" => $muni['id'],
				"id" => substr($muni['id'], 0, -3),
				"nombre" => $muni['nm'],
			);
		}
		$municipios2 = group_by("id",$arrm);
		$ideal = array();
		foreach ($municipios2 as $key => $mx) {
			foreach ($provicincias as $key2 => $px) {
				if($provicincias[$key2]['id'] == $key){
					$ideal[] = array(
						"provincia" => $px['nm'],
						"codigo" => $key,
						"municipio" => $municipios2[$key],
					);
				}
			}
		}*/
		//$datos[0]['uc']['localidad']);
		$mun = array();
		foreach ($municipios as $municipio) {
			if ($municipio['id'] == $datos[0]['uc']['localidad']) {
				$mun[] = $municipio['nm'];
			}
		}
		if (!empty($mun[0])) {
			$localidad = $mun[0];
		} else {
			$localidad = "un sitio";
		}


		$this->set('localidad', $localidad);


    	if (file_exists(ROOT . DS . APP_DIR . DS . "webroot" . DS . "users" . DS . $usuario . DS . $datos[0]['uc']['fotoperfil'])) {
    		$fotobase = Router::url('/', true) . "users/" . $usuario . "/" . $datos[0]['uc']['fotoperfil'];
    	}else {
    		$fotobase = "https://i0.wp.com/geekazos.com/wp-content/uploads/2015/02/fb2.jpg?fit=1280%2C720";
    	}
    	$this->set("datos_perfil", $datos);
    	$this->set("fotobase2", $fotobase);
    }


    public function msg($msg, $urlsec) {

    }







}
