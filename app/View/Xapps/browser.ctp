<div class="barfiles">

<span onclick='javascript:winit()'>
	<i class="fas fa-home"></i>

</span>

	<span onclick='javascript:wfup()' style="display: none" class="wfup">
	<i class="fas fa-arrow-up"></i>
</span>

	<span  class="wfupw">
	<i class="fas fa-upload"></i>
</span>

	<span onclick='javascript:wfnf()' class="wfnf">
	<i class="fas fa-folder-plus"></i>
</span>


</div>
<div id="browser" style="position: relative; display: block; height: calc(100% - 36px); overflow: auto;">

</div>

<script type="text/javascript">

	function winit() {
		historial = ["/"];
		ira("/");
	}

	function wfup() {
		historial.pop();
		h2 = historial.join("/");
		ira(h2);

	}

	function wfnf() {
		/*h2 = historial.join("/");
		ira(h2);

		$.post( '<?php echo $base;?>xapps/newfolder', { actual: h2, nuevo: "directorio" })
				.done(function( data ) {
					console.warn(data);
					ira(h2);
				});*/

		var locale = {
			OK: 'Ok',
			CONFIRM: 'Crear directorio',
			CANCEL: 'Cancelar'
		};

		bootbox.addLocale('custom', locale);

		bootbox.prompt({
			title: "¿Nombre del directorio?",
			locale: 'custom',
			centerVertical: true,
			callback: function (result) {
				if (result == null || result == "" || result == " " || result == undefined || $.trim( $(".bootbox-input-text").val() ) == '') {
					// no se hace nada
				} else {
					h2 = historial.join("/");
					ira(h2);

					$.post( '<?php echo $base;?>xapps/newfolder', { actual: h2, nuevo: result })
							.done(function( data ) {
								console.warn(data);
								ira(h2);
					});
				}
			}
		});

	}

function getFileExtension(filename) {
    return filename.split('.').pop();

}


	var historial = ["/"];
	function ira(url) {


		if(historial.length == 1) {
			$(".wfup").hide();
		} else {
			$(".wfup").show();
		}




console.warn(url)
		$("#browser").html("Cargando datos...");
		$.post( "<?php echo $base;?>xapps/browser", { directorio: url })
		.done(function( data ) {

			$(".wfupw").on("click", function (e) {
				$("#slct").click();
			});

			$("#browser").html(data);
			h2 = historial.join("/");
			$("#browser").append("<span style='position: absolute; bottom: 0px; right: 4px;'>"+h2+"</span>")


			$( ".files" ).each(function( index ) {

				console.log("URL actual: " + url);

				if($(this).hasClass("isfolder")) {
					console.log(index + ": directorio - " + $(this).attr("data-name"));

					$(this).on("click", function(e) {
						historial.push($(this).attr("data-name"));
						ira(url + "/" + $(this).attr("data-name"));


						console.warn(historial)
					});

				} else {

					h2 = historial.join("/");
					console.log(index + ": fichero - " + $(this).attr("data-name"));

					$(this).on("click", function(e) {
						//ira(url + $(this).attr("data-name"));
						console.log("Creando ventana para la extension:" + getFileExtension($(this).attr("data-name")))
						console.log(index + ": fichero - " + $(this).attr("data-imagen"));
						console.log(index + ": fichero - " + $(this).data("imagen"));
						createwindow({
							nombre: $(this).attr("data-name"),
							tipo: ""+getFileExtension($(this).attr("data-name"))+"",
							pi: false,
							icono:  $(this).attr("data-imagen"),
							contenido: "<?php echo $base;?>app/webroot/users/<?php echo $usuario;?>" + h2 + "/" + $(this).attr("data-name")

						});
					});
				}


			});


		});
	}


	// Llamamos al directorio de usuario "/" como entrada principal
	ira("/");


</script>
