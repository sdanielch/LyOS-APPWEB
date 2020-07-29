

<div class="pagina uk-grid-collapse" uk-grid style="position: relative;z-index: 2; padding-top: 35px; padding-bottom: 35px; width: 98%; max-width:800px; margin: auto;  text-align: center; padding: 10px; overflow: hidden;  color: #282828;">

	<div class="uk-width-1-1@s" style="font-size: 72px; color: rgba(0,0,0,0.8); margin-bottom: 10px; text-shadow: 2px 2px 26px #FFF;">
	<span style="font-size: 22px; font-weight: bold">Iniciar sesión en <i style="font-weight: normal; text-decoration: none;	line-height: 50px; font-family: 'Pacifico', cursive; font-size: 24px; display: inline-block; text-align: center; color: #181818;padding-left: 10px; padding-right: 20px;"> LineOS</i></span>
		<hr>

	</div>

	<div class="uk-width-1-2@s" style="padding: 5px;">
		<form action="<?php echo $base ?>login" id="UserLoginForm" method="post" accept-charset="utf-8"
			  class="uk-form-horizontal">
			<div style="display:none;"><input type="hidden" name="_method" value="POST"/></div>

			<input class="uk-input" name="data[User][username]" maxlength="50" type="email"
				   placeholder="<?php _t("6");?>" id="UserUsername"
				   required="required" autocomplete="off" style="border-radius: 4px; margin-bottom: 5px; background: rgba(255,255,255,0.7)">

			<input class="uk-input" name="data[User][password]" type="password"
				   placeholder="<?php _t("7");?>" id="UserPassword"
				   required="required"
				   autocomplete="off" style="border-radius: 4px; margin-bottom: 15px; background: rgba(255,255,255,0.7)">
			<input type="checkbox" class="uk-checkbox" onclick="myFunction()"> <span style="color: #222;">Mostrar contraseña</span>

			<br /><br />

			<input class="uk-button uk-button-primary uk-width-1" type="submit" style="border-radius: 4px;
    margin-bottom: 10px;
    background: #0b740d;
    color: #FFF;" value="<?php _t("10");?>"/>

		</form>
	</div>
	<div class="uk-width-1-2@s" style="padding: 5px;">
		<a href="<?php echo $base ?>add" class="uk-button uk-button-primary uk-width-1 hint--large hint--bottom hint--bounce hint--rounded" style="border-radius: 6px; background: #0b740d; margin-top: 5px; text-align: left; position: relative;" aria-label="Este modo ofrece una forma manual para registrarte en nuestra plataforma, requiere de confirmación desde el correo electrónico">Registrar cuenta manual</a>



		<span style="color: #222; display: block; margin-top: 15px; margin-bottom: 5px;">También puedes iniciar sesión directamente usando tus redes sociales:</span>

		<?php
		$fb = new \Facebook\Facebook([
				'app_id' => '272154067000398',
				'app_secret' => 'f0ecc118867abacf05acb5c348ccfe04',
				'default_graph_version' => 'v2.10',
				'persistent_data_handler'=>'session'
			//'default_access_token' => '{access-token}', // optional
		]);

		$helper = $fb->getRedirectLoginHelper();
		$permissions = ['email']; // Generar permisos opcionales
		$loginUrl = $helper->getLoginUrl('https://www.openlove.me/users/facebookcb', $permissions);

		/* Aquí el enlace a la página de login Facebook*/
		echo '<a href="'. htmlspecialchars($loginUrl) . '" class="uk-button uk-button-primary uk-width-1" style="border-radius: 6px; text-align: left; position: relative;"><span>Facebook </span><i class="fab fa-facebook" style="position: absolute; top: 6px; right: 8px; font-size: 24px;"></i></a>';


		/* Google App Client Id */
		define('CLIENT_ID', '795399308297-n0d6r80euh73eafneapu39egtlrdi869.apps.googleusercontent.com');

		/* Google App Client Secret */
		define('CLIENT_SECRET', '2NPDMo_-2m_qcDSKmdAq6Re2');

		/* Google App Redirect Url */
		define('CLIENT_REDIRECT_URL', 'https://www.openlove.me/users/authgoogle');


		?>

		<a id="login-button" class="uk-button uk-button-primary uk-width-1" style="border-radius: 6px; background: #bd1d1d; margin-top: 5px; text-align: left; position: relative;" href="<?= 'https://accounts.google.com/o/oauth2/auth?scope=' . urlencode('https://www.googleapis.com/auth/userinfo.profile https://www.googleapis.com/auth/userinfo.email') . '&redirect_uri=' . urlencode(CLIENT_REDIRECT_URL) . '&response_type=code&client_id=' . CLIENT_ID . '&access_type=online' ?>"><span>Google </span> <i class="fab fa-google" style="position: absolute; top: 6px; right: 8px; font-size: 24px;"></i></a>


	</div>
	<div class="uk-width-1-1@s" style="padding: 5px;">
		<small id="emailHelp" class="form-text text-muted">Nunca compartas ni reveles tu contraseña a nadie.</small>
	</div>
</div>
