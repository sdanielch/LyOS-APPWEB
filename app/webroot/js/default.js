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


// FUNCION REMOVE en arrays (el inverso de push)
function removeFromArray(array, item) {
	let changed = false;
	let j, i, len, elt;

	for (j = i = 0, len = array.length; i < len; ++i) {
		elt = array[i];
		if (elt === item) {
			changed = true;
		} else {
			array[j++] = elt;
		}
	}

	array.length = j;
	return changed;
}


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
	 * OPCIONES: ES UN ARRAY CON TODO LO DE ABAJO ASI NO HAY QUE RECORDAR EL ORDEN DE PARÁMETROS
	 * contenido: LINK DE DATOS
	 * tipo: fecth o iframe
	 * ancho: en px
	 * alto: en px
	 * pi: (permitir varias instancias) true o false
	 */


	//createwindow({op: 1, nombre: "Aplicacion"});
	//createwindow({op: 2});


});

var opened_windows = [];

function createwindow(opciones = {nombre: "App"}) {
	$("#olaucher").hide(300);

	$(".window").removeClass("active")
	// nombre, contenido, icono, tipo, ancho, alto, pi

	if (opciones.icono == undefined) {
		opciones.icono = window.location + "app/webroot/img/defaultapp.png";
	}
	if (opciones.nombre == undefined) {
		opciones.nombre = "App";
	}
	if (opciones.tipo == undefined) {
		opciones.tipo = "iframe";
	}
	if (opciones.ancho == undefined) {
		opciones.ancho = 500;
	}
	if (opciones.alto == undefined) {
		opciones.alto = 350;
	}
	if (opciones.pi == undefined) {
		opciones.pi = true;
	}
	if (opciones.contenido == undefined) {
		opciones.contenido = "about:blank";
	}

	var win_idn = Math.floor(Math.random() * 85000) + 1;
	var win_id = "win_" + win_idn;

	var found = false;
	var findex = -1;
	for (var i = 0; i < opened_windows.length && !found; i++) {
		if (opened_windows[i].includes(opciones.contenido)) {
			found = true;
			findex = i;
			break;
		}
	}

	if (opciones.pi == false && found == true) {
		// Si la ventana ya está abierta le damos el foco
		console.info("Window is already opened");
		//console.log(opened_windows[findex].split(" ")[2])
		// Conseguimos la ID de la ventana y la usamos para reactivar el foco
		var idn = opened_windows[findex].split(" ")[2];
		$(".task").removeClass("activetask");
		$(".task_" + idn).addClass("activetask");
		$(".window").removeClass("active");
		$('#win_' + idn).addClass("active");
		if ($('#win_' + idn).hasClass("wminimized")) {
			$('#win_' + idn).removeClass("wminimized");
		}

		setTimeout(function () {
			$(".window").removeClass("active");
			$('#win_' + idn).addClass("active");
		}, 100);
		// Devolvemos false para salir de la función y no reabrir otro proceso
		return false;
	}


	setTimeout(function () {
		$(".window").removeClass("active");
		$('#win_' + idn).addClass("active");
		//$(".task").removeClass("activetask");
		//$(".task_" + idn).addClass("activetask");
	}, 100);


	// Posición aleatoria de las ventanas (WM)
	function getRandom(min, max) {
		return Math.floor(Math.random() * (max - min)) + min;
	}

	var win_alto = $(window).height();
	var win_ancho = $(window).width();
	var app_alto = opciones.alto;
	var app_ancho = opciones.ancho;

	var pinki1 = win_alto - app_alto;
	var pinki2 = win_ancho - app_ancho;

	var app_top = getRandom(0, pinki1);
	var app_left = getRandom(0, pinki2);


	

	// Se crea la ventana
	$("body").append("<div class='window active' id='" + win_id + "' style='top: " + app_top + "px; left: " + app_left + "px;width: 100%; height: 100%; max-width: " + opciones.ancho + "px; max-height: " + opciones.alto + "px;'><div class='resizer'></div></div>");
	// Se crea la barra de títulos
	$('#' + win_id).append("<span class='wmtitle wmt_" + win_idn + "'> <img src='" + opciones.icono + "' style='margin-right:4px; width: 16px; height: 16px;' /> " + opciones.nombre + "</span> ");
	// Se crea el espacio los botones de minimizar, maximizar/restaurar y cerrar
	$('#' + win_id).append("<div class='wmbuttons wmb_" + win_idn + "'></div>");


	// BOTON DE MINIMIZAR
	$(".wmb_" + win_idn).append("<div class='mm_window_button wmbm_" + win_idn + "' style='margin-right: 6px;'> <i class=\"fas fa-window-minimize\"></i> </div>");

	// BOTON DE MAXIMIZAR
	$(".wmb_" + win_idn).append("<div class='mr_window_button wmbr_" + win_idn + "'> <i class=\"far fa-window-maximize\"></i> </div>");


	// BOTON DE CERRAR
	$(".wmb_" + win_idn).append("<div class='close_window_button wmbc_" + win_idn + "'> <i class=\"fas fa-times-circle\"></i> </div>");


	/*
	 * CONTENIDO DE LA VENTANA, AQUI SE CARGA SEGUN EL PARÁMETRO QUE LE HAYAMOS INDICADO
	 */

	 if (opciones.tipo == "iframe") {
	 	$('#' + win_id).append("<div class='wmcontain wmc_" + win_idn + "'><iframe src='" + opciones.contenido + "' allowfullscreen allowusermedia style='border: 0; width: 100%; height: 100%; display: block; background: transparent'> Su navegador no soporta el uso de este tipo de aplicación</iframe> </div>");
	 } else if(opciones.tipo == "jpg" || opciones.tipo == "jpeg" || opciones.tipo == "gif" || opciones.tipo == "png" || opciones.tipo == "bmp") {
	 	$('#' + win_id).append("<div class='wmcontain wmc_" + win_idn + "' style=' background: url(" + opciones.contenido + ") center center no-repeat; background-size: contain;'></div>");
	 } else if(opciones.tipo == "pdf" || opciones.tipo == "doc" || opciones.tipo == "docx" || opciones.tipo == "txt" || opciones.tipo == "xls" || opciones.tipo == "xls" || opciones.tipo == "odt") {
	 	$('#' + win_id).append("<div class='wmcontain wmc_" + win_idn + "'><iframe src='https://drive.google.com/viewerng/viewer?url=" + opciones.contenido + "?pid=explorer&efh=false&a=v&chrome=false&embedded=true' embedded=true allowfullscreen allowusermedia style='border: 0; width: 100%; height: 100%; display: block; background: transparent'> Su navegador no soporta el uso de este tipo de aplicación</iframe> </div>");
	 } else {
	 	$('#' + win_id).append("<div class='wmcontain wmc_" + win_idn + "' style='overflow: auto; padding: 10px;'>Cargando aplicación...</div>");
	 	$(".wmc_" + win_idn).load(opciones.contenido, function () {
	 		console.warn("Aplicación '" + opciones.nombre + "', cargada.")
	 	});
	 }


	// TASKBAR
	$("#taskmanager").append("<div class='task task_" + win_idn + "'> <img src='" + opciones.icono + "' style='margin-right:4px; width: 16px; height: 16px;' /><span> " + opciones.nombre + "</span></div>")
	$(".task").removeClass("activetask");
	$(".task_" + win_idn).addClass("activetask").on("click", function (e) {
		$(".task").removeClass("activetask");
		$(this).addClass("activetask");
		$(".window").removeClass("active");
		$('#' + win_id).addClass("active");
		if ($('#' + win_id).hasClass("wminimized")) {
			$('#' + win_id).removeClass("wminimized");
		}
	});


	// TEMAS DE BOTONES PARA MINIMIZAR Y DEMAS

	$(".wmbm_" + win_idn).on("click", function (e) {
		$('#' + win_id).addClass("wminimized").removeClass("active");
		setTimeout(function () {
			$(".task_" + win_idn).removeClass("activetask");
		}, 300);
	});

	$(".wmbr_" + win_idn).on("click", function (e) {
		setTimeout(function () {
			addResponsiveDesign();
		}, 300);

		if ($('#' + win_id).hasClass("maximized")) {
			$('#' + win_id).removeClass("maximized");
			$(this).html("<i class=\"far fa-window-maximize\"></i>");
		} else {

			$('#' + win_id).addClass("maximized");
			$(this).html("<i class=\"far fa-window-restore\"></i>");
		}
	});

	$(".wmt_" + win_idn).dblclick(function () {
		setTimeout(function () {
			addResponsiveDesign();
		}, 300);
		if ($('#' + win_id).hasClass("maximized")) {
			$('#' + win_id).removeClass("maximized");
			$(".wmbr_" + win_idn).html("<i class=\"far fa-window-maximize\"></i>");
		} else {
			$('#' + win_id).addClass("maximized");
			$(".wmbr_" + win_idn).html("<i class=\"far fa-window-restore\"></i>");

		}
	});

	$(".wmbc_" + win_idn).on("click", function (e) {
		$('#' + win_id).addClass("wmcierre");
		$(".task_" + win_idn).remove();


		opened_windows.forEach(function (elemento, indice) {
			if (elemento.includes(opciones.contenido)) {
				toremove = opened_windows[indice];
				removeFromArray(opened_windows, toremove);
			}

		});

		setTimeout(function () {
			$('#' + win_id).remove();
		}, 300);
	});

	/*
	 * Añadimos soporte para un diseño responsivo
	 */
	 function addResponsiveDesign() {
		// 640 * 320
		var ancho = $(".wmc_" + win_idn).width();
		var alto = $(".wmc_" + win_idn).height();

		if (ancho <= 640 || alto <= 320) {
			$(".wmc_"+win_idn).addClass("responsivedesign");
			$(".wmc_"+win_idn).children("*").addClass("responsivedesign");
			$(".wmc_"+win_idn).children("*").children("*").addClass("responsivedesign");
			//console.log(ancho, alto);
		} else {
			$(".wmc_"+win_idn).removeClass("responsivedesign");
			$(".wmc_"+win_idn).children("*").removeClass("responsivedesign");
			$(".wmc_"+win_idn).children("*").children("*").removeClass("responsivedesign");
		}

	}

	setTimeout(function () {
		addResponsiveDesign();
	}, 350);

	$(window).on("resize", function(e) {
		addResponsiveDesign();
	});
	//addResponsiveDesign();
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
			$(".task").removeClass("activetask");
			$(".task_"+win_idn).addClass("activetask");

			$('#'+win_id).removeClass("maximized");
			$(".wmbr_"+win_idn).html("<i class=\"far fa-window-maximize\"></i>");

		},
	}).on("click", function (e) {
		$(".window").removeClass("active");
		$('#'+win_id).addClass("active");
		$(".task").removeClass("activetask");
		$(".task_"+win_idn).addClass("activetask");
	}).resizable({
		onDrag: function (e, $el, newWidth, newHeight, opt) {
			// limit box size

			addResponsiveDesign();

			if (newWidth < 320)
				newWidth = 320;
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
	addResponsiveDesign();
},
onDragEnd: function (e, $el, opt) {
	$el.css("cursor", "");
	addResponsiveDesign();
},
handleSelector: "> .resizer"
});
	// Guardamos la URL en el array de ventanas abiertas (SI LA HUBIERA, NO REPETIMOS)
	if (!opened_windows.includes(opciones.contenido, 0)) {
		opened_windows.push(opciones.contenido + " :-: " + win_idn);
	}

	return win_id;
}

function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return 'n/a';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	if (i == 0) return bytes + ' ' + sizes[i];
	return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
};



javascript:(function(){var script=document.createElement('script');script.onload=function(){var stats=new Stats();document.body.appendChild(stats.dom);requestAnimationFrame(function loop(){stats.update();requestAnimationFrame(loop)});};script.src='//mrdoob.github.io/stats.js/build/stats.min.js';document.head.appendChild(script);})()
