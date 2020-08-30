<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		LineOS -
		<?php echo $this->fetch('title'); ?>
	</title>

	<link rel="manifest" href="<?php echo $base ?>app/webroot/manifest.webmanifest.json"/>

	<script src="<?php echo $script; ?>pace.min.js"></script>

	<!-- include PWACompat _after_ your manifest -->
	<script async src="<?php echo $fundamental_libs; ?>pwacompat.min.js"></script>

	<link rel="apple-touch-icon" sizes="57x57" href="<?php echo $base ?>app/webroot/apple-icon-57x57.png">
	<link rel="apple-touch-icon" sizes="60x60" href="<?php echo $base ?>app/webroot/apple-icon-60x60.png">
	<link rel="apple-touch-icon" sizes="72x72" href="<?php echo $base ?>app/webroot/apple-icon-72x72.png">
	<link rel="apple-touch-icon" sizes="76x76" href="<?php echo $base ?>app/webroot/apple-icon-76x76.png">
	<link rel="apple-touch-icon" sizes="114x114" href="<?php echo $base ?>app/webroot/apple-icon-114x114.png">
	<link rel="apple-touch-icon" sizes="120x120" href="<?php echo $base ?>app/webroot/apple-icon-120x120.png">
	<link rel="apple-touch-icon" sizes="144x144" href="<?php echo $base ?>app/webroot/apple-icon-144x144.png">
	<link rel="apple-touch-icon" sizes="152x152" href="<?php echo $base ?>app/webroot/apple-icon-152x152.png">
	<link rel="apple-touch-icon" sizes="180x180" href="<?php echo $base ?>app/webroot/apple-icon-180x180.png">
	<link rel="icon" type="image/png" sizes="192x192" href="<?php echo $base ?>app/webroot/android-icon-192x192.png">
	<link rel="icon" type="image/png" sizes="32x32" href="<?php echo $base ?>app/webroot/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="96x96" href="<?php echo $base ?>app/webroot/favicon-96x96.png">
	<link rel="icon" type="image/png" sizes="16x16" href="<?php echo $base ?>app/webroot/favicon-16x16.png">
	<meta name="msapplication-TileColor" content="#080808">
	<meta name="msapplication-TileImage" content="<?php echo $base ?>app/webroot/ms-icon-144x144.png">
	<meta name="theme-color" content="#080808">


	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
	<meta name="apple-mobile-web-app-title" content="LineOS"/>
	<meta name="title" content="<?php if (isset($titletag)) {
	echo $titletag;
} else {
echo "LineOS";
} ?>">
<meta name="description" content="<?php if (isset($descripciontag)) {
echo $descripciontag;
} else {
echo "Wherever you Are";
} ?>">
<meta name="keywords" content="webdesktop, web desktop, escritorio web, nuevo, 2020, trending, españa">
<meta name="robots" content="index, follow">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="language" content="Spanish">
<meta name="revisit-after" content="1 days">
<meta name="author" content="Sergio Daniel Calvo Hidalgo">
<meta name="theme-color" content="#040404"/>
<meta name="apple-mobile-web-app-status-bar-style" content="black"/>
<meta name="apple-mobile-web-app-capable" content="yes">
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

<script src="<?php echo $fundamental_libs; ?>jquery.3.5.1.min.js"></script>
<script src="<?php echo $fundamental_libs; ?>jquery.pep.js"></script>
<script src="<?php echo $fundamental_libs; ?>wm_resizable.js"></script>
<script src="<?php echo $fundamental_libs; ?>contextmenu.min.js"></script>
<script src="<?php echo $fundamental_libs; ?>jquery.easypiechart.min.js"></script>
<script src="<?php echo $fundamental_libs; ?>jquery.form.min.js"></script>
<script src="<?php echo $fundamental_libs; ?>jquery.touchSwipe.min.js"></script>
<script src="<?php echo $fundamental_libs; ?>popper.min.js"></script>
<link rel="stylesheet" href="<?php echo $fundamental_libs; ?>uikit-3.4.6/css/uikit.min.css"/>
<script src="<?php echo $fundamental_libs; ?>uikit-3.4.6/js/uikit.min.js"></script>
<script src="<?php echo $fundamental_libs; ?>uikit-3.4.6/js/uikit-icons.min.js"></script>
<link rel="stylesheet" href="<?php echo $fundamental_libs; ?>bootstrap-4.4.1-dist/css/bootstrap.min.css"/>
<script src="<?php echo $fundamental_libs; ?>bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
	<script src="<?php echo $fundamental_libs; ?>fontawesome-free-5.13.0-web/js/all.min.js"></script>
	<script src="<?php echo $fundamental_libs; ?>bootbox/bootbox.all.min.js"></script>
