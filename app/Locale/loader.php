<?php

// DEFINIMOS LAS KEYS PARA EL CIFRADO DE INFORMACIÓN
// Las keys fuerón generadas aleatoriamente
define('FIRSTKEY','kC3fLUC6iQ9eO9K1lKAtnqiPJSHpj0PhnaakOAGy7L8=');
define('SECONDKEY','PhtZjN6LzuR2DX7Q0zcP3oellkbEnKjyrNLEMql0GAZRUGNg0ynbm7RkWfduanWZ9G8b8KTymAyFkei5M8rP4Q==');



/*
 * PARÁMETROS DE LA FUNCIÓN
 * $STRING = Es el nombre de la cadena de texto que queremos buscar, si no la encuentra se muestra tal cual
 * $RETURN = Es para que la función nos devuelva directamente un ECHO o un RETURN para segun que situaciones
 * $LANG = Si es especificado se cambiará el idioma para esa petición
 * $PERMANENT = Si lo ponemos en true, guardará el idioma que contenga LANG en sesión.
 *
 * Desde el APP controller se establece un idioma por defecto en caso de no tenerlo (Así evitamos las dobles sesiones)
 */
function _t($string, $return = false , $lang = NULL, $permantent = false ) {

	if ($permantent == true ) {
		$_SESSION["idioma2"] = $lang;
	}
	if ($lang == NULL) { $lang = $_SESSION["idioma2"];}
	$url_strings = getcwd() . DS . '..' . DS .'Locale' . DS . $lang . DS . 'strings.json';
	$strings = file_get_contents($url_strings);
	$array_strings = json_decode($strings, true);
	if(isset($array_strings[$string])) {
		if ($return == false) {
			echo $array_strings[$string];
		} else {
			return $array_strings[$string];
		}
	} else {
		if ($return == false) {
			echo $string;
		} else {
			return $string;
		}
	}
}

function base64url_encode($data) {
	return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function base64url_decode($data) {
	return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
}


function encriptar($data)
{
	$first_key = base64_decode(FIRSTKEY);
	$second_key = base64_decode(SECONDKEY);

	$method = "aes-256-cbc";
	$iv_length = openssl_cipher_iv_length($method);
	$iv = openssl_random_pseudo_bytes($iv_length);

	$first_encrypted = openssl_encrypt($data,$method,$first_key, OPENSSL_RAW_DATA ,$iv);
	$second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

	$output = base64_encode($iv.$second_encrypted.$first_encrypted);
	return base64url_encode($output);
}


function desencriptar($input)
{
	$first_key = base64_decode(FIRSTKEY);
	$second_key = base64_decode(SECONDKEY);
	$mix = base64_decode(base64url_decode($input));

	$method = "aes-256-cbc";
	$iv_length = openssl_cipher_iv_length($method);

	$iv = substr($mix,0,$iv_length);
	$second_encrypted = substr($mix,$iv_length,64);
	$first_encrypted = substr($mix,$iv_length+64);

	$data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);
	$second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);

	if (hash_equals($second_encrypted,$second_encrypted_new))
		return $data;

	return false;
}

// Funcion para agrupar arrays
function group_by($key, $data) {
	$result = array();
	foreach($data as $val) {
		if(array_key_exists($key, $val)){
			$result[$val[$key]][] = $val;
		}else{
			$result[""][] = $val;
		}
	}
	return $result;
}

function groupby($array, $keys=array())
{
	$return = array();
	foreach ($array as $val) {
		$final_key = "";
		foreach ($keys as $theKey) {
			$final_key .= $val[$theKey] . "_";
		}
		$return[$final_key][] = $val;
	}
	return $return;
}

function getRealIP() {
   		if (!empty($_SERVER['HTTP_CLIENT_IP']))
        return $_SERVER['HTTP_CLIENT_IP'];
    	if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    	return $_SERVER['REMOTE_ADDR'];
		}


function entre($content, $start = "", $end = "")
		{
			$n = explode($start, $content);
			$result = array();
			foreach ($n as $val) {
				$pos = strpos($val, $end);
				if ($pos !== false) {
					$result[] = substr($val, 0, $pos);
				}
			}
			return $result;
		}

function merge_two_arrays($array1,$array2) {
	$data = array();
	$arrayAB = array_merge($array1,$array2);
	foreach ($arrayAB as $value) {
		$id = $value['id'];
		if (!isset($data[$id])) {
			$data[$id] = array();
		}
		$data[$id] = array_merge($data[$id],$value);
	}
	return $data;
}

