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
		console.log($(e.target).hasClass('ncm'))
		console.warn($(e.target).parent().parent().hasClass('ncm'));
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


});

