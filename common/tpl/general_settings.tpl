<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
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
			<input type="number" class="input-lg" size="5" maxlength="7" min="0" step="10" id="session_length" name="session_length" value="<?php print $GLOBALS["general_settings"]["login"]["session_length"]; ?>" autofocus tabindex="1" />
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-building-o"></span>&nbsp;&nbsp;Schede e dettagli sui files <a name="Gestione_schede" id="Gestione_schede"></a><small class="help-block">Criterio di acquisizione dei contenuti dal nome del file</small></span></div>
		<div class="panel-body">
			<p>
				Questa impostazione si riferisce a tutti quei files che non godono di metadati che identificano il contenuto.<br />
				In tal caso Ninuxoo interpreter&agrave; il nome del file, ed &egrave; quindi possibile stabilire l'ordine dei termini in cui sono rinominati i files.
			</p>
			<p><b>Nota:</b> gli elementi non testuali (<tt>. - , ~</tt> ecc...) verranno ignorati, mentre quelli numerici interpretati: se la cifra &egrave; di 1 o 2 caratteri sar&agrave; una traccia, 4 una data, ecc...</p>
			<ul>
				<li><small>Il libro citato nell'esempio, "Pro Git", &egrave; rilasciato su licenza <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">CC - NC BY SA</a> ed &egrave; disponibile presso questo indirizzo: <a href="http://git-scm.com/book/it" target="_blank">http://git-scm.com/book/it</a></small></li>
				<li><small>Il brano citato nell'esempio, "Flog it.", &egrave; rilasciato su licenza <a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC - BY SA</a> ed &egrave; disponibile presso questo indirizzo: <a href="http://www.jamendo.com/en/track/1043921/flog-it." target="_blank">http://www.jamendo.com/en/track/1043921/flog-it.</a></small></li>
				<li><small>Il film citato nell'esempio, "Big Buck Bunny", &egrave; rilasciato su licenza <a href="http://www.bigbuckbunny.org/index.php/about/" target="_blank">CC - BY</a> ed &egrave; disponibile presso questo indirizzo: <a href="http://www.bigbuckbunny.org/index.php/download/" target="_blank">http://www.bigbuckbunny.org/index.php/download/</a></small></li>
			</ul>
		</div>
		<div class="panel-body">
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs nav-stacked">
					<li class="active">
						<a href="#collapseBook" data-toggle="tab" class="btn btn-link"><span class="fa fa-book"></span>&nbsp;&nbsp;File ebook</a>
					</li>
					<li>
						<a href="#collapseAudio" data-toggle="tab" class="btn btn-link"><span class="fa fa-music"></span>&nbsp;&nbsp;File audio</a>
					</li>
					<li>
						<a href="#collapseVideo" data-toggle="tab" class="btn btn-link"><span class="fa fa-film"></span>&nbsp;&nbsp;File video</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane panel-body fade in active" id="collapseBook">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="title_author_editor" name="data_scan_ebook" value="title_author_editor" tabindex="3" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "title_author_editor") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="title_author_editor">titolo - autore - editore</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Pro Git - Scott Chacon (Apress).pdf</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="author_title_editor" name="data_scan_ebook" value="author_title_editor" tabindex="3" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "author_title_editor") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="author_title_editor">autore - titolo - editore</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Scott Chacon - Pro Git [Apress].pdf</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="author_editor_title" name="data_scan_ebook" value="author_editor_title" tabindex="3" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "author_editor_title") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="author_editor_title">autore - editore - titolo</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Scott Chacon, Apress - Pro Git.pdf</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" class="personalized" name="data_scan_ebook" value="personalized" tabindex="3" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "true") ? "personalized" : ""); ?> />
								</span>
								<input type="text" class="form-control" id="data_scan_ebook_personalized" name="data_scan_ebook_personalized" placeholder="Personalizzato (regex)..." value="<?php print $GLOBALS["general_settings"]["file data"]["scan_ebook_name_regex"]; ?>" />
							</div>
							<span class="help-block">Esempio: <tt class="info"></tt></span>
						</div>
					</div>
					<div class="tab-pane panel-body fade in" id="collapseAudio">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="no_track_artist_album" name="data_scan_audio" value="no_track_artist_album" tabindex="4" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "no_track_artist_album") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="no_track_artist_album">#traccia - brano - artista - album</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">09 - Flog it, Conway Hambone (Live at the Social).mp3</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="artist_album_no_track" name="data_scan_audio" value="artist_album_no_track" tabindex="4" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "artist_album_no_track") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="artist_album_no_track">artista - album - #traccia - brano</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Conway Hambone - Live at the Social - 09 - Flog it.mp3</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="artist_no_track_album" name="data_scan_audio" value="artist_no_track_album" tabindex="4" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "artist_no_track_album") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="artist_no_track_album">artista - #traccia - brano - album</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Conway Hambone 09. Flog it - Live at the Social.mp3</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" class="personalized" name="data_scan_audio" value="personalized" tabindex="4" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "personalized") ? "checked" : ""); ?> />
								</span>
								<input type="text" class="form-control" id="data_scan_audio_personalized" name="data_scan_audio_personalized" placeholder="Personalizzato (regex)..." value="<?php print $GLOBALS["general_settings"]["file data"]["scan_audio_name_regex"]; ?>" />
							</div>
							<span class="help-block">Esempio: <tt class="info"></tt></span>
						</div>
					</div>
					<div class="tab-pane panel-body fade in" id="collapseVideo">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="title_year_director" name="data_scan_video" value="title_year_director" tabindex="5" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "title_year_director") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="title_year_director">titolo - anno - regista</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Big Buck Bunny (2008 - Sacha Goedegebure).avi</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="year_title_director" name="data_scan_video" value="year_title_director" tabindex="5" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "year_title_director") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="year_title_director">anno - titolo - regista</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">2008, Big Buck Bunny (Sacha Goedegebure).avi</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="director_title_year" name="data_scan_video" value="director_title_year" tabindex="5" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "director_title_year") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="director_title_year">regista - titolo - anno</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Sacha Goedegebure - Big Buck Bunny [2008].avi</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" class="personalized" name="data_scan_video" value="personalized" tabindex="5" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "personalized") ? "checked" : ""); ?> />
								</span>
								<input type="text" class="form-control" id="data_scan_video_personalized" name="data_scan_video_personalized" placeholder="Personalizzato (regex)..." value="<?php print $GLOBALS["general_settings"]["file data"]["scan_video_name_regex"]; ?>" />
							</div>
							<span class="help-block">Esempio: <tt class="info"></tt></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;Utenti <a name="Gestione_utenti" id="Gestione_utenti"></a><small class="help-block"></small></span></div>
		<div class="panel-body">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($GLOBALS["general_settings"]["login"]["allow_user_registration"] == "true") ? "checked" : ""); ?> id="allow_user_registration" name="allow_user_registration" tabindex="6" />
					Consenti agli utenti di potersi auto-registrare
				</label>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-edit"></span>&nbsp;&nbsp;Editor degli script <a name="Editor_degli_script" id="Editor_degli_script"></a><small class="help-block"></small></span></div>
		<div class="panel-heading">
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
					<input type="checkbox" <?php print (($GLOBALS["user_settings"]["User"]["use_editor_always"] == "true") ? "checked" : "") ?> id="allow_editor_always" name="allow_editor_always" tabindex="7" />
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
				<input type="text" class="input-lg" name="station_name" id="station_name" value="<?php print $GLOBALS["config"]["Meteo"]["station_name"]; ?>" style="width: 50%;" tabindex="8"/>
			</div>
			<div class="form-group">
				<label for="station_active">Stato: </label>
				<select id="station_active" name="station_active" tabindex="9" style="width: 150px;">
					<option value="true" <?php print (($GLOBALS["config"]["Meteo"]["station_active"] == "true") ? "selected" : "") ?>>Attiva</option>
					<option value="false" <?php print (($GLOBALS["config"]["Meteo"]["station_active"] == "false") ? "selected" : "") ?>>Non attiva</option>
				</select>
			</div>
			<div class="form-group">
				<label for="meteo_refresh">Intervallo di aggiornamento dei dati: </label>
				<input type="number" class="input-lg" name="meteo_refresh" id="meteo_refresh" size="5" maxlength="7" min="0" step="10" value="<?php print ($GLOBALS["config"]["Meteo"]["refresh_interval"]); ?>" tabindex="9" />
			</div>
			<div class="form-group">
				<label for="meteo_owid">OpenWeather ID:</label>
				<input type="text" class="input-lg" name="meteo_owid" id="meteo_owid" value="<?php print $GLOBALS["config"]["Meteo"]["OpenWeatherID"]; ?>" tabindex="10"/>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;Mappa <a name="Mappa" id="Mappa"></a><small class="help-block">Parametri di visualizzazione</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["config"]["Meteo"]["show_ninux_nodes"] == "true") ? "checked" : "") ?> id="show_ninux_nodes" name="show_ninux_nodes" tabindex="11" />
						Mostra i nodi Ninux attivi sulla mappa
					</label>
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["config"]["Meteo"]["show_region_area"] == "true") ? "checked" : "") ?> id="show_region_area" name="show_region_area" tabindex="12" />
						Definisci il territorio del comune
					</label>
				</div>
			</div>
		</div>
		<hr />
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_city">Citt&agrave;:</label>
				<input type="text" class="input-lg" name="meteo_city" id="meteo_city" value="<?php print $GLOBALS["config"]["Meteo"]["station_city"]; ?>" tabindex="13" />
			</div>
			<div class="form-group">
				<label for="meteo_region">Regione:</label>
				<select id="meteo_region" name="meteo_region" tabindex="14" style="width: 200px;">
					<?php
					$regions = array("Abruzzo",  "Basilicata",  "Calabria",  "Campania",  "Emilia-Romagna",  "Friuli-Venezia Giulia",  "Lazio",  "Liguria",  "Lombardia",  "Marche",  "Molise",  "Piemonte",  "Puglia",  "Sardegna",  "Sicilia",  "Toscana",  "Trentino-Alto Adige",  "Umbria",  "Valle d'Aosta",  "Veneto");
					foreach($regions as $region) {
						print '<option value="' . $region . '"' . (($GLOBALS["config"]["Meteo"]["station_region"] == $region) ? "selected" : "") . '>' . $region . '</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="meteo_country">Paese:</label>
				<input type="text" class="input-lg" name="meteo_country" id="meteo_country" value="<?php print $GLOBALS["config"]["Meteo"]["station_country"]; ?>" tabindex="15"/>
			</div>
		</div>
		<div class="panel-heading"><span class="text-primary">Coordinate</span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_lat">Latitudine:</label>
				<input type="number" class="input-lg" name="meteo_lat" id="meteo_lat" value="<?php print $GLOBALS["config"]["Meteo"]["latitude"]; ?>" tabindex="16" />
			</div>
			<div class="form-group">
				<label for="meteo_lng">Longitudine:</label>
				<input type="number" class="input-lg" name="meteo_lng" id="meteo_lng" value="<?php print $GLOBALS["config"]["Meteo"]["longitude"]; ?>" tabindex="17" />
			</div>
		</div>
		<div class="panel-heading"><span class="text-primary">Quota</span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_altitude_mt">metri:</label>
				<input type="number" class="input-lg" name="meteo_altitude_mt" id="meteo_altitude_mt" size="2" value="<?php print $GLOBALS["config"]["Meteo"]["altitude_mt"]; ?>" tabindex="18" />
			
				<label for="meteo_altitude_ft">piedi:</label>
				<input type="number" class="input-lg" name="meteo_altitude_ft" id="meteo_altitude_ft" size="4" value="<?php print $GLOBALS["config"]["Meteo"]["altitude_ft"]; ?>" tabindex="19" />
			</div>
			<div class="form-group">
				<label for="meteo_altitude_unit">Unit&agrave; di misura predefinita:</label>
				<select name="meteo_altitude_unit" id="meteo_altitude_unit" style="width: 100px;" tabindex="20">
					<option value="mt"<?php print (($GLOBALS["config"]["Meteo"]["default_altitude_unit"] == "mt") ? "selected" : ""); ?>>metri</option>
					<option value="ft"<?php print (($GLOBALS["config"]["Meteo"]["default_altitude_unit"] == "ft") ? "selected" : ""); ?>>piedi</option>
				</select>
			</div>
		</div>
	</div>
	<hr />
	<button class="btn btn-primary right" id="save_settings_btn" tabindex="21">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>
