<?php
$conf_dir = "common/include/conf";
if($GLOBALS["general_settings"]["login"]["allow_user_registration"] == "true") {
	?>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<script type="text/javascript" src="common/js/include/common.js"></script>

	<?php
	if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
		$key = explode("::::", urldecode(base64_decode($_GET["q"])));
		if($key[4] == sha1(date("d-m-Y"))) {
			?>
			<?php
			if(!file_exists($conf_dir . "/user/" . sha1($key[1]))) {
				require_once("common/include/classes/sendmail.class.php");
				$sendmail = new Sendmail();
				
				$user_conf = '[User]' . "\n";
				$user_conf .= 'name = "' . $key[0] . '"' . "\n";
				$user_conf .= 'username = "' . $key[1] . '"' . "\n";
				$user_conf .= 'pass = "' . $key[2] . '"' . "\n\n";
				$user_conf .= 'node = "' . $key[3] . '"';
				
				mkdir($conf_dir . "/user/" . sha1($key[1]));
				chmod($conf_dir . "/user/" . sha1($key[1]), 0777);
				
				if($fu = @fopen($conf_dir . "/user/" . sha1($key[1]) . "/user.conf", "w")) {
					fwrite($fu, $user_conf . PHP_EOL);
					fclose($fu);
					
					$user_message = "Ciao " . $key[0] . ",\nla tua registrazione a Ninuxoo e' stata effettuata con successo e da ora potrai accedere con le tue credenziali appena create.";
					$sendmail->send($key[0] . " <" . $key[1] . ">", "Registrazione effettuata con successo", $user_message);
					
					foreach($GLOBALS["general_settings"]["login"]["admin"] as $admins) {
						$admin = parse_ini_file($conf_dir . "/user/" . $admins . "/user.conf", true);
						
						$nome = explode(" ", ucwords($admin["User"]["name"]));
						$admin_message = "Ciao " . $nome[0] . ",\nl'utente " . $key[0] . " (" . $key[1] . "), con riferimento al nodo \"" . $key[3] . "\", si e' appena iscritto al tuo NAS con successo.";
						
						$sendmail->send(ucwords($admin["User"]["name"]) . " <" . $admin["User"]["username"] . ">", "Un utente si e' iscritto a Ninuxoo", $admin_message);
					}
					?>
					<p>Attendere...</p>
					<meta http-equiv="refresh" content="0; url=<?php print $GLOBALS["config"]["NAS"]["http_root"]; ?>/Accedi">
					<?php
				}
			} else {
				?>
				<h1>Ooops...</h1>
				<h2>Questo utente esiste gi&agrave;</h2>
				<?php
			}
		} else {
			?>
			<h1>Tempo scaduto!</h1>
			<h2>Il tempo stabilito per questa operazione &egrave; scaduto.</h2>
			<p>&Egrave; necessario ripetere la <a href="./Registrati">registrazione</a></p>
			<?php
		}
	} else {
		?>
		<link href="common/js/chosen/chosen.css" rel="stylesheet" />
		<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
		<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
		<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
		<script language="Javascript" src="common/js/GnuPG/sha1.js" type="text/javascript"></script>
		<script language="Javascript" src="common/js/GnuPG/base64.js" type="text/javascript"></script>
		<script language="Javascript" src="common/js/GnuPG/PGpubkey.js" type="text/javascript"></script>
		<script type="text/javascript" src="common/js/include/registration_form.js"></script>
		<form method="post" action="" id="registration_frm" onsubmit="$('#page_loader').fadeIn(300); save(); return false;" class="form-horizontal">
			<div class="panel panel-default">
			<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-user"></span>&nbsp;&nbsp;Entra a far parte dei nostri! <a name="Mappa" id="Mappa"></a><small class="help-block">Un sacco di personalizzazioni ti aspettano</small></span></div>
			<div class="panel-body">
				<div class="row">
					<div class="col-md-1">
						<img class="img-responsive" src="common/media/img/group_full_plus_128_333.png" />
					</div>
					<div class="col-md-11">
						<p>
							Personalizza le funzionalit&agrave; di Ninuxoo!<br />
							Imposta avvisi via e-mail sulle novit&agrave;, crea tue pagine personali, accedi alla chat collettiva e tanto altro ancora...
						</p>
						<p>Per registrarsi &egrave; necessario appartenere a un nodo Ninux attivo.</p>
					</div>
				</div>
			</div>
			<hr />
			<div class="panel-body">
				<div class="form-group">
					<label for="user_name" class="col-sm-2 control-label">Nome:</label>
					<div class="col-sm-4">
						<input id="user_name" name="user_name" class="form-control" type="text" value="" autocomplete="off" tabindex="1" required />
					</div>
				</div>
				<div class="form-group">
					<label for="node_name" class="col-sm-2 control-label">Nome del nodo di riferimento:</label>
					<div class="col-sm-4">
						<select name="node_name" id="node_name" disabled="disabled" class="form-control chosen-select" tabindex="2" required><option value=""></option></select>
						<span class="help-block">Puoi controllare il nome sulla <a href="http://map.ninux.org" target="_blank">mappa</a></span>
					</div>
					<span id="nlloader" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;<img src="common/media/img/loader.gif" width="16" /></span>
				</div>
			</div>
			<div class="panel-body">
				<div class="form-group">
					<label for="user_username" class="col-sm-2 control-label">Indirizzo e-mail:</label>
					<div class="col-sm-4">
						<input id="user_username" name="user_username" class="form-control" type="email" value="" autocomplete="off" tabindex="3" required />
					</div>
				</div>
				<div class="form-group">
					<label for="user_password" class="col-sm-2 control-label">Password:</label>
					<div class="col-sm-3">
						<input type="password" id="user_password" name="user_password" class="form-control" value="" autocomplete="off" tabindex="4" required />
					</div>
				</div>
				<div class="form-group">
					<label for="user_password2" class="col-sm-2 control-label">Ripeti la password:</label>
					<div class="col-sm-3">
						<input type="password" id="user_password2" name="user_password2" class="form-control" value="" autocomplete="off" tabindex="5" required />
					</div>
				</div>
			</div>
			<hr />
			<button class="btn btn-primary right" id="register_btn" tabindex="6">Registrati&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
		</form>
		<?php
	}
} else {
	?>
	<h1>Ooops...<h1>
	<h2>Non consentito in questo NAS</h2>
	<p>Il proprietario di questo NAS non ha consentito la registrazione degli utenti su questo dispositivo.<br />Spiacenti ma non &egrave; possibile proseguire con l'operazione</p>
	<?php
}
?>