<?php
$config = parse_ini_file("common/include/conf/config.ini", true);
$setting = parse_ini_file("common/include/conf/general_settings.ini", true);
$usetting = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
?>
<link href="common/js/chosen/chosen.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<!--script type="text/javascript" src="common/js/include/general_settings.js"></script-->

<h1>Impostazioni personali</h1>
<br />
<br />
<form method="post" action="" id="settings_frm" onsubmit="return false;" role="form">
	<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;Profilo <a name="Profilo" id="Profilo"></a><small class="help-block">Dati personali</small></span></div><div class="panel-body">
			<p class="text-danger"><strong>Attenzione</strong>: cambiando la propria chiave pubblica PGP cambier&agrave; anche l'indirizzo e-mail di riferimento, perci&ograve; <u>la modifica avr&agrave; effetto anche su login e notifiche</u>.</p>
			<br />
			<div class="form-group">
				<label for="pgp_pub_key">Chiave pubblica PGP</label>
				<textarea name="pgp_pub_key" id="pgp_pub_key" class="form-control" rows="10"><?php print file_get_contents("common/include/conf/user/" . sha1($username) . "/pubkey.asc"); ?></textarea>
			</div>
			<div class="form-group">
				<label class="control-label">Chiave PGP:</label>
				<p class="form-control-static disable" id="pgp_key"><?php print $usetting["User"]["key"]; ?></p>
			</div>
			<div class="form-group">
				<label class="control-label">Indirizzo e-mail associato alla chiave:</label>
				<p class="form-control-static disable" id="pgp_email"><?php print $usetting["User"]["username"]; ?></p>
			</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="name">Nome e cognome</label>
				<input type="text" class="input-lg" name="name" id="name" value="<?php print $usetting["User"]["name"]; ?>" tabindex="1"/>
				<?php
				if(strlen($usetting["User"]["key"]) > 0) {
					?>
					<small class="help-block">* Dato ricavato automaticamente dal keyserver <a target="_blank" href="http://pgp.mit.edu:11371/pks/lookup?search=0x<?php print $usetting["User"]["key"]; ?>" title="Lookup della tua chiave PGP">pgp.mit.edu</a></small>
					<?php
				}
				?>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-bell"></span>&nbsp;&nbsp;Notifiche <a name="Mappa" id="Mappa"></a><small class="help-block">Imposta gli avvisi</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($usetting["Notification"]["new_files"] == "true") ? "checked" : "") ?> id="new_files" name="new_files" tabindex="8" />
						Nuovi files
					</label>
				</div>
			</div>
		</div>
	</div>
	<hr />
	<button class="btn btn-danger" id="remove_account" tabindex="16">Rimuovi l'account&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-remove"></button>
	<button class="btn btn-primary right" id="save_settings_btn" tabindex="17">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>