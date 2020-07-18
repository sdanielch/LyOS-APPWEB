<?php

$usuario = $_SESSION['Auth']['User']['id'];
		if (file_exists(dirname(__FILE__).'/notificaciones'.$usuario.'.json')) {
			$filename = dirname(__FILE__).'/notificaciones'.$usuario.'.json';
		}else {
			$myfile = fopen(dirname(__FILE__).'/notificaciones'.$usuario.'.json', "w") or die("Error al escribir en disco");
			$txt = "";
			fwrite($myfile, $txt);
			fclose($myfile);
			$filename = dirname(__FILE__).'/notificaciones'.$usuario.'.json';
		}


		// Bucle infinito hasta que el archivo de datos no se modifica

$lastmodif    = isset($_GET['timestamp']) ? $_GET['timestamp'] : 0;//si tiene algun dato se asigna a $lastmodif, si no se iguala a 0
$currentmodif = filemtime($filename);//extrae la ultima fecha de modificacion


while ($currentmodif <= $lastmodif) // comprobar si el archivo de datos se ha modificado
{
  usleep(10000); // dormir 10 ms para descargar la CPU
  clearstatcache();
  $currentmodif = filemtime($filename);//si la fecha/hora de modificacion es diferente a la del archivo, se rompe el ciclo y avanza despues del while
}

// return json array
$response = array();
$response['msg']       = file_get_contents($filename);
$response['timestamp'] = $currentmodif;
//$response['usesion'] = $_SESSION['Auth']['User']['id'];



echo json_encode($response);
flush();//Vaciar el búfer de salida del sistema

?>