function calcula_distancia($point1_lat, $point1_long, $point2_lat, $point2_long, $unit = 'km', $decimals = 2) {
	// Cálculo de la distancia en grados
	$degrees = rad2deg(acos((sin(deg2rad($point1_lat))*sin(deg2rad($point2_lat))) + (cos(deg2rad($point1_lat))*cos(deg2rad($point2_lat))*cos(deg2rad($point1_long-$point2_long)))));

	// Conversión de la distancia en grados a la unidad escogida (kilómetros, millas o millas naúticas)
	switch($unit) {
		case 'km':
			$distance = $degrees * 111.13384; // 1 grado = 111.13384 km, basándose en el diametro promedio de la Tierra (12.735 km)
			break;
		case 'mi':
			$distance = $degrees * 69.05482; // 1 grado = 69.05482 millas, basándose en el diametro promedio de la Tierra (7.913,1 millas)
			break;
		case 'nmi':
			$distance =  $degrees * 59.97662; // 1 grado = 59.97662 millas naúticas, basándose en el diametro promedio de la Tierra (6,876.3 millas naúticas)
	}
	return round($distance, $decimals);
}


if ( ! function_exists( 'array_key_last' ) ) {
	/**
	 * Polyfill for array_key_last() function added in PHP 7.3.
	 *
	 * Get the last key of the given array without affecting
	 * the internal array pointer.
	 *
	 * @param array $array An array
	 *
	 * @return mixed The last key of array if the array is not empty; NULL otherwise.
	 */
	function array_key_last( $array ) {
		$key = NULL;

		if ( is_array( $array ) ) {

			end( $array );
			$key = key( $array );
		}

		return $key;
	}
}


function edad($fecha_nacimiento){
	$dat = explode("/", $fecha_nacimiento);
	$edad2 = $dat[2] . "-" . $dat[0] . "-" - $dat[1];

	$dia=date("d");
	$mes=date("m");
	$ano=date("Y");

	$dianaz=date("d",strtotime($edad2));
	$mesnaz=date("m",strtotime($edad2));
	$anonaz=date("Y",strtotime($edad2));
	if (($mesnaz == $mes) && ($dianaz > $dia)) {
		$ano=($ano-1); }
	if ($mesnaz > $mes) {
		$ano=($ano-1);}
	$edad=($ano-$anonaz);
	return $edad;

}

function whoip() {
	$ipaddress = '';
	if (getenv('HTTP_CLIENT_IP'))
		$ipaddress = getenv('HTTP_CLIENT_IP');
	else if(getenv('HTTP_X_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
	else if(getenv('HTTP_X_FORWARDED'))
		$ipaddress = getenv('HTTP_X_FORWARDED');
	else if(getenv('HTTP_FORWARDED_FOR'))
		$ipaddress = getenv('HTTP_FORWARDED_FOR');
	else if(getenv('HTTP_FORWARDED'))
		$ipaddress = getenv('HTTP_FORWARDED');
	else if(getenv('REMOTE_ADDR'))
		$ipaddress = getenv('REMOTE_ADDR');
	else
		$ipaddress = 'UNKNOWN';
	return $ipaddress;
}


// PERIMETREAR UN ÁREA DADO UNA DISTANCIA EN UNAS COORDENADAS ESPECIFICADAS
function getBoundaries($lat, $lng, $distance = 1, $earthRadius = 6371)
{
	$return = array();

	// Los angulos para cada dirección
	$cardinalCoords = array('north' => '0',
		'south' => '180',
		'east' => '90',
		'west' => '270');

	$rLat = deg2rad($lat);
	$rLng = deg2rad($lng);
	$rAngDist = $distance/$earthRadius;

	foreach ($cardinalCoords as $name => $angle)
	{
		$rAngle = deg2rad($angle);
		$rLatB = asin(sin($rLat) * cos($rAngDist) + cos($rLat) * sin($rAngDist) * cos($rAngle));
		$rLonB = $rLng + atan2(sin($rAngle) * sin($rAngDist) * cos($rLat), cos($rAngDist) - sin($rLat) * sin($rLatB));

		$return[$name] = array('lat' => (float) rad2deg($rLatB),
			'lng' => (float) rad2deg($rLonB));
	}

	return array('min_lat'  => $return['south']['lat'],
		'max_lat' => $return['north']['lat'],
		'min_lng' => $return['west']['lng'],
		'max_lng' => $return['east']['lng']);
}

 function recode($content)
    {
        if (!mb_detect_encoding($content, 'UTF-8', true)) {
            return utf8_encode(strip_tags($content));
        } else {
            return strip_tags($content);
        }
    }



?>
