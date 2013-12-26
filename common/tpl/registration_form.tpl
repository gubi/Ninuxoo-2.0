<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>

<?php
if(isset($_GET["q"]) && trim($_GET["q"]) !== "") {
	$key = explode("::::", urldecode(base64_decode($_GET["q"])));
	if($key[4] == sha1(date("d-m-Y"))) {
		?>
		<?php
		$conf_dir = "common/include/conf";
		$config = parse_ini_file("common/include/conf/config.ini", true);
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
				
				$general_settings = parse_ini_file($conf_dir . "/general_settings.ini", true);
				foreach($general_settings["login"]["admin"] as $admins) {
					$admin = parse_ini_file($conf_dir . "/user/" . $admins . "/user.conf", true);
					
					$nome = explode(" ", ucwords($admin["User"]["name"]));
					$admin_message = "Ciao " . $nome[0] . ",\nl'utente " . $key[0] . " (" . $key[1] . "), con riferimento al nodo \"" . $key[3] . "\", si e' appena iscritto al tuo NAS con successo.";
					
					$sendmail->send(ucwords($admin["User"]["name"]) . " <" . $admin["User"]["username"] . ">", "Un utente si e' iscritto a Ninuxoo", $admin_message);
				}
				?>
				<p>Attendere...</p>
				<meta http-equiv="refresh" content="0; url=<?php print $config["NAS"]["http_root"]; ?>/Accedi">
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
	<script type="text/javascript" src="common/js/include/registration_form.js"></script>
	<form method="post" action="" id="registration_frm" onsubmit="return false;">
		<fieldset class="frm">
			<legend>Entra a far parte dei nostri!</legend>
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td><img src="common/media/img/group_full_plus_128_333.png" /></td>
					<td>
						<p>
							La registrazione a Ninuxoo consente di poter usufruire di moltissime funzionalit&agrave; personalizzate.<br />
							Traccia dello storico delle ricerche, avvisi via e-mail sulle novit&agrave;, jukebox personalizzabile, pagine proprie, e tanto altro ancora...
						</p>
						<p>Per registrarsi &egrave; necessario appartenere a un nodo Ninux attivo.</p>
					</td>
				</tr>
			</table>
			<hr />
			<label for="user_name" class="required">Nome:</label>
			<input id="user_name" name="user_name" type="text" value="" autocomplete="off" tabindex="1" />
			
			<label for="node_name" class="required">Nodo Ninux:</label>
			<small style="margin-bottom: 20px;">Puoi controllare il nome sulla <a href="http://map.ninux.org" target="_blank">mappa</a></small><br />
			<input id="node_name" name="node_name" type="text" value="" autocomplete="off" tabindex="2" />
			
			<br />
			<label for="user_username" class="required">Indirizzo e-mail:</label>
			<input id="user_username" name="user_username" type="email" value="" autocomplete="off" tabindex="3" />
			<br />
			<br />
			<span class="left">
				<label for="user_password" class="required">Password:</label>
				<input type="password" id="user_password" name="user_password" value="" autocomplete="off" tabindex="4" />
			</span>
			<span class="left">
				<label for="user_password2" class="required">Ripeti la password:</label>
				<input type="password" id="user_password2" name="user_password2" value="" autocomplete="off" tabindex="5" />
			</span>
		</fieldset>
		<button id="register_btn" tabindex="6">Registrati</button>
	</form>
	<?php
}
?>