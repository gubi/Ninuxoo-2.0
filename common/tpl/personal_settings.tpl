<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/personal_settings.js"></script>

<h1>Impostazioni personali</h1>
<br />
<br />
<form method="post" action="" id="personal_settings_frm" onsubmit="return false;" role="form">
	<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;Profilo <a name="Profilo" id="Profilo"></a><small class="help-block">Dati personali</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="name">Nome:</label><br />
				<input type="text" class="input-lg" name="name" id="name" value="<?php print $GLOBALS["user_settings"]["User"]["name"]; ?>" tabindex="1"/>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-bell"></span>&nbsp;&nbsp;Notifiche <a name="Notifiche" id="Notifiche"></a><small class="help-block">Imposta gli avvisi</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["user_settings"]["Notification"]["new_files"] == "true") ? "checked" : ""); ?> id="new_files" name="new_files" tabindex="8" />
						Avvisa via mail quando ci sono dei nuovi files
					</label>
				</div>
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["user_settings"]["Notification"]["new_chat_messages"] == "true") ? "checked" : ""); ?> id="new_chat_messages" name="new_chat_messages" tabindex="9" />
						Notifica i nuovi messagi in chat pubblica
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-comments"></span>&nbsp;&nbsp;Chat di gruppo <a name="Chat_di_gruppo" id="Chat_di_gruppo"></a><small class="help-block">Imposta gli avvisi</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["user_settings"]["Chat"]["show_ip"] == "true") ? "checked" : ""); ?> id="show_ip" name="show_ip" tabindex="10" />
						Mostra l'indirizzo IP nei messaggi della chat
					</label>
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="refresh_interval">Tempo di refresh:</label><br />
				<input id="refresh_interval" class="input-lg" type="number" value=" <?php print $GLOBALS["user_settings"]["Chat"]["refresh_interval"]; ?>" name="refresh_interval" step="10" min="500" maxlength="8" size="7" tabindex="11">
			</div>
		</div>
	</div>
	<hr />
	<?php
	if(!$GLOBALS["is_admin"]) {
		?>
		<button class="btn btn-danger" id="remove_account" tabindex="16">Rimuovi l'account&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-remove"></button>
		<?php
	}
	?>
	<button class="btn btn-primary right" id="save_settings_btn" tabindex="17">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>