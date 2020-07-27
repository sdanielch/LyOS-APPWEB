<?php
App::uses('AppModel', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class User extends AppModel
{


	public $validate = array(
		'username' => array(
			'nonEmpty' => array(
				'rule' => array('notBlank'),
				'message' => '¡Se necesita un nombre de usuario!',
				'allowEmpty' => false
			),
			'between' => array(
				'rule' => array('between', 5, 25),
				'required' => true,
				'message' => 'El nombre de usuario debe tener entre 5 y 25 carácteres'
			),
			'unique' => array(
				'rule' => array('isUniqueUsername'),
				'message' => 'Este usuario ya existe, pero puedes elegir otro :)'
			),
			'alphaNumericDashUnderscore' => array(
				'rule' => array('alphaNumericDashUnderscore'),
				'message' => 'El nombre de usuario solo puede contener letras, números y guiones bajos'
			),
		),
		'password' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Se necesita una contraseña'
			),
			'min_length' => array(
				'rule' => array('minLength', '6'),
				'message' => 'La contraseña debe tener un mínimo de 5 carácteres'
			)
		),

		'password_confirm' => array(
			'required' => array(
				'rule' => array('notBlank'),
				'message' => 'Por favor, confirma tu contraseña'
			),
			'equaltofield' => array(
				'rule' => array('equaltofield', 'password'),
				'message' => 'Las contraseñas no coinciden, deben coincidir.'
			)
		),

		'email' => array(
			'required' => array(
				'rule' => array('email', true),
				'message' => 'Por favor introduce un email válido.'
			),
			'unique' => array(
				'rule' => array('isUniqueEmail'),
				'message' => 'Debe de registrarse con otro email, este ya está siendo usado por otra cuenta',
			),
			'between' => array(
				'rule' => array('between', 6, 60),
				'message' => 'El correo debe estar entre 6 y 60 carácteres incluyendo la arroba'
			)
		),
		'role' => array(
			'valid' => array(
				'rule' => array('inList', array('author', 'admin', 'bishop', 'rook', 'knight', 'pawn')),
				'message' => 'Por favor elige un rol válido',
				'allowEmpty' => false
			)
		),


		'password_update' => array(
			'min_length' => array(
				'rule' => array('minLength', '6'),
				'message' => 'La contraseña tiene que tener un minimo de 6 carácteres',
				'allowEmpty' => true,
				'required' => false
			)
		),
		'password_confirm_update' => array(
			'equaltofield' => array(
				'rule' => array('equaltofield', 'password_update'),
				'message' => 'Las contraseñas deben de coincidir.',
				'required' => false,
			)
		)


	);

	/**
	 * Before isUniqueUsername
	 * @param array $options
	 * @return boolean
	 */
	function isUniqueUsername($check)
	{

		$username = $this->find(
			'first',
			array(
				'fields' => array(
					'User.id',
					'User.username'
				),
				'conditions' => array(
					'User.username' => $check['username']
				)
			)
		);

		if (!empty($username)) {
			if ($this->data[$this->alias]['username'] != $username['User']['username']) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	/**
	 * Before isUniqueEmail
	 * @param array $options
	 * @return boolean
	 */
	function isUniqueEmail($check)
	{

		$email = $this->find(
			'first',
			array(
				'fields' => array(
					'User.id',
					'User.email'
				),
				'conditions' => array(
					'User.email' => $check['email']
				)
			)
		);

		if (!empty($email)) {
			if ($this->data[$this->alias]['email'] != $email['User']['email']) {
				return true;
			} else {
				return false;
			}
		} else {
			return true;
		}
	}

	public function alphaNumericDashUnderscore($check)
	{
		// $data array is passed using the form field name as the key
		// have to extract the value to make the function generic
		$value = array_values($check);
		$value = $value[0];

		return preg_match('/^[a-zA-Z0-9_ \-]*$/', $value);
	}

	public function equaltofield($check, $otherfield)
	{
		//get name of field
		$fname = '';
		foreach ($check as $key => $value) {
			$fname = $key;
			break;
		}
		return $this->data[$this->name][$otherfield] === $this->data[$this->name][$fname];
	}

	/**
	 * Before Save
	 * @param array $options
	 * @return boolean
	 */


	public function beforeSave($options = array())
	{
		if (isset($this->data[$this->alias]['password'])) {
			$passwordHasher = new BlowfishPasswordHasher();
			$this->data[$this->alias]['password'] = $passwordHasher->hash(
				$this->data[$this->alias]['password']
			);
		}
		return true;
	}

// Devuelve la información básica de registro de un usuario por su email de registro
	public function rev_bas($username)
	{
		$db = $this->getDataSource();
		$values = array('username' => $username);
		$sql = "
		SELECT * FROM users WHERE username = :username LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		//$merged = call_user_func_array('array_merge', $result);
		return $result;
	}

	// Devuelve la información básica de registro de un usuario por su email de registro
	public function rev_bas_id($username)
	{
		$db = $this->getDataSource();
		$values = array('username' => $username);
		$sql = "
		SELECT * FROM users
		LEFT JOIN users_completed uc ON uc.gbl_id = users.id
		WHERE users.id = :username LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		//$merged = call_user_func_array('array_merge', $result);
		return $result;
	}

// Activar cuentas de usuario por email

	public function activar_usuario($email)
	{
		$db = $this->getDataSource();
		$values = array('email' => $email);
		$sql = "
		UPDATE users SET active='S' WHERE username=:email";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

// Crea un usuario en la base de datos
	public function nuevo_usuario($username, $password, $gen = "N", $empresario = NULL, $role = NULL)
	{


		if ($empresario == NULL) {
			$dan = 0;
			$snp = 0;
		} else {
			$dan = $empresario;
			$snp = 2;
		}

		if ($role == NULL) {
			$rol = "usuario";
		} else {
			$rol = $role;
		}




		$passwordHasher = new BlowfishPasswordHasher();
		$pe = $passwordHasher->hash($password);
		$db = $this->getDataSource();
		$values = array(
			'username' => $username,
			'password' => $pe,
			'snp' => encriptar($password),
			'active' => $gen,
			'referido' => $dan,
			'cdp' => $snp,
			'role' => $rol
		);
		$sql = "INSERT INTO users (username, password, snp, role, referido, cpd, active, ban)
		VALUES (:username, :password, :snp, :role, :referido, :cdp, :active, 'N')";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}






// Completa el registro de un usuario en la base de datos
	public function completar_usuario($gbl_id,
		$u_name,
		$u_surname,
		$u_date,
		$dni,
		$localidad,
		$fotoperfil,
		$lobby,
		$busco,
		$ojos,
		$estudios,
		$complexion,
		$autovisual,
		$rasgoa,
		$rasgob,
		$rasgor,
		$hobbies,
		$pelo,
		$lpelo,
		$civil,
		$hijos,
		$vivo,
		$trabajo,
		$animales,
		$idiomas,
		$comidas,
		$autobiografia,
		$soy,
		$soy2,
		$altura)
	{
		$db = $this->getDataSource();
		$values = array(
			'gbl_id' => $gbl_id,
			'u_name' => $u_name,
			'u_surname' => $u_surname,
			'u_date' => $u_date,
			'dni' => $dni,
			'localidad' => $localidad,
			'fotoperfil' => $fotoperfil,
			'lobby' => $lobby,
			'busco' => $busco,
			'ojos' => $ojos,
			'estudios' => $estudios,
			'complexion' => $complexion,
			'autovisual' => $autovisual,
			'rasgoa' => $rasgoa,
			'rasgob' => $rasgob,
			'rasgor' => $rasgor,
			'hobbies' => $hobbies,
			'pelo' => $pelo,
			'lpelo' => $lpelo,
			'civil' => $civil,
			'hijos' => $hijos,
			'vivo' => $vivo,
			'trabajo' => $trabajo,
			'animales' => $animales,
			'idiomas' => $idiomas,
			'comidas' => $comidas,
			'autobiografia' => $autobiografia,
			'soy' => $soy,
			'soy2' => $soy2,
			'altura' => $altura
		);
		$sql = "INSERT INTO `users_completed` (`gbl_id`, `u_name`, `u_surname`, `u_date`, `dni`, `localidad`, `fotoperfil`, `lobby`, `busco`, `ojos`, `estudios`, `complexion`, `autovisual`, `rasgo-a`, `rasgo-b`, `rasgo-r`, `hobbies`, `pelo`, `lpelo`, `civil`, `hijos`, `vivo`, `trabajo`, `animales`, `idiomas`, `comidas`, `autobiografia`, `soy`, `soy2`, `altura`)
		VALUES (:gbl_id, :u_name, :u_surname, :u_date, :dni, :localidad, :fotoperfil, :lobby, :busco, :ojos, :estudios, :complexion, :autovisual, :rasgoa, :rasgob, :rasgor, :hobbies, :pelo, :lpelo, :civil, :hijos, :vivo, :trabajo, :animales, :idiomas, :comidas, :autobiografia, :soy, :soy2, :altura)";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}



	public function editar_finish($gbl_id,
									  $u_name,
									  $u_surname,
									  $u_date,
									  $dni,
									  $localidad,
									  $fotoperfil,
									  $lobby,
									  $busco,
									  $ojos,
									  $estudios,
									  $complexion,
									  $autovisual,
									  $rasgoa,
									  $rasgob,
									  $rasgor,
									  $hobbies,
									  $pelo,
									  $lpelo,
									  $civil,
									  $hijos,
									  $vivo,
									  $trabajo,
									  $animales,
									  $idiomas,
									  $comidas,
									  $autobiografia,
									  $soy,
									  $soy2,
									  $altura)
	{
		$db = $this->getDataSource();
		$values = array(
			'gbl_id' => $gbl_id,			'u_name' => $u_name,
			'u_surname' => $u_surname,			'u_date' => $u_date,
			'dni' => $dni,			'localidad' => $localidad,
			'fotoperfil' => $fotoperfil,			'lobby' => $lobby,
			'busco' => $busco,			'ojos' => $ojos,
			'estudios' => $estudios,			'complexion' => $complexion,
			'autovisual' => $autovisual,			'rasgoa' => $rasgoa,
			'rasgob' => $rasgob,			'rasgor' => $rasgor,
			'hobbies' => $hobbies,			'pelo' => $pelo,
			'lpelo' => $lpelo,			'civil' => $civil,
			'hijos' => $hijos,			'vivo' => $vivo,
			'trabajo' => $trabajo,			'animales' => $animales,
			'idiomas' => $idiomas,			'comidas' => $comidas,
			'autobiografia' => $autobiografia,			'soy' => $soy,
			'soy2' => $soy2,			'altura' => $altura
		);

		if ($fotoperfil != "NOFOTO") {
			$sql = "UPDATE `users_completed`
		SET `u_name`= :u_name,
		`u_surname`= :u_surname,
		`u_date`= :u_date,
		`dni`= :dni,
		`localidad`= :localidad,
		`fotoperfil`= :fotoperfil,
		`lobby`= :lobby,
		`busco`= :busco,
		 `ojos`= :ojos,
		`estudios`= :estudios,
		 `complexion`= :complexion,
		`autovisual`= :autovisual,
		`rasgo-a`= :rasgoa,
		`rasgo-b`= :rasgob,
		`rasgo-r`= :rasgor,
		`hobbies`= :hobbies,
		`pelo`= :pelo,
		`lpelo`= :lpelo,
		`civil`= :civil,
		`hijos`= :hijos,
		`vivo`= :vivo,
		`trabajo`= :trabajo,
		`animales`= :animales,
		`idiomas`= :idiomas,
		`comidas`= :comidas,
		`autobiografia`= :autobiografia,
		`soy`= :soy,
		`soy2`= :soy2,
		`altura`= :altura
		 WHERE  `gbl_id`= " . $gbl_id;
		} else {
			$sql = "UPDATE users_completed
		SET `u_name`=:u_name,
		`u_surname`=:u_surname,
		`u_date`=:u_date,
		`dni`=:dni,
		`localidad`=:localidad,
		`lobby`=:lobby,
		`busco`=:busco,
		`ojos`=:ojos,
		`estudios`=:estudios,
		`complexion`=:complexion,
		`autovisual`=:autovisual,
		`rasgo-a`=:rasgoa,
		`rasgo-b`=:rasgob,
		`rasgo-r`=:rasgor,
		`hobbies`=:hobbies,
		`pelo`=:pelo,
		`lpelo`=:lpelo,
		`civil`=:civil,
		`hijos`=:hijos,
		`vivo`=:vivo,
		`trabajo`=:trabajo,
		`animales`=:animales,
		`idiomas`=:idiomas,
		`comidas`=:comidas,
		`autobiografia`=:autobiografia,
		`soy`=:soy,
		`soy2`=:soy2,
		`altura`=:altura
		 WHERE gbl_id= " . $gbl_id;
		}






		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}

	public function lovers($genero, $pag, $condicion, $id) {
		$db = $this->getDataSource();
		$values = array(
			'genero' => $genero,
			'pagina' => $pag
		);
		$sql = "SELECT * FROM users_completed AS uc
		LEFT JOIN users u ON u.id = uc.gbl_id
		WHERE ". $genero . " ". $condicion ." AND uc.gbl_id != " . $id . "
		ORDER BY uc.id DESC
		LIMIT " . $pag . ",25";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}


	public function test_registrado($id)
	{
		$db = $this->getDataSource();
		$values = array(
			'username' => $id,
		);
		$sql = "SELECT * FROM users_completed AS uc
		LEFT JOIN users u ON u.id = uc.gbl_id
		WHERE u.id = :username
		LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}


	public function lista_de_bdts($usuario)
	{
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario,
		);
		$sql = "SELECT * FROM BDT WHERE gbl_id = 1";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;

	}

	// Devuelve los comentarios de una sección en específico

	public function get_comentarios($url)
	{
		$db = $this->getDataSource();
		$values = array(
			'url' => $url,
		);
		$sql = "SELECT *
		FROM comentarios com
		LEFT JOIN users u ON u.id = com.usuario
		LEFT JOIN users_completed uc ON uc.gbl_id = com.usuario
		WHERE url = :url AND u.ban = 'N'
		ORDER BY com.date ASC";

		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}

	public function tipocomentario($dato)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato' => $dato
		);
		$sql = "SELECT *, SUBSTR(subidas.nsec,2,50) as pepino FROM subidas
		LEFT JOIN tarsec ON SUBSTR(subidas.nsec,2,50) = tarsec.enlace
		WHERE fichero = :dato";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	// Ingresa comentarios de un usuario en una seccion en especifico

	public function put_comentarios($usuario, $url, $comentario, $ip = "NO-IP")
	{
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario,
			'url' => $url,
			'comentario' => $comentario,
			'ip' => $ip
		);
		$sql = "INSERT INTO comentarios (msg, url, usuario, ip)
		VALUES (:comentario, :url, :usuario, :ip)";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}

	/**
	 * Función para recoger desde BD el listado de secciones de una determinada seccion
	 * el nombre puede variar segun el controlador que maneja dicha seccion.
	 */

	public function recoger_lista_bd($seccion)
	{
		$db = $this->getDataSource();
		$values = array('seccion' => $seccion);
		$sql = "SELECT * FROM tarsec WHERE seccion = :seccion";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	/**
	 * Función para recoger las subidas de una determinada sección (INT)
	 */
	public function recoger_subidas($seccion)
	{
		$db = $this->getDataSource();
		$values = array('seccion' => $seccion);
		$sql = "SELECT * FROM subidas
		LEFT JOIN users u ON subidas.usuario = u.id
		LEFT JOIN users_completed uc ON subidas.usuario = uc.gbl_id
		LEFT JOIN tarsec sec ON sec.enlace = SUBSTR(subidas.nsec, 16, 50)
		WHERE subidas.nsec = :seccion AND u.ban = 'N'
		ORDER BY subidas.date ASC
		";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	// LEFT JOIN tarsec sec ON sec.enlace = SUBSTR(subidas.nsec, 16, 50)
	public function recoger_pam_sec($seccion)
	{
		$db = $this->getDataSource();
		$values = array('seccion' => $seccion);
		$sql = "SELECT * FROM tarsec

		WHERE tarsec.enlace = :seccion";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	/**
	 * Función para dejar constancia en la base de datos de las subidas de los usuarios en una determinada sección.
	 */
	public function poner_subidas($usuario, $nsec, $fichero, $tipo, $ip, $texto, $descripcion = NULL, $miniatura = NULL)
	{
		$db = $this->getDataSource();

		if ($miniatura == NULL) {
			$miniatura = "";
		}

		$values = array(
			'usuario' => $usuario,
			'nsec' => $nsec,
			'fichero' => $fichero,
			'tipo' => $tipo,
			'texto' => $texto,
			'ip' => $ip,
			'descripcion' => $descripcion,
			'caratula' => $miniatura
		);
		$sql = "INSERT INTO subidas (usuario,nsec,fichero,caratula,tipo,texto, descripcion,ip) VALUES (:usuario,:nsec,:fichero,:caratula,:tipo,:texto,:descripcion,:ip)";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	// FUNCION EXCLUSIVA PARA AJAX, DEVUELVE LOS DATOS DE UNA SUBIDA DADA SU NOMBRE.

	public function infoforfile($dato)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato' => $dato
		);
		$sql = "SELECT * FROM subidas
		LEFT JOIN users_completed uc ON uc.gbl_id = subidas.usuario
		WHERE subidas.fichero = :dato";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	// FUNCION PARA VOTAR EN UN ARCHIVO/COMENTARIO/SECCION

	public function vota_out($usuario, $url, $voto)
	{
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario,
			'url' => $url,
			'voto' => $voto
		);
		$sql = "INSERT INTO votos (usuario,identificador,puntuacion) VALUES (:usuario,:url,:voto)";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function vota_out_del($usuario, $url)
	{
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario,
			'url' => $url
		);

		$sql = "DELETE FROM votos WHERE usuario = :usuario AND identificador = :url LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function vota_in($dato, $dato2)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato' => $dato,
			'dato2' => $dato2
		);
		$sql = "SELECT * FROM votos WHERE usuario = :dato AND identificador = :dato2";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	// Esta función devuelve LA MEDIA del número de estrellas
	public function estrellas($dato2)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato2' => $dato2
		);
		$sql = "SELECT identificador, SUM(puntuacion)/SUM(hsum) AS media, SUM(puntuacion) AS todas FROM estrellas
		WHERE identificador = :dato2
		GROUP BY identificador";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}
	public function dar_estrellas($usuario, $identificador ,$puntuacion) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "INSERT INTO estrellas (`usuario`,`identificador`,`puntuacion`) VALUES (".$usuario.", '".$identificador."', ".$puntuacion.")";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function actualizar_estrellas($usuario, $identificador ,$puntuacion) {
		$db = $this->getDataSource();
		$values = array();

		$sql = "UPDATE estrellas
		SET `puntuacion`=" . $puntuacion . "
		WHERE `usuario`=" . $usuario . " AND `identificador`='" . $identificador . "';  ";

		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function test_e_in($dato, $dato2)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato' => $dato,
			'dato2' => $dato2
		);
		$sql = "SELECT * FROM estrellas WHERE usuario = :dato AND identificador = :dato2";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}




	public function vota_count($dato)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato' => $dato
		);
		$sql = "SELECT * FROM votos WHERE identificador = :dato";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	/*public function tipocomentario($dato)
	{
		$db = $this->getDataSource();
		$values = array(
			'dato' => $dato
		);
		$sql = "SELECT *, SUBSTR(subidas.nsec,2,50) as pepino FROM subidas
		LEFT JOIN tarsec ON SUBSTR(subidas.nsec,2,50) = tarsec.enlace
		WHERE fichero = :dato";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}*/


	public function usuarios($pagina = 0, $search, $empresario = NULL)
	{

		if ($empresario != NULL) {
			$emp = "AND referido = " .$empresario;
		} else {
			$emp = "AND u.role != 'empresario' AND u.role != 'trabajador'";
		}

		$db = $this->getDataSource();
		$values = array(
			'pagina' => intval($pagina),
			'search' => $search
		);
		$sql = "SELECT * FROM users AS u
		LEFT JOIN users_completed uc ON u.id = uc.gbl_id
		WHERE (u.username LIKE '" . $search . "%' OR uc.u_name LIKE '" . $search . "%' OR uc.u_surname LIKE '" . $search . "%') ".$emp."
		GROUP BY u.id
		ORDER BY u.id ASC
		LIMIT 15
		OFFSET " . $pagina;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function empresarios($pagina = 0, $search)
	{

		$db = $this->getDataSource();
		$values = array(
			'pagina' => intval($pagina),
			'search' => $search
		);
		$sql = "SELECT * FROM users AS u
		LEFT JOIN users_completed uc ON u.id = uc.gbl_id
		WHERE (u.username LIKE '" . $search . "%' OR uc.u_name LIKE '" . $search . "%' OR uc.u_surname LIKE '" . $search . "%') AND u.role = 'empresario'
		GROUP BY u.id
		ORDER BY u.id ASC
		LIMIT 15
		OFFSET " . $pagina;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function numusuarios($empresario = NULL)
	{

		if ($empresario != NULL) {
			$emp = "WHERE referido = " .$empresario;
		} else {
			$emp = "";
		}

		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT COUNT(*) AS contador FROM users AS u
		LEFT JOIN users_completed uc ON u.id = uc.gbl_id
		".$emp."
		LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}



	public function numempresarios($empresario = NULL)
	{

		if ($empresario != NULL) {
			$emp = "WHERE referido = " .$empresario;
		} else {
			$emp = "WHERE role = 'empresario'";
		}

		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT COUNT(*) AS contador FROM users AS u
		LEFT JOIN users_completed uc ON u.id = uc.gbl_id
		".$emp."
		LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function regcom($usuario)
	{

		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM users
		LEFT JOIN users_completed ON users.id = users_completed.gbl_id
		WHERE users.id =" . $usuario . " LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


// FUNCION PARA CAMBIAR LA INFORMACIÓN BASICA SIN ALTERAR LA CONTRASEÑA
	public function changeinfobase($id, $usuario, $activo, $baneado, $rol)
	{

		$db = $this->getDataSource();
		$values = array();
		$SQL2 = "
		UPDATE `users` SET `username`='" . $usuario . "', `role`='" . $rol . "', `active`='" . $activo . "', `ban`='" . $baneado . "' WHERE  `id`=" . $id . ";
		";
		$result = $db->fetchAll($SQL2, $values);
		return $result;

	}

	public function removeuserbyemail($email)
	{

		$db = $this->getDataSource();
		$values = array();
		$sql = "DELETE FROM users WHERE username = '" . $email . "' LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function completar_usuario_editar($gbl_id, $u_name, $u_surname, $u_date, $dni, $localidad, $fotoperfil)
	{
		$db = $this->getDataSource();
		$values = array(
			'gbl_id' => $gbl_id,
			'u_name' => $u_name,
			'u_surname' => $u_surname,
			'u_date' => $u_date,
			'dni' => $dni,
			'localidad' => $localidad,
			'fotoperfil' => $fotoperfil
		);
		$sql = "UPDATE users_completed
		SET `u_name`='" . $u_name . "',
		`u_surname`='" . $u_surname . "',
		`u_date`='" . $u_date . "',
		`dni`='" . $dni . "',
		`localidad`='" . $localidad . "',
		`fotoperfil`='NOFOTO'
		WHERE `gbl_id`=" . $gbl_id . ";  ";
		$result = $db->fetchAll($sql, $values);
		// $merged = call_user_func_array('array_merge', $result);
		return $result;
	}


	public function changepassword($id, $password)
	{
		$passwordHasher = new BlowfishPasswordHasher(); // INICIO DE LA ENCRIPTACION
		$pe = $passwordHasher->hash($password); // PASSWORD ENCRIPTADO
		$snp = encriptar($password);
		$db = $this->getDataSource(); // DATABASE ACTUAL
		$values = array(
			'id' => $id,
			'password' => $pe,
			'snp' => $snp
		);

		$sql = "UPDATE users
		SET `password` = '" . $pe . "', `snp` = '".$snp."'
		WHERE `id`=" . $id . "";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function gallery($id)
	{

		$db = $this->getDataSource();
		$values = array();
		$sql = "
		SELECT * FROM subidas AS sub
		LEFT JOIN users u ON sub.usuario = u.id
		LEFT JOIN users_completed uc ON u.id = uc.gbl_id
		LEFT JOIN tarsec ts ON SUBSTR(sub.nsec, 2, 100) = ts.enlace
		WHERE sub.nsec != 'MDT' AND u.id =" . $id;
		$result = $db->fetchAll($sql, $values);
		return $result;

	}


	public function listarsecciones()
	{

		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM tarsec";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function whoisfile($id)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM subidas WHERE id = " . $id;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function deletefile($id)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "DELETE FROM subidas WHERE id = " . $id;
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function mensajesto($id)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT fr.gbl_id*tx.gbl_id AS `group`, mensajes.id, mensajes.`from`, mensajes.`to`, mensajes.sms, mensajes.date,mensajes.leido, ua.ban, ub.ban,  fr.*, tx.* FROM mensajes
		LEFT JOIN users ua ON ua.id = mensajes.`from`
		LEFT JOIN users ub ON ua.id = mensajes.`to`
		LEFT JOIN users_completed fr ON fr.gbl_id = mensajes.`from`
		LEFT JOIN users_completed tx ON tx.gbl_id = mensajes.`to`
		WHERE mensajes.`from` = " . $id . " OR mensajes.`to` = " . $id . "
		GROUP BY `group`
		";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function mensajesfrom($id, $to)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "
		SELECT * FROM mensajes AS sms
		LEFT JOIN users_completed uc ON sms.`from` = uc.gbl_id
		LEFT JOIN users_completed uc2 ON sms.`from` = uc.gbl_id
		WHERE sms.`from` = " . $id . " AND sms.`to` = " . $to . " OR sms.`from` = " . $to . " AND sms.`to` = " . $id . "
		GROUP BY sms.id
		ORDER BY sms.date ASC
		";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function enviarsms($from, $to, $sms)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "INSERT INTO mensajes (`from`,`to`,`sms`) VALUES (" . $from . "," . $to . ",'" . $sms . "')";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}


	public function jsonusers($usuario, $busqueda)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM users_completed AS uc
		LEFT JOIN users ON users.id = uc.gbl_id
		WHERE uc.gbl_id != " . $usuario . " AND uc.dni LIKE '%" . $busqueda . "%' LIMIT 10";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function e_jsonusers($empresa, $usuario, $busqueda)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM users_completed AS uc
		LEFT JOIN users us ON us.id = uc.gbl_id
		WHERE uc.gbl_id != " . $usuario . " AND uc.u_name LIKE '%" . $busqueda . "%' OR uc.u_surname LIKE '%" . $busqueda . "%' LIMIT 10";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function ticksverdes($from, $to)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "UPDATE mensajes SET `leido`='S'  WHERE  `from`=" . $from . " AND `to`=" . $to . ";";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function MDTfiles($from)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM subidas
		WHERE subidas.usuario = " . $from;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function buscarsubida($from)
	{
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM subidas WHERE fichero = '" . $from . "'";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	// COMPRUEBA EL ESTADO DE UNA MDT
	public function testmdt($user) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM emovers
		LEFT JOIN secciones ON secciones.mpt_id = emovers.id
		WHERE gbl_id =" . $user . "
		ORDER BY secciones.posicion ASC
		";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function insertimginto($user, $data, $into) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "INSERT INTO emovers (`gbl_id`,`".$into."`) VALUES (" . $user . ",'" . $data . "')";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function insertinfointo($user, $data) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "INSERT INTO emovers (`gbl_id`,`d_name`) VALUES (" . $user . ",'" . $data . "')";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function updateimginto($user, $data, $into) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "UPDATE emovers SET `".$into."`='".$data."' WHERE `gbl_id`=" . $user;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function updateinfointo($user, $nombre_mdt, $f1, $f1z, $f2, $f2z, $bgpanel, $fgpanel, $bgpanel2, $fgpanel2, $fgcuerpo, $plantilla, $d_design, $d_pxbmenu, $d_pxbrmenu, $d_pxcmenu, $clogo = NULL, $cfondo = NULL, $show = 1) {
		$db = $this->getDataSource();
		if (empty($d_design) || !isset($d_design)) {
			$d_design = 2;
		}

		$values = array();
		$sql = "UPDATE emovers
		SET `d_name`='".$nombre_mdt."',
		`d_fontheader`='".$f1."',
		`d_fontheader_size`=".$f1z.",
		`d_fontcentral`='".$f2."',
		`d_fontcentral_size`=".$f2z.",
		`d_bgheader`='".$bgpanel."',
		`d_fgheader`='".$fgpanel."',
		`d_bgfooter`='".$bgpanel2."',
		`d_fgfooter`='".$fgpanel2."',
		`d_fgbody`='".$fgcuerpo."',
		`d_bgbody`='".$plantilla."',
		`d_design`=".$d_design.",
		`d_pxbmenu`=".$d_pxbmenu.",
		`d_pxbrmenu`=".$d_pxbrmenu.",
		`d_pxcmenu`=".$d_pxcmenu.",
		`clogo`=".$clogo.",
		`cfondo`='".$cfondo."',
		`show`=".$show."
		WHERE `gbl_id`=".$user;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	/*
	 * Esta función inserta en la base de datos una nueva sección (NO LA ACTUALIZA)
	 */
	public function nuevaseccion($dato1,$dato2,$dato3,$dato4,$dato5,$dato6,$dato7,$dato8) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "INSERT INTO secciones (`mpt_id`,`name`,`bgmenu`,`fgmenu`,`bordercolor`,`icono_menu`,`portada_menu`,`contenido`) VALUES (" . $dato1 . ",'" . $dato2 . "','" . $dato3 . "','" . $dato4 . "','" . $dato5 . "','" . $dato6 . "','" . $dato7 . "','" . $dato8 . "')";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	/*
	 * Función para EDITAR las secciones
	 * $DATO 1 SIEMPRE ES LA ID DE LA SECCION QUE SE ESTA EDITANDO
	 * NO SE NECESITA LA ID DEL USUARIO ACTIVO NI LA DEL PROPIETARIO
	 */

	public function nuevaseccion_editar($dato1,$dato2,$dato3,$dato4,$dato5,$dato6,$dato7,$dato8) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "UPDATE secciones
		SET name='".$dato2."',
		bgmenu='".$dato3."',
		fgmenu='".$dato4."',
		bordercolor='".$dato5."',
		icono_menu='".$dato6."',
		portada_menu='".$dato7."',
		contenido='".$dato8."'
		WHERE `id`=".$dato1.";";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function ajaxseccion($id) {

		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM secciones
		WHERE id =" . $id;
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function update_position($dato1, $dato2) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "UPDATE secciones
		SET posicion='".$dato2."'
		WHERE `id`=".$dato1.";";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function update_portada($dato1, $dato2) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "UPDATE subidas
		SET caratula='".$dato2."'
		WHERE `id`=".$dato1.";";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function test_borrado_seccion($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id,
		);
		$sql = "SELECT em.gbl_id FROM secciones
		LEFT JOIN emovers em ON secciones.mpt_id = em.id
		WHERE secciones.id = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function borrar_seccion($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id,
		);
		$sql = "DELETE FROM secciones WHERE id = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function averiguar_nombre_seccion($name) {
		$db = $this->getDataSource();
		$values = array(
			'enlace' => $name
		);
		$sql = "SELECT * FROM tarsec WHERE enlace = :enlace";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}



	public function duser($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id
		);
		$sql = "SELECT * FROM users
		LEFT JOIN users_completed uc ON uc.gbl_id = users.id
		WHERE users.id = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	// FUNCIÓN QUE NOS DEVUELVE LAS SUBIDAS DE LA ÚLTIMA SEMANA DE UN USUARIO, OBTIENE ÚNICAMENTE LAS SUBIDAS AL FRONTEND

	public function lastweekuploads($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id
		);
		$sql = "SELECT *
		FROM subidas
		LEFT JOIN users_completed uc ON uc.gbl_id = subidas.usuario
		WHERE DATEDIFF(NOW(), subidas.date) <= 6 AND subidas.nsec != 'MDT' AND subidas.usuario = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function ultimassubidasmes($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id
		);
		$sql = "SELECT *
		FROM subidas
		LEFT JOIN users_completed uc ON uc.gbl_id = subidas.usuario
		WHERE DATEDIFF(NOW(), subidas.date) <= 30 AND subidas.nsec != 'MDT' AND subidas.usuario = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function subidastotales($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id
		);
		$sql = "SELECT *
		FROM subidas
		LEFT JOIN users_completed uc ON uc.gbl_id = subidas.usuario
		WHERE subidas.nsec != 'MDT' AND subidas.usuario = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function foronuevaseccion($nombre, $imagen, $enlace){

		$db = $this->getDataSource();
		$values = array();
		$sql = "INSERT INTO tarsec (`seccion`,`nombre`,`imagen`,`enlace`,`tipo`,`ps`) VALUES ('foro','".$nombre."','".$imagen."','movers/foro/".$enlace."', 'mix', 1)";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}


	public function editarperfil($gblid, $nickname, $fotoperfil, $localidad = NULL, $pcontacto = NULL, $tcontacto = NULL) {


		$db = $this->getDataSource();
		$values = array();



		if ($localidad != NULL) {
			$sql = "UPDATE users_completed
			SET `dni`='".$nickname."', `fotoperfil`='".$fotoperfil."', `localidad`='".$localidad."',`pcontacto`='".$pcontacto."', `tcontacto`=".$tcontacto." WHERE `gbl_id`=".$gblid.";";
		} else {
			$sql = "UPDATE users_completed
			SET `dni`='".$nickname."', `fotoperfil`='".$fotoperfil."'
			WHERE `gbl_id`=".$gblid.";";
		}

		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function busquedaensubidas($busqueda) {
		$db = $this->getDataSource();
		$values = array(
			'busqueda' => $busqueda
		);
		$sql = "SELECT * FROM subidas
		LEFT JOIN users_completed uc ON uc.gbl_id = subidas.usuario
		WHERE (subidas.texto LIKE '%".$busqueda."%' OR uc.dni LIKE '%".$busqueda."%') AND subidas.nsec != 'MDT';";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function recogerusuarios() {
		$db = $this->getDataSource();
		$values = array(
			'busqueda' => $busqueda
		);
		$sql = "SELECT * FROM users_completed";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}


	public function testmpt($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id
		);
		$sql = "SELECT * FROM emovers WHERE gbl_id = :id";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}



	/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	/// CONTROL DE PRESENCIA -- CDP
	///

	public function testcdp($id) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $id
		);
		$sql = "SELECT * FROM users
		LEFT JOIN cdp ON users.id = cdp.gbl_id
		WHERE users.id = :id
		LIMIT 1";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function cdp1($gblid, $status) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $gblid,
			'status' => $status
		);
		$sql = "UPDATE users
		SET `cpd`=:status
		WHERE `id`=:id";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function cdp2($gblid, $status) {
		$db = $this->getDataSource();
		$values = array(
			'id' => $gblid,
			'status' => $status
		);
		$sql = "INSERT INTO cdp (gbl_id, estado)
		VALUES (:id, :status)";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}




	public function cdp_fecha_old($empresario) {
		$db = $this->getDataSource();
		$values = array(
			'empresario' => $empresario,
		);
		$sql = "SELECT cdp.gbl_id, cdp.estado, cdp.tiempo, us.username, us.referido, uc.u_name, uc.u_surname
		FROM cdp
		LEFT JOIN users us ON us.id = cdp.gbl_id
		LEFT JOIN users_completed uc ON cdp.gbl_id = uc.gbl_id
		WHERE us.referido = ".$empresario."
		ORDER BY cdp.tiempo ASC";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function cdp_fecha($empresario,$fecha) {
		$db = $this->getDataSource();
		$values = array(
			'empresario' => $empresario,
			'fecha' => $fecha
		);
		$sql = "SELECT cdp.gbl_id, cdp.estado, cdp.tiempo, us.username, us.referido, uc.u_name, uc.u_surname
		FROM cdp
		LEFT JOIN users us ON us.id = cdp.gbl_id
		LEFT JOIN users_completed uc ON cdp.gbl_id = uc.gbl_id
		WHERE us.referido = ".$empresario."
		AND cdp.id IN (
		SELECT MAX(cdp.id)
		FROM cdp
		WHERE cdp.tiempo BETWEEN '".$fecha." 00:00:00' AND '".$fecha." 23:59:59'
		GROUP BY cdp.gbl_id
	)   ORDER BY cdp.tiempo ASC";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function rhoras($empleado, $tiempo1 = NULL, $tiempo2 = NULL) {
	$db = $this->getDataSource();

		// Si especificamos un intervalo de tiempo la consulta será mas especifica.
	if ($tiempo1 != NULL) {
		$time = "AND tiempo BETWEEN '".$tiempo1."' AND '".$tiempo2."' ";
	} else {
		$time = "";
	}

	$values = array();
	$sql = "SELECT * FROM cdp WHERE gbl_id = " . $empleado . " " . $time;
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function suma_visita_seccion($seccion) {
	$db = $this->getDataSource();
	$values = array();
	$sql = "UPDATE tarsec SET contador=contador + 1 WHERE enlace='".$seccion."'";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function tarseclist() {
	$db = $this->getDataSource();
	$values = array();
	$sql = "SELECT * FROM tarsec WHERE seccion != 'profesionales' ORDER BY contador DESC";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function estrellas_globales($usuario = NULL) {

	if ($usuario != NULL) {
		$limit = "";
	} else {
		$limit = "LIMIT 25";
	}

	$db = $this->getDataSource();
	$values = array();
	$sql = "
	SELECT estrellas.usuario, subidas.nsec, subidas.fichero, subidas.caratula, subidas.tipo, subidas.texto, uc.dni, subidas.usuario , SUM(puntuacion) AS puntaje
	FROM estrellas
	LEFT JOIN subidas ON subidas.fichero = estrellas.identificador
	LEFT JOIN users_completed uc ON uc.gbl_id = estrellas.usuario
	GROUP BY identificador
	ORDER BY puntaje DESC
	".$limit."
	";
	$result = $db->fetchAll($sql, $values);
	return $result;
}



public function ban($usuario, $ban) {
	$db = $this->getDataSource();
	$values = array();
	$sql = "UPDATE users SET ban='".$ban."' WHERE id=".$usuario."";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function listadeipsbaneadas($data) {
	$db = $this->getDataSource();
	$values = array();
	$sql = "UPDATE dipblock SET `ips`='".$data."' WHERE `id`= 1";
	$result = $db->fetchAll($sql, $values);
	return $result;
}

public function getipbaneados() {
	$db = $this->getDataSource();
	$values = array();
	$sql = "SELECT * FROM dipblock WHERE id = 1 LIMIT 1";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function ponip($usuario, $ip) {
	$db = $this->getDataSource();
	$values = array();
	$sql = "UPDATE users SET `ip`='".$ip."' WHERE `id`= ".$usuario."";
	$result = $db->fetchAll($sql, $values);
	return $result;
}

public function updatefpd($id, $foto) {
	$db = $this->getDataSource();
	$values = array();
	$sql = "UPDATE users_completed SET `fotoperfil`='".$foto."' WHERE `gbl_id`= ".$id."";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function get_global_ranking_comentarios($usuario = NULL) {

	if ($usuario != NULL) {
		$condicion = "WHERE c.usuario = " . $usuario;
	} else {
		$condicion = "";
	}


	$db = $this->getDataSource();
	$values = array();
	$sql = "
	SELECT s.id ,s.texto AS titulo, t.enlace, s.usuario, COUNT(c.id) AS c_count FROM comentarios AS c
	LEFT JOIN subidas s ON s.fichero = c.url
	INNER JOIN tarsec t ON SUBSTR(s.nsec, 2, 200) = t.enlace
	".$condicion."
	GROUP BY s.fichero
	ORDER BY c_count DESC
	LIMIT 50
	";
	$result = $db->fetchAll($sql, $values);
	return $result;
}

public function likesig($usuario = NULL) {

	if ($usuario != NULL) {
		$condicion = "WHERE votos.usuario = ".$usuario." AND s.usuario != ".$usuario."";
	} else {
		$condicion = "";
	}


	$db = $this->getDataSource();
	$values = array();
	$sql = "
	SELECT *, COUNT(puntuacion) AS likes  FROM votos
	LEFT JOIN subidas s ON s.fichero = votos.identificador
	INNER JOIN tarsec t ON t.enlace = SUBSTR(s.nsec, 2, 200)
	".$condicion."
	GROUP BY votos.identificador
	ORDER BY likes DESC
	";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function likesig2($usuario = NULL) {

	if ($usuario != NULL) {
		$condicion = "WHERE votos.usuario = ".$usuario."";
	} else {
		$condicion = "";
	}


	$db = $this->getDataSource();
	$values = array();
	$sql = "
	SELECT *, COUNT(puntuacion) AS likes  FROM votos
	LEFT JOIN subidas s ON s.fichero = votos.identificador
	INNER JOIN tarsec t ON t.enlace = SUBSTR(s.nsec, 2, 200)
	".$condicion."
	GROUP BY votos.identificador
	";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function borrarcomentario($id) {
	$db = $this->getDataSource();
	$values = array(
		'id' => $id
	);

	$sql = "DELETE FROM comentarios WHERE id = :id";
	$result = $db->fetchAll($sql, $values);
	return $result;

}


public function bsubudaid($id) {
	$db = $this->getDataSource();
	$values = array(
		'id' => $id
	);

	$sql = "SELECT * FROM subidas WHERE id = :id LIMIT 1";
	$result = $db->fetchAll($sql, $values);
	return $result;
}

public function rankingdesubidas() {

	$db = $this->getDataSource();
	$values = array(
			//'id' => $id
	);

	$sql = "SELECT *,  COUNT(usuario) AS subidas_realizadas
	FROM subidas
	INNER JOIN users_completed uc ON subidas.usuario = uc.gbl_id
	GROUP BY usuario
	ORDER BY subidas_realizadas DESC
	LIMIT 3";
	$result = $db->fetchAll($sql, $values);
	return $result;

}


public function ver_mpts() {

	$db = $this->getDataSource();
	$values = array(
			//'id' => $id
	);

	$sql = "SELECT * FROM emovers
	INNER JOIN users_completed uc ON uc.gbl_id = emovers.gbl_id ";
	$result = $db->fetchAll($sql, $values);
	return $result;

}


	public function megusta($idgbl, $megusta) {

		$db = $this->getDataSource();
		$values = array(
			'idgbl' => $idgbl,
			'megusta' => $megusta
		);
		$sql = "INSERT INTO likes (de, para)
		VALUES (:idgbl, :megusta)";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function nomegusta($idgbl, $megusta) {

		$db = $this->getDataSource();
		$values = array(
			'idgbl' => $idgbl,
			'megusta' => $megusta
		);
		//$sql = "DELETE FROM comentarios WHERE id = :id";
		$sql = "DELETE FROM likes WHERE (`de` = ".$idgbl." AND `para` = ".$megusta.")";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

public function checkmegusta($idgbl, $megusta) {
			$db = $this->getDataSource();
	$values = array(
		'idgbl' => $idgbl,
		'megusta' => $megusta
	);

	$sql = "SELECT * FROM likes WHERE (`de` = ".$idgbl." AND `para` = ".$megusta.")";
	$result = $db->fetchAll($sql, $values);
	return $result;
}


public function delsms($id, $usuario) {
	$db = $this->getDataSource();
	$ds = encriptar("<i>Mensaje eliminado</i>");
	$sql = "UPDATE mensajes SET sms='".$ds."' WHERE (`from`=". $usuario ." AND `id`=" . $id . ")";
	$result = $db->fetchAll($sql, $values);
	return $result;
}

public function addgps($long, $lat, $usuario) {

	$db = $this->getDataSource();
	$values = array(
		'long' => $long,
		'lat' => $lat,
		'usuario' => $usuario
	);
	$sql = "INSERT INTO gps (gbl_id, longitud, latitud)
			VALUES (:usuario, :long, :lat)";
	$result = $db->fetchAll($sql, $values);
	return $result;

}

public function retgps($usuario, $fecha = 0) {
	$db = $this->getDataSource();
	$values = array();
	$sql = "SELECT * FROM gps WHERE gbl_id = '" . $usuario . "' AND fecha LIKE '".$fecha."%' ORDER BY fecha ASC";
	$result = $db->fetchAll($sql, $values);
	return $result;
}

	public function sugeridos($where) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM users_completed " . $where . " ORDER BY RAND() LIMIT 15";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function sugeridos2($where) {
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM users_completed " . $where . " ORDER BY RAND() LIMIT 150";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function encuentro($usuario, $like, $time) {
		if ($like == "hombre") {
			$likesql = "AND uc.soy = 'hombre'";
		} else if ($busca == "mujer") {
			$likesql = "AND uc.soy = 'mujer'";
		} else {
			$likesql = "";
		}
		$db = $this->getDataSource();
		$values = array();
		$sql = "SELECT * FROM gps
		LEFT JOIN users_completed uc ON gps.gbl_id = uc.gbl_id
		WHERE gps.gbl_id != " . $usuario . " " . $likesql . "AND gps.fecha LIKE '".$time."%' LIMIT 100";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}




	// FORUM
	public function get_forum($param) {
		$db = $this->getDataSource();
		$values = array(
			'subforo' => $param
		);
		// si es un subforo cogemos el ID del subforo y lo ponemos, mejor poner solo un nivel
		$sql = "SELECT * FROM subforo
			LEFT JOIN users_completed uc ON subforo.creador = uc.gbl_id
			WHERE sub_id = :subforo AND subforo.hilo = 'NO' ORDER BY subforo.id DESC";


		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function get_hilos($param = NULL) {
		$db = $this->getDataSource();
		$values = array(
			'subforo' => $param
		);
		// si es un subforo cogemos el ID del subforo y lo ponemos, mejor poner solo un nivel
		$sql = "SELECT * FROM subforo
		LEFT JOIN users_completed uc ON subforo.creador = uc.gbl_id
		WHERE sub_id = :subforo AND subforo.hilo = 'SI'
		ORDER BY subforo.id DESC";


		$result = $db->fetchAll($sql, $values);
		return $result;
	}



	public function get_hilo($param = NULL) {
		$db = $this->getDataSource();

		$values = array(
			'subforo' => $param
		);
		// si es un subforo cogemos el ID del subforo y lo ponemos, mejor poner solo un nivel
		$sql = "SELECT * FROM subforo WHERE id = :subforo ORDER BY id DESC LIMIT 1";


		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function get_subf($param = NULL) {
		$db = $this->getDataSource();

		$values = array(
			'subforo' => $param
		);
		// si es un subforo cogemos el ID del subforo y lo ponemos, mejor poner solo un nivel
		$sql = "SELECT * FROM subforo WHERE sub_id = :subforo ORDER BY id DESC";

		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	public function insert_in_forum($gblid, $tipo, $foro, $nombre) {
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $gblid,
			'tipo' => $tipo,
			'foro' => $foro,
			'nombre' => $nombre
		);
		$sql = "INSERT INTO subforo (sub_id, creador, hilo, nombre) VALUES (:foro, :usuario, :tipo, :nombre)";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

/*
 * NOTIFICACIONES
 */
	public function get_notificaciones($param) {
		$db = $this->getDataSource();
		$values = array(
			'subforo' => $param
		);
		// si es un subforo cogemos el ID del subforo y lo ponemos, mejor poner solo un nivel
		$sql = "SELECT * FROM notificaciones WHERE gbl_id = :subforo ORDER BY leido,fecha DESC LIMIT 25";

		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function put_notificacion($para, $notificacion, $url, $icono) {
			$db = $this->getDataSource();
			$values = array(
				'para' => $para,
				'notificacion' => $notificacion,
				'icono' => $icono,
				'url' => $url
			);
			$sql = "INSERT INTO notificaciones (gbl_id, icono, notificacion, url) VALUES (:para, :icono, :notificacion, :url)";
			$result = $db->fetchAll($sql, $values);
			return $result;

	}

	public function update_notificaciones($param) {
		$db = $this->getDataSource();
		$values = array(
			'subforo' => $param
		);
		// si es un subforo cogemos el ID del subforo y lo ponemos, mejor poner solo un nivel
		$sql = "UPDATE notificaciones SET leido = 1 WHERE (`gbl_id`=". $param .")";

		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function megustan($usuario) {
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario
		);
		$sql = "SELECT * FROM likes LEFT JOIN users_completed ON likes.para = users_completed.gbl_id WHERE de = :usuario ORDER BY fecha DESC ";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function lesgusto($usuario) {
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario
		);
		$sql = "SELECT * FROM likes LEFT JOIN users_completed ON likes.de = users_completed.gbl_id WHERE para = :usuario ORDER BY fecha DESC ";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	public function afinidades($usuario) {
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $usuario
		);
		$sql = "SELECT * FROM likes
				INNER JOIN likes l2 ON likes.de = l2.para
				LEFT JOIN users_completed ON l2.de = users_completed.gbl_id
				WHERE (likes.de+likes.para) = (l2.de+l2.para) AND likes.de = :usuario
				GROUP BY (likes.de+likes.para)
				ORDER BY likes.fecha DESC ";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}

	// ESTA FUNCION NO DEBE USARSE; ES UNICAMENTE PARA CONSULTAS SENCILLAS DESDE LOS CONTROLADORES
	public function query($query) {
		$db = $this->getDataSource();
		$values = array();
		$sql = $query;
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


     /* FUNCION SQL DE USUARIOS CERCANOS - ESPECIFICAMOS UNA PERIFERIA PARA ACOTAR LA BÚSQUEDA*/
	public function usuarios_cercanos($lat, $lng, $min_lat, $max_lat, $min_lng, $max_lng, $nouser, $distancia) {
		$db = $this->getDataSource();
		$values = array(
			'lat' => $lat,
			'lng' => $lng,
			'min_lat' => $min_lat,
			'max_lat' => $max_lat,
			'min_lng' => $min_lng,
			'max_lng' => $max_lng,
			'nouser' => $nouser,
			'distance' => $distancia

		);
		$sql = "SELECT *, ( 6371 * ACOS(
                                             COS( RADIANS( :lat ) )
                                             * COS(RADIANS( lat ) )
                                             * COS(RADIANS( lng )
                                             - RADIANS( :lng ) )
                                             + SIN( RADIANS( :lat ) )
                                             * SIN(RADIANS( lat ) )
                                            )
                               ) AS distance
                     FROM users_completed
                     WHERE (lat BETWEEN :min_lat AND :max_lat)
                     AND (lng BETWEEN :min_lng AND :max_lng)
                     AND gbl_id != :nouser
                     HAVING distance < :distance
                     ORDER BY distance ASC";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}


	/*
	 * Registra la API de Google Cloud del usuario
	 */
	public function registrarApi($user, $api) {
		$db = $this->getDataSource();
		$values = array(
			'usuario' => $user,
			'api' => $api
		);
		$sql = "UPDATE users SET apigcloud = :api WHERE (`id`=:usuario)";
		$result = $db->fetchAll($sql, $values);
		return $result;

	}

	public function trabajadores($empresario) {
		$db = $this->getDataSource();
		$values = array(
			'empresario' => $empresario
		);
		$sql = "SELECT * FROM users
			LEFT JOIN users_completed uc ON uc.gbl_id = users.id
			WHERE users.referido = :empresario ORDER BY fecha DESC ";
		$result = $db->fetchAll($sql, $values);
		return $result;
	}







}


//UPDATE `c2c`.`users` SET `username`='sdanielcha@outlook.com', `role`='usuario2', `active`='S', `ban`='N' WHERE  `id`=3;