<link href="<?php echo $fundamental_libs; ?>animate.min.css" rel="stylesheet"
type="text/css">
<link rel="stylesheet" href="<?php echo $fundamental_libs; ?>uitable.css"/>
<link rel="stylesheet" href="<?php echo $fundamental_libs; ?>hint.min.css"/>

<?php
// echo $this->Html->meta('icon');jquery.comet
echo $this->Html->css('normalize');
echo $this->Html->css('openlove');
//echo $this->Html->script('prototype');
echo $this->Html->script('jquery.form.min');
echo $this->Html->script('jquery.wait');
echo $this->Html->script('default');
//echo $this->fetch('meta');
echo $this->fetch('css');
echo $this->fetch('script');

//var_dump($fundamental_libs); exit;
?>
<style>
	.modal-open .modal {
		overflow-x: hidden;
		overflow-y: auto;
		backdrop-filter: blur(18px);
	}

	@font-face {
		font-family: "Karla";
		src: url("<?php echo $fundamental_libs;?>Karla-Regular.eot"); /* IE9 Compat Modes */
		src: url("<?php echo $fundamental_libs;?>Karla-Regular.eot?#iefix") format("embedded-opentype"), /* IE6-IE8 */ url("<?php echo $fundamental_libs;?>Karla-Regular.woff2") format("woff2"), /* Modern Browsers */ url("<?php echo $fundamental_libs;?>Karla-Regular.otf") format("opentype"), /* Open Type Font */ url("<?php echo $fundamental_libs;?>Karla-Regular.svg") format("svg"), /* Legacy iOS */ url("<?php echo $fundamental_libs;?>Karla-Regular.ttf") format("truetype"), /* Safari, Android, iOS */ url("<?php echo $fundamental_libs;?>Karla-Regular.woff") format("woff"); /* Modern Browsers */
		font-weight: 400;
		font-style: normal;
	}

	@font-face {
		font-family: "Pacifico";
		src: url("<?php echo $fundamental_libs;?>Pacifico-Regular.eot"); /* IE9 Compat Modes */
		src: url("<?php echo $fundamental_libs;?>Pacifico-Regular.eot?#iefix") format("embedded-opentype"), /* IE6-IE8 */ url("<?php echo $fundamental_libs;?>Pacifico-Regular.woff2") format("woff2"), /* Modern Browsers */ url("<?php echo $fundamental_libs;?>Pacifico-Regular.otf") format("opentype"), /* Open Type Font */ url("<?php echo $fundamental_libs;?>Pacifico-Regular.svg") format("svg"), /* Legacy iOS */ url("<?php echo $fundamental_libs;?>Pacifico-Regular.ttf") format("truetype"), /* Safari, Android, iOS */ url("<?php echo $fundamental_libs;?>Pacifico-Regular.woff") format("woff"); /* Modern Browsers */
		font-weight: 400;
		font-style: normal;
	}
</style>


<script>
		//jQuery.noConflict();
	</script>

	<style type="text/css">
		.pace {
			-webkit-pointer-events: none;
			pointer-events: none;
			position: relative;
			-webkit-user-select: none;
			-moz-user-select: none;
			user-select: none;
			z-index: 4500;
		}

		.pace-inactive {
			display: none;
		}

		.pace .pace-progress {
			background: #961688;
			position: fixed;
			z-index: 4600;
			top: 0;
			right: 100%;
			width: 100%;
			height: 2px;
		}


		.bg-dark {
			background-color: rgba(10, 10, 10, 0.8) !important;
			backdrop-filter: blur(20px) saturate(180%);
		}

		b {
			font-weight: bold;
		}

	</style>
	<?php if(isset($usuario)) {
	echo '<link type="text/css" href="' . $fundamental_libs . $estilo . '.css" rel="Stylesheet" id="loadestilo">';
}?>


