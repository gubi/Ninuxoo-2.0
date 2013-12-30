<?php
$config = parse_ini_file("common/include/conf/config.ini", true);
$setting = parse_ini_file("common/include/conf/general_settings.ini", true);
$usetting = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
?>
<link href="common/js/chosen/chosen.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/general_settings.js"></script>

<h1>Impostazioni generali</h1>
<br />
<br />
<form method="post" action="" id="settings_frm" onsubmit="return false;" role="form">
	<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;Accesso al Sistema <a name="Accesso_al_Sistema" id="Accesso_al_Sistema"></a><small class="help-block">Durata generale della sessione</small></span></div>
		<div class="panel-body">
			<input type="number" class="input-lg" size="5" maxlength="7" min="0" step="10" id="session_length" name="session_length" value="<?php print $setting["login"]["session_length"]; ?>" autofocus tabindex="1" />&nbsp;&nbsp;<span id="hour"></span>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;Utenti <a name="Gestione_utenti" id="Gestione_utenti"></a><small class="help-block"></small></span></div>
		<div class="panel-body">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($setting["login"]["allow_user_registration"] == "true") ? "checked" : "") ?> id="allow_user_registration" name="allow_user_registration" tabindex="2" />
					Consenti agli utenti di potersi auto-registrare
				</label>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;Editor degli script <a name="Editor_degli_script" id="Editor_degli_script"></a><small class="help-block"></small></span></div>
		<div class="well">
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
		<div class="panel-body">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($usetting["User"]["use_editor_always"] == "true") ? "checked" : "") ?> id="allow_editor_always" name="allow_editor_always" tabindex="3" />
					Usa l'editor di linguaggi in tutto il Pannello di Amministrazione
				</label>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-cloud"></span>&nbsp;&nbsp;Stazione Meteo <a name="Stazione_Meteo" id="Stazione_Meteo"></a><small class="help-block"></small></span></div>
		
		<div class="panel-body">
			<div class="form-group">
				<label for="station_name">Nome della Stazione:</label>
				<input type="text" class="input-lg" name="station_name" id="station_name" value="<?php print $config["Meteo"]["station_name"]; ?>" style="width: 50%;" tabindex="4"/>
			</div>
			<div class="form-group">
				<label for="station_active">Stato: </label>
				<select id="station_active" name="station_active" tabindex="5">
					<option value="true" <?php print (($config["Meteo"]["station_active"] == "true") ? "selected" : "") ?>>Attiva</option>
					<option value="false" <?php print (($config["Meteo"]["station_active"] == "false") ? "selected" : "") ?>>Non attiva</option>
				</select>
			</div>
			<div class="form-group">
				<label for="meteo_refresh">Intervallo di aggiornamento dei dati: </label>
				<input type="number" class="input-lg" name="meteo_refresh" id="meteo_refresh" size="4" value="<?php print ($config["Meteo"]["refresh_interval"]/1000); ?>" tabindex="6" />&nbsp;&nbsp;secondi
			</div>
			<div class="form-group">
				<label for="meteo_owid">OpenWeather ID:</label>
				<input type="text" class="input-lg" name="meteo_owid" id="meteo_owid" value="<?php print $config["Meteo"]["OpenWeatherID"]; ?>" tabindex="7"/>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;Mappa <a name="Mappa" id="Mappa"></a><small class="help-block">Parametri di visualizzazione</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($config["Meteo"]["show_ninux_nodes"] == "true") ? "checked" : "") ?> id="show_ninux_nodes" name="show_ninux_nodes" tabindex="8" />
						Mostra i nodi Ninux attivi sulla mappa
					</label>
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($config["Meteo"]["show_region_area"] == "true") ? "checked" : "") ?> id="show_region_area" name="show_region_area" tabindex="9" />
						Definisci il territorio del comune
					</label>
				</div>
			</div>
		</div>
		<hr />
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_city">Citt&agrave;:</label>
				<input type="text" class="input-lg" name="meteo_city" id="meteo_city" value="<?php print $config["Meteo"]["station_city"]; ?>" tabindex="10" />
			</div>
			<div class="form-group">
				<label for="meteo_region">Regione:</label>
				<select id="meteo_region" name="meteo_region" tabindex="11">
					<?php
					$regions = array("Abruzzo",  "Basilicata",  "Calabria",  "Campania",  "Emilia-Romagna",  "Friuli-Venezia Giulia",  "Lazio",  "Liguria",  "Lombardia",  "Marche",  "Molise",  "Piemonte",  "Puglia",  "Sardegna",  "Sicilia",  "Toscana",  "Trentino-Alto Adige",  "Umbria",  "Valle d'Aosta",  "Veneto");
					foreach($regions as $region) {
						print '<option value="' . $region . '"' . (($config["Meteo"]["station_region"] == $region) ? "selected" : "") . '>' . $region . '</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="meteo_country">Paese:</label>
				<input type="text" class="input-lg" name="meteo_country" id="meteo_country" value="<?php print $config["Meteo"]["station_country"]; ?>" tabindex="11"/>
			</div>
		</div>
		<div class="panel-heading"><span class="text-primary">Coordinate</span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_lat">Latitudine:</label>
				<input type="number" class="input-lg" name="meteo_lat" id="meteo_lat" value="<?php print $config["Meteo"]["latitude"]; ?>" tabindex="12" />
			</div>
			<div class="form-group">
				<label for="meteo_lng">Longitudine:</label>
				<input type="number" class="input-lg" name="meteo_lng" id="meteo_lng" value="<?php print $config["Meteo"]["longitude"]; ?>" tabindex="13" />
			</div>
		</div>
		<div class="panel-heading"><span class="text-primary">Quota</span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_altitude_mt">metri:</label>
				<input type="number" class="input-lg" name="meteo_altitude_mt" id="meteo_altitude_mt" size="2" value="<?php print $config["Meteo"]["altitude_mt"]; ?>" tabindex="14" />
			
				<label for="meteo_altitude_ft">piedi:</label>
				<input type="number" class="input-lg" name="meteo_altitude_ft" id="meteo_altitude_ft" size="4" value="<?php print $config["Meteo"]["altitude_ft"]; ?>" tabindex="15" />
			</div>
			<div class="form-group">
				<label for="meteo_altitude_unit">Unit&agrave; di misura predefinita:</label>
				<select name="meteo_altitude_unit" id="meteo_altitude_unit" style="width: 100px;" tabindex="16">
					<option value="mt"<?php print (($config["Meteo"]["default_altitude_unit"] == "mt") ? "selected" : ""); ?>>metri</option>
					<option value="ft"<?php print (($config["Meteo"]["default_altitude_unit"] == "ft") ? "selected" : ""); ?>>piedi</option>
				</select>
			</div>
		</div>
	</div>
	<hr />
	<button class="btn btn-primary right" id="save_settings_btn" tabindex="17">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>