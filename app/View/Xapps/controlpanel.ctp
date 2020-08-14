<div class="opaneloptions">
<div class="menuit">Menu de opciones</div>
	<hr>
	<div class="menuitems">
		<a href="#" attr-section="home"><i class="fas fa-home"></i> <span>Inicio</span></a>
		<a href="#" attr-section="apariencia"><i class="fas fa-palette"></i> <span>Apariencia</span></a>
		<a href="#"><i class="fas fa-chart-pie"></i> <span>Recursos</span></a>
		<a href="#"><i class="fas fa-user-circle"></i> <span>Cuenta</span></a>
	</div>
</div>
<div class="opanelresults">

	<div id="home">
		Hola <?php echo $username;?>, este es el panel de control de LineOS, desde aquí podrás configurar tu cuenta y personalizar algunos aspectos de la interfaz. <hr> Resumen de tu cuenta:<br />

<script>
	$(function() {

		$(".opanelresults > div").hide();
		$(".menuitems a").on("click", function (e) {
			//console.log($(this).attr("attr-section"));
			var seccion = "#" + $(this).attr("attr-section");
			$(".opanelresults > div").hide();
			$(seccion).show(300);
		});

		$(".theme").on("click", function (e) {
			$("#loadestilo").remove();
			$("head").append('<link type="text/css" href="<?php echo $fundamental_libs;?>'+ $(this).val() +'.css" rel="Stylesheet" id="loadestilo">')
		});



		$('.chart').easyPieChart({
			barColor: '#ef1e25',
			trackColor: '#444444',
			scaleColor: 'transparent',
			scaleLength: 5,
			lineCap: 'round',
			lineWidth: 5,
			trackWidth: undefined,
			size: 110,
			rotate: 0, // in degrees
			animate: {
				duration: 1000,
				enabled: true
			},
			easing: function (x, t, b, c, d) { // easing function
				t = t / (d/2);
				if (t < 1) {
					return c / 2 * t * t + b;
				}
				return -c/2 * ((--t)*(t-2) - 1) + b;
			},
			onStep: function(from, to, percent) {
				$(this.el).find('.percent').text(Math.round(percent));
			}
		});



			// SUBIDA DE FICHEROS


		$('#submitperso').click(function () {
			$('#uploadForm').ajaxForm({
				target: '#outputImage',
				url: '<?php echo $base;?>xapps/wallchange',
				beforeSubmit: function () {
					$("#outputImage").hide();
					/*if($("#img").val() == "") {
						$("#outputImage").show();
						$("#outputImage").html("<div class='error'>Sin .</div>");
						return false;
					}*/
					$("#progressDivId").css("display", "block");
					var percentValue = '0%';

					$('#progressBar').width(percentValue);
					$('#percent').html(percentValue);
				},
				uploadProgress: function (event, position, total, percentComplete) {

					var percentValue = percentComplete + '%';
					$("#progressBar").animate({
						width: '' + percentValue + ''
					}, {
						duration: 100,
						easing: "linear",
						step: function (x) {
							percentText = Math.round(x * 100 / percentComplete);
							$("#percent").text(percentText + "%");
							if(percentText == "100") {
								$("#progressDivId").wait(1000).hide(300)
								$("#outputImage").show();
							}
						}
					});
				},
				error: function (response, status, e) {
					alert('Oops something went.');
				},

				complete: function (xhr) {
					if (xhr.responseText && xhr.responseText != "error")
					{
						$("#outputImage").html(xhr.responseText);
					}
					else{
						$("#outputImage").show();
						$("#outputImage").html("<div class='error'>Ha ocurrido un error al guardar los datos.</div>");
						$("#progressBar").stop();
					}
				}
			});
		});















	});











</script>
<div class="charting">
<span class="chart" data-percent="86">
  <span class="percent"></span>
</span>
	<span style="font-size: 12px;">Espacio utilizado en tu cuenta</span>