</head>

<body>
	<div id="context-menu" style="display:none;">
		Cerrar ventana
	</div>

	<script>

	// Instalación del serviceWorker
	// LA INSTALACION DEL SERVICE WORKER QUEDA SUSPENDIDA EN MODO DESARROLLO
	/*if ('serviceWorker' in navigator) {
		window.addEventListener('load', function () {
			navigator.serviceWorker.register('<?php echo $base ?>sw.js', {scope: '<?php echo $base ?>'}).then(function (registration) {
				// Registration was successful
				console.info('ServiceWorker iniciado.');
				// NOTIFICACIONES PUSH
				//initialiseUI();
			}, function (err) {
				// registration failed :(
				console.log('Ha fallado la instalación ServiceWorker : ', err);
			});
		});
	}*/

	$(function () {

	});
</script>
<style>
	#background {
		background: #282828 url('<?php echo $wallpaper;?>') center center no-repeat;
		position: fixed;
		top: 0; left: 0; right: 0; bottom: 0;
		z-index: 1;
		background-size: cover;
	}
</style>
<div id="background">

</div>
<!-- aqui deberia haber un menu -->


<?php echo $this->fetch('content'); ?>
<div style="position: relative; display: block;" id="flash">
	<?php echo $this->Flash->render(); ?>
</div>

<!-- aqui tendriamos el footer que vendria siendo la barra de tareas -->
<div id="olaucher" class="ncm">
	<div class="mprogramas ncm">

	</div>
	<div class="mopciones nmc">
		<img src="<?php echo $img;?>logo.png" style="width: 96px; border-radius: 500px;">
		<hr style="background: rgba(255,255,255,0.2);">
		<?php if($usuario == null) {
		?>
		<button type="button" onclick="createwindow({nombre: 'Iniciar sesión', tipo: 'ajax', pi: false, contenido: '<?php echo $base;?>users/login', ancho: 800, alto: 450})">Iniciar sesión</button>
		<?php } else { ?>



		<button type="button" onclick="createwindow({nombre: 'Documentos', tipo: 'ajax', pi: false, contenido: '<?php echo $base;?>xapps/browser', ancho: 800, alto: 450, icono: '<?php echo $img;?>controlpanel.png'})">Documentos</button>



		<button type="button" onclick="createwindow({nombre: 'Panel de control', tipo: 'ajax', pi: false, contenido: '<?php echo $base;?>xapps/controlpanel', ancho: 800, alto: 450, icono: '<?php echo $img;?>controlpanel.png'})">Panel de control</button>
			<button type="button" onclick="createwindow({nombre: 'LineTube', tipo: 'ajax', pi: false, contenido: '<?php echo $base;?>xapps/fyoutube', ancho: 800, alto: 450, icono: '<?php echo $img;?>youtube.png'})">LineTube</button>

			<button type="button" onclick="createwindow({nombre: 'Calendario', tipo: 'ajax', pi: false, contenido: '<?php echo $base;?>xapps/calendario', ancho: 800, alto: 450, icono: '<?php echo $img;?>youtube.png'})">Calendario</button>





			<a href="<?php echo $base;?>users/logout">Cerrar sesión</a>
		<?php } ?>
	</div>
</div>
<footer style="color: #FFF; position: fixed; bottom: 0; left: 0; right: 0; background: transparent url('<?php echo $img; ?>bottombar.png'); height: 30px; z-index: 1000; backdrop-filter: blur(8px) saturate(180%);">

	<div id="launcher" class="ncm">
		<i class="fas fa-rocket ncm"></i>
	</div>


	<!-- TODO: Aqui vendria el gestor de tareas -->
	<div id="taskmanager">

	</div>
	<!-- Aqui vendria el gestor de tareas -->


	<div id="trayicons">
		<i class="fas fa-memory" id="memo" style="margin-right: 8px; cursor: pointer;"></i>
		<div id="clock"></div>


	</div>

</footer>


</body>
</html>
