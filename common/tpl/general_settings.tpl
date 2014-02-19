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
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-time"></span>&nbsp;&nbsp;Accesso al Sistema <a name="Accesso_al_Sistema" id="Accesso_al_Sistema"></a><small class="help-block">Impostazioni relative alle connessioni al sistema</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="input-group">
					<label for="session_length">Durata generale di una sessione: </label>
					<input type="number" class="input-lg" size="5" maxlength="7" min="0" step="10" id="session_length" name="session_length" value="<?php print $GLOBALS["general_settings"]["login"]["session_length"]; ?>" autofocus tabindex="1" />
					<span class="help-block" style="display: none;">Le modifiche avranno effetto sugli accessi successivi al salvataggio.<br />Nel tuo caso dal prossimo accesso</span>
				</div>
			</div>
			<div class="form-group">
				<div class="input-group">
					<div class="checkbox">
						<label>
							<input type="checkbox" <?php print (($GLOBALS["general_settings"]["login"]["allow_browser_save"] == "true") ? "checked" : ""); ?> id="allow_browser_save" name="allow_browser_save" tabindex="2" />
							Permetti ai browsers di salvare i dati di connessione
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary">
				<span class="fa fa-building-o"></span>&nbsp;&nbsp;Schede e dettagli sui files <sup><a data-toggle="collapse" href="#file_data_info" class="text-muted"><span class="fa fa-info"></span></a></sup>
				<a name="Gestione_schede" id="Gestione_schede"></a>
				<small class="help-block">Criterio di acquisizione dei contenuti dal nome del file</small>
			</span>
			
			<div id="file_data_info" class="info panel-body panel-collapse collapse">
				<p>
					Questa impostazione si riferisce a tutti quei files che non godono di metadati che identificano il contenuto.<br />
					In tal caso Ninuxoo interpreter&agrave; il nome del file, ed &egrave; quindi possibile stabilire il criterio in cui lo far&agrave;.
				</p>
				<p><b>Nota:</b> gli elementi non testuali che separano i termini nel nome (<tt>. - , ~</tt> ecc...) verranno ignorati.</p>
				<ul>
					<li><small>Il libro citato nell'esempio, "Pro Git", &egrave; rilasciato su licenza <a href="http://creativecommons.org/licenses/by-nc-sa/3.0/" target="_blank">CC - NC BY SA</a> ed &egrave; disponibile presso questo indirizzo: <a href="http://git-scm.com/book/it" target="_blank">http://git-scm.com/book/it</a></small></li>
					<li><small>Il brano citato nell'esempio, "Flog it.", &egrave; rilasciato su licenza <a href="http://creativecommons.org/licenses/by-sa/3.0/" target="_blank">CC - BY SA</a> ed &egrave; disponibile presso questo indirizzo: <a href="http://www.jamendo.com/en/track/1043921/flog-it." target="_blank">http://www.jamendo.com/en/track/1043921/flog-it.</a></small></li>
					<li><small>Il film citato nell'esempio, "Big Buck Bunny", &egrave; rilasciato su licenza <a href="http://www.bigbuckbunny.org/index.php/about/" target="_blank">CC - BY</a> ed &egrave; disponibile presso questo indirizzo: <a href="http://www.bigbuckbunny.org/index.php/download/" target="_blank">http://www.bigbuckbunny.org/index.php/download/</a></small></li>
				</ul>
			</div>
		</div>
		<div class="panel-body">
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs nav-stacked">
					<li class="active">
						<a href="#collapseBook" data-toggle="tab" class="btn btn-link" tabindex="5"><span class="fa fa-book"></span>&nbsp;&nbsp;File ebook</a>
					</li>
					<li>
						<a href="#collapseAudio" data-toggle="tab" class="btn btn-link" tabindex="6"><span class="fa fa-music"></span>&nbsp;&nbsp;File audio</a>
					</li>
					<li>
						<a href="#collapseVideo" data-toggle="tab" class="btn btn-link" tabindex="7"><span class="fa fa-film"></span>&nbsp;&nbsp;File video</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane panel-body fade in active" id="collapseBook">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="title_author_editor" name="data_scan_ebook" value="title_author_editor" tabindex="10" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "title_author_editor") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="title_author_editor">titolo - autore - editore</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Pro Git - Scott Chacon (Apress).pdf</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="author_title_editor" name="data_scan_ebook" value="author_title_editor" tabindex="10" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "author_title_editor") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="author_title_editor">autore - titolo - editore</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Scott Chacon - Pro Git [Apress].pdf</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="author_editor_title" name="data_scan_ebook" value="author_editor_title" tabindex="10" <?php print (($GLOBALS["general_settings"]["file data"]["scan_ebook_name_order"] == "author_editor_title") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="author_editor_title">autore - editore - titolo</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Scott Chacon, Apress - Pro Git.pdf</tt></span>
						</div>
					</div>
					<div class="tab-pane panel-body fade in" id="collapseAudio">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="no_track_artist_album" name="data_scan_audio" value="no_track_artist_album" tabindex="15" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "no_track_artist_album") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="no_track_artist_album">#traccia - brano - artista - album</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">09 - Flog it, Conway Hambone (Live at the Social).mp3</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="artist_album_no_track" name="data_scan_audio" value="artist_album_no_track" tabindex="15" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "artist_album_no_track") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="artist_album_no_track">artista - album - #traccia - brano</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Conway Hambone - Live at the Social - 09 - Flog it.mp3</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="artist_no_track_album" name="data_scan_audio" value="artist_no_track_album" tabindex="15" <?php print (($GLOBALS["general_settings"]["file data"]["scan_audio_name_order"] == "artist_no_track_album") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="artist_no_track_album">artista - #traccia - brano - album</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Conway Hambone 09. Flog it - Live at the Social.mp3</tt></span>
						</div>
					</div>
					<div class="tab-pane panel-body fade in" id="collapseVideo">
						<div class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="title_year_director" name="data_scan_video" value="title_year_director" tabindex="20" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "title_year_director") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="title_year_director">titolo - anno - regista</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Big Buck Bunny (2008 - Sacha Goedegebure).avi</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="year_title_director" name="data_scan_video" value="year_title_director" tabindex="20" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "year_title_director") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="year_title_director">anno - titolo - regista</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">2008, Big Buck Bunny (Sacha Goedegebure).avi</tt></span>
							<div class="input-group">
								<span class="input-group-addon">
									<input type="radio" id="director_title_year" name="data_scan_video" value="director_title_year" tabindex="20" <?php print (($GLOBALS["general_settings"]["file data"]["scan_video_name_order"] == "director_title_year") ? "checked" : ""); ?> />
								</span>
								<label class="form-control" for="director_title_year">regista - titolo - anno</label>
							</div>
							<span class="help-block">Esempio: <tt class="info">Sacha Goedegebure - Big Buck Bunny [2008].avi</tt></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary">
				<button title="Abilita il caching" id="allow_caching" class="btn btn-default right" tabindex="25"><span class="fa fa-fw <?php print (($GLOBALS["general_settings"]["caching"]["allow_caching"] == "true") ? "fa-check-square-o" : "fa-square-o"); ?>"></span></button>
				<input type="checkbox" id="allow_caching_checkbox" name="allow_caching" <?php print (($GLOBALS["general_settings"]["caching"]["allow_caching"] == "true") ? "checked" : ""); ?> style="display: none;" />
				<span class="fa fa-clipboard"></span>&nbsp;&nbsp;Caching <sup><a data-toggle="collapse" href="#caching_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="Caching" id="Caching"></a><small class="help-block">Salvataggio temporaneo di dati dinamici</small>
			</span>
			
			<div id="caching_info" class="info panel-body panel-collapse collapse">
				<p>
					Il caching dei dati consente di salvare i dati dinamici (come ad esempio quelli semantici) in locale, in modo da non dover attendere ogni volta il loro caricamento e/o generazione.<br />
					Questo &egrave; molto utile nel caso in cui non si disponga di una connessione ad Internet particolarmente veloce, o non si voglia stressare troppo la risorsa.
				</p>
				<b>Nota:</b> i dati in locale potrebbero occupare molto spazio se si hanno molti files in condivisione.
			</div>
		</div>
		<div class="panel-heading">
			<span class="text-primary">Dati semantici</span>
		</div>
		<div class="panel-body caching_active">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($GLOBALS["general_settings"]["caching"]["save_semantic_data"] == "true") ? "checked" : ""); ?> id="save_semantic_data" name="save_semantic_data" tabindex="35" />
					Memorizza i dati semantici
				</label>
			</div>
			<div class="form-group">
				<label for="semantic_caching_refresh">Re-sincronizza i dati acquisiti se pi&ugrave; vecchi di: </label>
				<select style="width: 150px;" tabindex="36" name="semantic_caching_refresh" id="semantic_caching_refresh">
					<option value="never" <?php print (($GLOBALS["general_settings"]["caching"]["semantic_caching_refresh"] == "never") ? "selected" : ""); ?>>Mai</option>
					<option value="7" <?php print (($GLOBALS["general_settings"]["caching"]["semantic_caching_refresh"] == "7") ? "selected" : ""); ?>>1 settimana</option>
					<option value="15" <?php print (($GLOBALS["general_settings"]["caching"]["semantic_caching_refresh"] == "15") ? "selected" : ""); ?>>15 giorni</option>
					<option value="30" <?php print (($GLOBALS["general_settings"]["caching"]["semantic_caching_refresh"] == "30") ? "selected" : ""); ?>>1 mese</option>
					<option value="180" <?php print (($GLOBALS["general_settings"]["caching"]["semantic_caching_refresh"] == "180") ? "selected" : ""); ?>>6 mesi</option>
					<option value="365" <?php print (($GLOBALS["general_settings"]["caching"]["semantic_caching_refresh"] == "365") ? "selected" : ""); ?>>1 anno</option>
				</select>
			</div>
		</div>
		<div class="panel-heading">
			<span class="text-primary">Spettri audio</span>
		</div>
		<div class="panel-body caching_active">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($GLOBALS["general_settings"]["caching"]["save_audio_spectum"] == "true") ? "checked" : ""); ?> id="save_audio_spectum" name="save_audio_spectum" tabindex="45" />
					Memorizza le immagini degli spettri audio
				</label>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary"><span class="glyphicon glyphicon-user"></span>&nbsp;&nbsp;Utenti <a name="Gestione_utenti" id="Gestione_utenti"></a><small class="help-block"></small></span>
		</div>
		<div class="panel-body">
			<div class="checkbox">
				<label>
					<input type="checkbox" <?php print (($GLOBALS["general_settings"]["login"]["allow_user_registration"] == "true") ? "checked" : ""); ?> id="allow_user_registration" name="allow_user_registration" tabindex="55" />
					Consenti agli utenti di potersi auto-registrare
				</label>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-cloud"></span>&nbsp;&nbsp;Stazione Meteo <a name="Stazione_Meteo" id="Stazione_Meteo"></a><small class="help-block"></small></span></div>
		
		<div class="panel-body">
			<div class="form-group">
				<label for="station_name">Nome della Stazione:</label>
				<input type="text" class="input-lg" name="station_name" id="station_name" value="<?php print $GLOBALS["config"]["Meteo"]["station_name"]; ?>" style="width: 50%;" tabindex="65"/>
			</div>
			<div class="form-group">
				<label for="station_active">Stato: </label>
				<select id="station_active" name="station_active" tabindex="66" style="width: 150px;">
					<option value="true" <?php print (($GLOBALS["config"]["Meteo"]["station_active"] == "true") ? "selected" : "") ?>>Attiva</option>
					<option value="false" <?php print (($GLOBALS["config"]["Meteo"]["station_active"] == "false") ? "selected" : "") ?>>Non attiva</option>
				</select>
			</div>
			<div class="form-group">
				<label for="meteo_refresh">Intervallo di aggiornamento dei dati: </label>
				<input type="number" class="input-lg" name="meteo_refresh" id="meteo_refresh" size="5" maxlength="5" min="0" step="10" value="<?php print ($GLOBALS["config"]["Meteo"]["refresh_interval"]); ?>" tabindex="70" />
			</div>
			<div class="form-group">
				<label for="meteo_owid">OpenWeather ID:</label>
				<input type="text" class="input-lg" name="meteo_owid" id="meteo_owid" value="<?php print $GLOBALS["config"]["Meteo"]["OpenWeatherID"]; ?>" tabindex="75"/>
			</div>
		</div>
	</div>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-map-marker"></span>&nbsp;&nbsp;Mappa <a name="Mappa" id="Mappa"></a><small class="help-block">Parametri di visualizzazione</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["config"]["Meteo"]["show_ninux_nodes"] == "true") ? "checked" : "") ?> id="show_ninux_nodes" name="show_ninux_nodes" tabindex="80" />
						Mostra i nodi Ninux attivi sulla mappa
					</label>
				</div>
			</div>
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" <?php print (($GLOBALS["config"]["Meteo"]["show_region_area"] == "true") ? "checked" : "") ?> id="show_region_area" name="show_region_area" tabindex="85" />
						Definisci il territorio del comune
					</label>
				</div>
			</div>
		</div>
		<hr />
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_city">Citt&agrave;:</label>
				<input type="text" class="input-lg" name="meteo_city" id="meteo_city" value="<?php print $GLOBALS["config"]["Meteo"]["station_city"]; ?>" tabindex="90" />
			</div>
			<div class="form-group">
				<label for="meteo_region">Regione:</label>
				<select id="meteo_region" name="meteo_region" tabindex="95" style="width: 200px;">
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
				<input type="text" class="input-lg" name="meteo_country" id="meteo_country" value="<?php print $GLOBALS["config"]["Meteo"]["station_country"]; ?>" tabindex="100"/>
			</div>
		</div>
		<div class="panel-heading"><span class="text-primary">Coordinate</span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_lat">Latitudine:</label>
				<input type="number" class="input-lg" name="meteo_lat" id="meteo_lat" value="<?php print $GLOBALS["config"]["Meteo"]["latitude"]; ?>" tabindex="105" />
			</div>
			<div class="form-group">
				<label for="meteo_lng">Longitudine:</label>
				<input type="number" class="input-lg" name="meteo_lng" id="meteo_lng" value="<?php print $GLOBALS["config"]["Meteo"]["longitude"]; ?>" tabindex="110" />
			</div>
		</div>
		<div class="panel-heading"><span class="text-primary">Quota</span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="meteo_altitude_mt">metri:</label>
				<input type="number" class="input-lg" name="meteo_altitude_mt" id="meteo_altitude_mt" size="2" value="<?php print $GLOBALS["config"]["Meteo"]["altitude_mt"]; ?>" tabindex="115" />
			
				<label for="meteo_altitude_ft">piedi:</label>
				<input type="number" class="input-lg" name="meteo_altitude_ft" id="meteo_altitude_ft" size="4" value="<?php print $GLOBALS["config"]["Meteo"]["altitude_ft"]; ?>" tabindex="120" />
			</div>
			<div class="form-group">
				<label for="meteo_altitude_unit">Unit&agrave; di misura predefinita:</label>
				<select name="meteo_altitude_unit" id="meteo_altitude_unit" style="width: 100px;" tabindex="125">
					<option value="mt"<?php print (($GLOBALS["config"]["Meteo"]["default_altitude_unit"] == "mt") ? "selected" : ""); ?>>metri</option>
					<option value="ft"<?php print (($GLOBALS["config"]["Meteo"]["default_altitude_unit"] == "ft") ? "selected" : ""); ?>>piedi</option>
				</select>
			</div>
		</div>
	</div>
	<hr />
	<button class="btn btn-primary right" id="save_settings_btn" tabindex="130">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>