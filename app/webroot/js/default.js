/*
Notificaciones integradas con el navegador (notificaciones push) en HTML5
Estas notificaciones no son lanzadas desde el servidor, se lanzan desde el mismo cliente web
Si la web se cierra las notificaciones NO serán lanzadas.
*/

/*
navigator.geolocation.getCurrentPosition(mostrarUbicacion, manejoDelError, { timeout: 5000, enableHighAccuracy: true, maximumAge: 0 });

function mostrarUbicacion (ubicacion) {
	var lng = ubicacion.coords.longitude;
	var lat = ubicacion.coords.latitude;
	console.log('Longitud: ' + lng + " / Latitud: " + lat);
}

function  manejoDelError (error) {
	switch (error.code) {
		case 3:
			UIkit.modal.alert('Agotado el tiempo de espera para establecer tus coordenadas GPS, comprueba la cobertura de tu telefono o si este dispone de un sensor GPS');
			break;
		case 2:
			UIkit.modal.alert('Error, tu dispositivo tiene un problema con el sensor GPS, Openlove.ME no puede asegurarte un buen funcionamiento en plataforma sin GPS');
			break;
		case 1:
			UIkit.modal.alert('Se han negado los permisos para la geolocalización, para un buen funcionamiento necesitamos acceso a tu posición.');
	}
}

*/


function myFunction() {
	var x = document.getElementById("UserPassword");
	if (x.type === "password") {
		x.type = "text";
	} else {
		x.type = "password";
	}
}


function notpush(titulo, texto, icono = "http://xitrus.es/imgs/logo_claro.png") {
	if (Notification) {
		if (Notification.permission !== "granted") {
			// Usariamos esto si queremos las notificaciones de forma intrusiva
			// Notification.requestPermission()
		}
		var title = titulo;
		var extra = {
			icon: icono,
			body: texto,
			vibrate: [500, 110, 500, 110, 450, 110, 200, 110, 170, 40, 450, 110, 200, 110, 170, 40, 500]
		}
		var noti = new Notification(title, extra)
		noti.onclick = {
			// Al hacer click
		}
		noti.onclose = {
			// Al cerrar
		}
		setTimeout(function () {
			noti.close()
		}, 10000)
	}
}


jQuery(document).ready(function ($) {


	$("#lmenu a[href='" + location.href + "']").addClass("eactivo");


	if (navigator.userAgent.match(/Android/i)
		|| navigator.userAgent.match(/webOS/i)
		|| navigator.userAgent.match(/iPhone/i)
		|| navigator.userAgent.match(/iPad/i)
		|| navigator.userAgent.match(/iPod/i)
		|| navigator.userAgent.match(/BlackBerry/i)
		|| navigator.userAgent.match(/Windows Phone/i)) {
		// Mobile
		// alert('you are using a mobile device')
	} else {


	}

	/*
	 * Reloj digital
	 */
	function startTime() {
		var today = new Date();
		var hr = today.getHours();
		var min = today.getMinutes();
		var sec = today.getSeconds();
		//Add a zero in front of numbers<10
		min = checkTime(min);
		sec = checkTime(sec);
		//document.getElementById("clock").innerHTML = hr + " : " + min + " : " + sec;
		$("#clock").html(hr + " : " + min);
		var time = setTimeout(function () {
			startTime()
		}, 500);
	}

	function checkTime(i) {
		if (i < 10) {
			i = "0" + i;
		}
		return i;
	}

	startTime();

	$("#launcher").on("click", function (e) {


		setTimeout(function () {
			$("#olaucher").toggle();
		}, 100);


	});
	$(document).on("click", function (e) {
		/*$("#olaucher").hide(300);*/
		//console.log($(e.target).hasClass('ncm'))
		//console.warn($(e.target).parent().parent().hasClass('ncm'));
		//console.log($(e.target).parent().parent().attr('id'))
		if ($(e.target).hasClass('ncm') == true ||
			$(e.target).parent().hasClass('ncm') == true ||
			$(e.target).parent().parent().hasClass('ncm') == true) {
			//No hacemos nada
		} else {
			$("#olaucher").hide(300);
		}
	});


	//$('[data-toggle="tooltip"]').tooltip();


	console.log("Proyecto iniciado correctamente.");



	/*
	 * contenido: LINK DE DATOS
	 * tipo: fecth o iframe
	 * ancho: en px
	 * alto: en px
	 * pi: (permitir varias instancias) true o false
	 */




	function createwindow(contenido, tipo, ancho, alto, pi) {

		if (tipo == undefined) {
			var tipo = "iframe";
		}
		if (ancho == undefined) {
			var ancho = 500;
		}
		if (alto == undefined) {
			var alto = 350;
		}
		if (pi == undefined) {
			var pi = true;
		}

		// Posición aleatoria de las ventanas (WM)
		var app_alto = $(window).height();
		var app_ancho = $(window).width();



		$(".window").removeClass("active")
		var win_idn = Math.floor(Math.random() * 85000) + 1;
		var win_id = "win_" + win_idn;
		// Se crea la ventana
		$("body").append("<div class='window active' id='"+win_id+"' style='width: 100%; height: 100%; max-width: "+ancho+"px; max-height: "+alto+"px;'><div class='resizer'></div></div>");
		// Se crea la barra de títulos
		$('#'+win_id).append("<span class='wmtitle wmt_"+win_idn+"'>Haciendo test</span> ");
		// Se crea el espacio los botones de minimizar, maximizar/restaurar y cerrar
		$('#'+win_id).append("<div class='wmbuttons wmb_"+win_idn+"'></div>");
		// BOTON DE CERRAR
		$(".wmb_"+win_idn).append("<div class='close_window_button wmbc_"+win_idn+"'> <i class=\"fas fa-times-circle\"></i> </div>");
		$('#'+win_id).append("<div class='wmcontain wmc_"+win_idn+"'>Haciendo test<p>Haciendo test</p></div>");


		// TEMAS DE BOTONES PARA MINIMIZAR Y DEMAS
		$(".wmbc_"+win_idn).on("click", function (e) {
			$('#'+win_id).addClass("wmcierre");
			setTimeout(function(){ $('#'+win_id).remove(); }, 300);
		});


		// TEMAS DE MOVIMIENTO
		$('#'+win_id).pep({
			cssEaseDuration:300,
			constrainTo: 'window',
			shouldEase: false,
			debug: false,
			allowDragEventPropagation: false,
			elementsWithInteraction: "div",
			disableSelect: false,
			useCSSTranslation: false,
			start: function(){
				$(".window").removeClass("active");
				$('#'+win_id).addClass("active");
			},
		}).on("click", function (e) {
			$(".window").removeClass("active");
			$('#'+win_id).addClass("active");
		}).resizable({
			onDrag: function (e, $el, newWidth, newHeight, opt) {
				// limit box size
				if (newWidth < 300)
					newWidth = 300;
				$($el).css({"max-width": newWidth, "max-height": newHeight})
				if (newHeight < 200)
					newHeight = 200;

				$el.width(newWidth);
				$el.height(newHeight);

				// explicitly return **false** if you don't want
				// auto-height computation to occur
				return false;
			},
			onDragStart: function (e, $el, opt) {
				$el.css("cursor", "nwse-resize");
			},
			onDragEnd: function (e, $el, opt) {
				$el.css("cursor", "");
			},
			handleSelector: "> .resizer"
		});


		return win_id;
	}
	createwindow();
	createwindow();










});

