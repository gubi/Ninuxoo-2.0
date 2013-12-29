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
<form method="post" action="" id="settings_frm" onsubmit="return false;">
	<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
	<fieldset class="frm">
		<legend>Accesso al Sistema <a name="Accesso_al_Sistema" id="Accesso_al_Sistema"></a></legend>
		<label for="session_length">Durata generale della sessione</label>
		<input type="number" size="5" maxlength="7" min="0" step="10" id="session_length" name="session_length" value="<?php print $setting["login"]["session_length"]; ?>" autofocus tabindex="1" />&nbsp;&nbsp;<span id="hour"></span>
	</fieldset>
	<fieldset id="user_management">
		<legend>Gestione utenti <a name="Gestione_utenti" id="Gestione_utenti"></a></legend>
		
		<span class="left">
			<input type="checkbox" <?php print (($setting["login"]["allow_user_registration"] == "true") ? "checked" : "") ?> id="allow_user_registration" name="allow_user_registration" tabindex="2" />
			<label for="allow_user_registration">Consenti agli utenti di potersi auto-registrare</label>
		</span>
	</fieldset>
	<fieldset class="frm">
		<legend>Editor degli script <a name="Editor_degli_script" id="Editor_degli_script"></a></legend>
		<p>
			L'editor di testo &egrave; un utile strumento per la lettura e la modifica di linguaggi di programmazione.<br />
			Questo tool appartiene all'editor delle configurazioni dei device, ma &egrave; possibile attivarlo anche in tutto il pannello di amministrazione.
		</p>
		<p>
			Durante il suo uso, &egrave; possibile utilizzare le scorciatoie di tastiera per abilitare le sue funzionalit&agrave; aggiuntive.<br />
			Trascinando un file di testo all'interno dell'editor ne verr&agrave; acquisito il relativo contenuto.<br />
		</p>
		<a href="javascript:void(0);" id="show_shortcut_btn"><span>Visualizza</span> le scorciatoie da tastiera</a> attivabili con il focus nell'editor
		<div style="display: none;" id="shortcut_legend">
			<p>
				<b>F11</b>: Attiva la modalit&agrave; schermo intero<br />
				<b>Esc</b>: Esce dalla modalit&agrave; schermo intero<br />
				<b>CTRL+F</b>: Cerca nel testo<br />
				<b>Shift+CTRL+F</b>: Sostituisce un termine nel testo<br />
				<b>CTRL+Invio</b>: Attiva l'autocompletamento<br />
				<b>CTRL+S</b>: Salva il file<br />
				<b>CTRL+D</b>: Attiva il prompt per il download del file
			</p>
		</div>
		<hr />
		<span class="left">
			<input type="checkbox" <?php print (($usetting["User"]["use_editor_always"] == "true") ? "checked" : "") ?> id="allow_editor_always" name="allow_editor_always" tabindex="3" />
			<label for="allow_editor_always">Usa l'editor di linguaggi in tutto il pannello di amministrazione</label>
		</span>
	</fieldset>
	<fieldset>
		<legend>Stazione Meteo <a name="Stazione_Meteo" id="Stazione_Meteo"></a></legend>
		
		<span class="left">
			<label for="station_active">Stato: </label>
			<select id="station_active" name="station_active" tabindex="4">
				<option value="true" <?php print (($config["Meteo"]["station_active"] == "true") ? "selected" : "") ?>>Attiva</option>
				<option value="false" <?php print (($config["Meteo"]["station_active"] == "false") ? "selected" : "") ?>>Non attiva</option>
			</select>
		</span>
		<br />
		<br />
		<br />
		<label for="station_name">Nome della Stazione:</label>
		<input type="text" name="station_name" id="station_name" value="<?php print $config["Meteo"]["station_name"]; ?>" style="width: 50%;" tabindex="5"/>
	</fieldset>
	<fieldset>
		<legend>Mappa <a name="Mappa" id="Mappa"></a></legend>
		<span class="left">
			<input type="checkbox" <?php print (($config["Meteo"]["show_ninux_nodes"] == "true") ? "checked" : "") ?> id="show_ninux_nodes" name="show_ninux_nodes" tabindex="6" />
			<label for="show_ninux_nodes">Mostra i nodi Ninux attivi sulla mappa</label>
		</span>
		<br />
		<span class="left">
			<input type="checkbox" <?php print (($config["Meteo"]["show_region_area"] == "true") ? "checked" : "") ?> id="show_region_area" name="show_region_area" tabindex="7" />
			<label for="show_region_area">Definisci il territorio del comune</label>
		</span>
		<br />
		<span class="left">
			<label for="meteo_refresh">Intervallo di aggiornamento dei dati: </label>
			<input type="number" name="meteo_refresh" id="meteo_refresh" size="4" value="<?php print ($config["Meteo"]["refresh_interval"]/1000); ?>" tabindex="8" />&nbsp;&nbsp;secondi
		</span>
		
		<hr />
		
		<label for="meteo_city">Citt&agrave;:</label>
		<input type="text" name="meteo_city" id="meteo_city" style="width: 30%;" value="<?php print $config["Meteo"]["station_city"]; ?>" tabindex="9" />
		<br />
		<br />
		<label for="meteo_region">Regione:</label>
		<select id="meteo_region" name="meteo_region" tabindex="10">
			<?php
			$regions = array("Abruzzo",  "Basilicata",  "Calabria",  "Campania",  "Emilia-Romagna",  "Friuli-Venezia Giulia",  "Lazio",  "Liguria",  "Lombardia",  "Marche",  "Molise",  "Piemonte",  "Puglia",  "Sardegna",  "Sicilia",  "Toscana",  "Trentino-Alto Adige",  "Umbria",  "Valle d'Aosta",  "Veneto");
			foreach($regions as $region) {
				print '<option value="' . $region . '"' . (($config["Meteo"]["station_region"] == $region) ? "selected" : "") . '>' . $region . '</option>';
			}
			?>
		</select>
		<br />
		<br />
		<label for="meteo_country">Paese:</label>
		<input type="text" name="meteo_country" id="meteo_country" value="<?php print $config["Meteo"]["station_country"]; ?>" tabindex="11"/>
		<hr />
		<label for="meteo_owid">OpenWeather ID:</label>
		<input type="text" name="meteo_owid" id="meteo_owid" value="<?php print $config["Meteo"]["OpenWeatherID"]; ?>" tabindex="12"/>
		
		<br />
		<br />
		<span class="left">
			<label for="meteo_lat">Latitudine:</label>
			<input type="number" name="meteo_lat" id="meteo_lat" value="<?php print $config["Meteo"]["latitude"]; ?>" tabindex="13" />
		</span>
		<span class="left">
			<label for="meteo_lng">Longitudine:</label>
			<input type="number" name="meteo_lng" id="meteo_lng" value="<?php print $config["Meteo"]["longitude"]; ?>" tabindex="14" />
		</span>
		<br />
		<br />
		<label class="left">Altitudine:</label>
		<br />
		<span class="left">
			<label for="meteo_altitude_mt">metri:</label>
			<input type="number" name="meteo_altitude_mt" id="meteo_altitude_mt" size="2" value="<?php print $config["Meteo"]["altitude_mt"]; ?>" tabindex="15" />
		</span>
		<span class="left">
			<label for="meteo_altitude_ft">piedi:</label>
			<input type="number" name="meteo_altitude_ft" id="meteo_altitude_ft" size="4" value="<?php print $config["Meteo"]["altitude_ft"]; ?>" tabindex="16" />
		</span>
		<br />
		<span class="left">
			<label for="meteo_altitude_unit">Unit&agrave; di misura predefinita:</label>
			<select name="meteo_altitude_unit" id="meteo_altitude_unit" style="width: 100px;" tabindex="17">
				<option value="mt"<?php print (($config["Meteo"]["default_altitude_unit"] == "mt") ? "selected" : ""); ?>>metri</option>
				<option value="ft"<?php print (($config["Meteo"]["default_altitude_unit"] == "ft") ? "selected" : ""); ?>>piedi</option>
			</select>
		</span>
	</fieldset>
	<button class="btn btn-primary right" id="save_settings_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>