</div>


		<!-- final de home -->
	</div>

	<div id="apariencia">
		Personalización y apariencia
		<hr>
		<form id="uploadForm" name="frmupload" action="<?php echo $base;?>xapps/wallchange" method="post" enctype="multipart/form-data">
			Tema de color:
			<div class="form-check" style="display: block; margin-bottom: 10px">
				<div style="display: inline-block">
				<input class="form-check-input theme" type="radio" name="exampleRadios" id="exampleRadios1" value="light" <?php if($estilo == "light") {echo "checked";};?>>
				<label class="form-check-label" for="exampleRadios1">
					<i class="fas fa-sun"></i> Claro
				</label>
				</div>
				<div style="display: inline-block; margin-left: 40px;">
				<input class="form-check-input theme" type="radio" name="exampleRadios" id="exampleRadios2" value="dark" <?php if($estilo == "dark") {echo "checked";};?>>
				<label class="form-check-label" for="exampleRadios2">
					<i class="fas fa-moon"></i> Oscuro
				</label>
				</div>
			</div>
			Fondo de pantalla<br />
			<label for="img">Subir imagen de fondo:</label><br />
			<input type="file" id="img" name="img" accept="image/*" />
			<br><br>


			<?php

				if (count($wallpapers) > 0) {
					echo "O escoje un fondo de tu galeria de wallpapers<br />";
					foreach ($wallpapers AS $kk => $fondo) {

						echo "
				<div style='display: inline-block; max-width: 160px; margin: 4px; padding: 10px; '>
				<input class='form-check-input' type='radio' id='CC".$kk."' name='wallpaper' value='".$fondo['nombre']."'>
				<label class='form-check-label' for='CC".$kk."'>
					<img src='".$fondo['url']."' style='border-radius: 8px;' />
				</label>
				</div>

						";
					}
				} else {
					echo "No hay elementos en su galería de Wallpapers";
				}


			?>


			<br /><br />
				<input type="submit"  id="submitperso" class="btn btn-primary" value="Guardar datos">
		</form>
		<div class='progress' id="progressDivId">
			<div class='progress-bar' id='progressBar'></div>
			<div class='percent' id='percent'>0%</div>
		</div>
		<div style="height: 10px;"></div>
		<div id='outputImage'></div>


	</div>
<!-- final de lateral derecho -->
</div>

<style>




	.progress {
		display: none;
		position: relative;
		margin: 20px;
		width: calc(100% - 180px);
		background-color: #ddd;
		border: 1px solid blue;
		padding: 1px;
		left: 15px;
		border-radius: 5px;
	}

	.progress-bar {
		background-color: green;
		width: 0%;
		height: 30px;
		border-radius: 4px;
		-webkit-border-radius: 4px;
		-moz-border-radius: 4px;
	}

	.progressPcent {
		-webkit-border-radius: 4px;
		color: #fff;
		display: inline-block;
		font-weight: bold;
		left: 50%;
		margin-left: -20px;
		margin-top: -9px;
		position: absolute;
		top: 50%;
	}

	#previewImg {
		display: none;
	}

	.error {
		background: #ffb3b3;
		border-radius: 5px;
		border: #f1a8a8 1px solid;
		box-sizing: border-box;
		color: #ad7b7b;
		margin: 15px;
		padding: 15px;
	}

	input#mediaUpload {
		border: #3d3d3d 1px solid;
		padding: 6px;
		border-radius: 5px;
	}

	#previewImg img {
		max-width: 300px;
	}



	.error {

	}



	.charting {
		display: inline-block;
		text-align: center;
		width: 120px;
		padding: 5px;
		border: 1px #CCC solid;
		border-radius: 4px;
		background: #FFF;
	}
	.menuit {
		display: block;
		text-align: center;

	}
	.menuit.responsivedesign {
		display: none;
	}
	.opanelresults > div {
		display: block;
	}
	.menuitems a {
		display: block;
		padding: 4px;
		border: 1px rgba(255,255,255,0.4) solid;
		background: rgba(255,255,255,0.1);
		border-radius: 4px;
		margin-bottom: 4px;
		text-shadow: 1px 1px 2px #000;
	}
	.menuitems a:link, .menuitems a:visited {
		color: #EFEFEF;
	}
	.menuitems a:hover, .menuitems a:active {
		color: #FFF;
		text-decoration: none;
	}
	.menuitems.responsivedesign a > span {
		display: none;
	}
	.menuitems.responsivedesign a {
		text-align: center;
	}
	.opaneloptions {
		position: absolute;
		top: 0;
		left: 0;
		width: 220px;
		min-height: 100vh;
		background: rgba(11, 78, 150, 0.5);
		padding: 10px;
		transition: 300ms;
	}
	.opanelresults {
		position: absolute;
		top: 0;
		left: 230px;
		right: 0;
		bottom: 0;
		padding: 10px;
		transition: 300ms;
	}
	.opaneloptions.responsivedesign  {
		width: 50px;
	}
	.opanelresults.responsivedesign {
		left: 50px;

	}
	.chart {
		position: relative;
		display: inline-block;
		width: 110px;
		height: 110px;
		margin-top: 0px;
		margin-bottom: 0px;
		text-align: center;
	}
	.chart canvas {
		position: absolute;
		top: 0;
		left: 0;
	}
	.percent {
		display: inline-block;
		line-height: 110px;
		z-index: 2;
	}
	.percent:after {
		content: '%';
		margin-left: 0.1em;
		font-size: .8em;
	}

	.opanelresults > div {
		display: none;
	}
</style>
