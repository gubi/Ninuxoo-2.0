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
		<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-comments"></span>&nbsp;&nbsp;Messaggistica<a name="Chat" id="Chat"></a><small class="help-block">Notifiche e chat private</small></span></div>
		<div class="panel-heading">
			<span class="text-primary">Chat private</span>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="chat_status">Mostra sempre il mio stato come:</label>&nbsp;
				<select id="chat_status" name="chat_status" tabindex="66" style="width: 150px;">
					<option value="floating" <?php print (($GLOBALS["user_settings"]["Chat"]["chat_status"] == "online") ? "selected" : "") ?>>In linea</option>
					<option value="open" <?php print (($GLOBALS["user_settings"]["Chat"]["chat_status"] == "do_not_disturb") ? "selected" : "") ?>>Non disturbare</option>
					<option value="open" <?php print (($GLOBALS["user_settings"]["Chat"]["chat_status"] == "out") ? "selected" : "") ?>>Assente</option>
				</select>
			</div>
			<div class="form-group">
				<label for="chat_window">Finestra della chat:</label>&nbsp;
				<select id="chat_window" name="chat_window" tabindex="66" style="width: 150px;">
					<option value="floating" <?php print (($GLOBALS["user_settings"]["Chat"]["chat_window"] == "floating") ? "checked" : "") ?>>A scomparsa</option>
					<option value="open" <?php print (($GLOBALS["user_settings"]["Chat"]["chat_window"] == "open") ? "checked" : "") ?>>Sempre aperta</option>
				</select>
			</div>
		</div>
		<div class="panel-heading">
			<span class="text-primary">Notifiche di gruppo</span>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["user_settings"]["Chat"]["show_ip"] == "true") ? "checked" : ""); ?> id="show_ip" name="show_ip" tabindex="10" />
						Mostra l'indirizzo IP nei messaggi
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="refresh_interval">Tempo di refresh delle notifiche:</label><br />
				<input id="refresh_interval" class="input-lg" type="number" value=" <?php print $GLOBALS["user_settings"]["Chat"]["refresh_interval"]; ?>" name="refresh_interval" step="10" min="500" maxlength="8" size="7" tabindex="11">
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary">
				<span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;Editor degli script <sup><a data-toggle="collapse" href="#script_editor_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="Editor_degli_script" id="Editor_degli_script"></a><small class="help-block"></small>
			</span>
		
			<div id="script_editor_info" class="info panel-body panel-collapse collapse">
				<p>
					L'editor degli script &egrave; un utile strumento per la lettura e la modifica di linguaggi di programmazione.<br />
					Questo tool appartiene all'editor delle configurazioni dei device, ma &egrave; possibile attivarlo anche in tutto il pannello di amministrazione, sostituendolo all'editor di testo per il Markdown.
				</p>
				<p>
					Durante il suo uso, &egrave; possibile utilizzare le scorciatoie di tastiera per abilitare le sue funzionalit&agrave; aggiuntive.<br />
					Trascinando un file di testo all'interno dell'editor ne verr&agrave; acquisito il relativo contenuto.<br />
				</p>
				<?php
				require_once("common/tpl/shortcut_legend.tpl");
				?>
			</div>
		</div>
		<div class="panel-body">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($GLOBALS["user_settings"]["User"]["use_editor_always"] == "true") ? "checked" : "") ?> id="allow_editor_always" name="allow_editor_always" tabindex="60" />
					Usa l'editor di linguaggi in tutto il Pannello di Amministrazione
				</label>
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