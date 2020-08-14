<div class="barfiles">
	
<span onclick='javascript:ira("/"); historial = "/"'>
	<i class="fas fa-home"></i>

</span>


</div>
<div id="browser" style="position: relative; display: block; height: calc(100% - 36px); overflow: auto;">
	
</div>

<script type="text/javascript">
	
function getFileExtension(filename) {
    return filename.split('.').pop();

}


	var historial = "";
	function ira(url) {
		$("#browser").html("Cargando datos...");
		$.post( "<?php echo $base;?>xapps/browser", { directorio: url })
		.done(function( data ) {
			$("#browser").html(data);

			
			$( ".files" ).each(function( index ) {
				
				console.log("URL actual: " + url);

				if($(this).hasClass("isfolder")) {
					console.log(index + ": directorio - " + $(this).attr("data-name"));

					$(this).on("click", function(e) {
						ira(url + $(this).attr("data-name"));
						historial = "/" + $(this).attr("data-name");
					});

				} else {
					console.log(index + ": fichero - " + $(this).attr("data-name"));
					$(this).on("click", function(e) {
						//ira(url + $(this).attr("data-name"));
						console.log("Creando ventana para la extension:" + getFileExtension($(this).attr("data-name")))
						createwindow({
							nombre: $(this).attr("data-name"),
							tipo: ""+getFileExtension($(this).attr("data-name"))+"",
							pi: false,
							contenido: "<?php echo $base;?>app/webroot/users/<?php echo $usuario;?>" + historial + "/" + $(this).attr("data-name")
						});
					});
				}


			});


		});	
	}


	// Llamamos al directorio de usuario "/" como entrada principal
	ira("/");
	

</script>