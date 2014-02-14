<link rel="stylesheet" href="common/js/chosen/chosen.css" />
<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/multiselect/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="common/js/multiselect/css/bootstrap-multiselect.css" type="text/css"/>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script language="Javascript" src="common/js/GnuPG/sha1.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/base64.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/PGpubkey.js" type="text/javascript"></script>
<script type="text/javascript" src="common/js/include/get_shares.js"></script>
<script type="text/javascript" src="common/js/include/setup.js"></script>
<div>
	<div id="setup_loader"><h1></h1><span></span></div>
	<div id="abstract">
		<div class="panel-heading">
			<h1>Benvenuti in Ninuxoo!<small class="help-block">Il motore di ricerca della community Ninux.org</small></h1>
		</div>
		<div class="panel-body">
			<p class="lead">Ninuxoo &egrave; un motore di ricerca decentralizzato per tutti i files condivisi nella Rete Comunitaria Ninux.</p>
		</div>
		<div class="panel-heading">
			<h1>Un nuovo modo di pensare alla Rete Locale</h1>
		<hr />
		</div>
		<div class="panel-body">
			<div class="col-xs-12 col-sm-6 col-lg-3">
				<div class="box">							
					<div class="icon">
						<a href="javascript:void(0);" class="image bg-danger danger"><span class="fa fa-magic"></span></a>
						<div class="description">
							<h3 class="title text-danger">Decentralizzato</h3>
							<p>Una rete <i>trusted</i> di dispositivi collegati fra loro: utenze, dati e ricerche sincronizzati</p>
						</div>
					</div>
					<div class="space"></div>
				</div> 
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-3">
				<div class="box">							
					<div class="icon">
						<a href="javascript:void(0);" class="image bg-success success"><span class="fa fa-lock"></span></a>
						<div class="description">
							<h3 class="title text-success">Sicuro</h3>
							<p>Nessun dato visibile, anzi cifrato!<br />Puoi collegare il tuo dispositivo ad altri solo con autorizzazione GPG... sei tu a decidere</p>
						</div>
					</div>
					<div class="space"></div>
				</div> 
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-3">
				<div class="box">							
					<div class="icon">
						<a href="javascript:void(0);" class="image bg-info info"><span class="fa fa-rocket"></span></a>
						<div class="description">
							<h3 class="title text-info">Semplice</h3>
							<p>Nessun macchinismo inutile: facile da usare anche per i non smanettoni</p>
						</div>
					</div>
					<div class="space"></div>
				</div> 
			</div>
			<div class="col-xs-12 col-sm-6 col-lg-3">
				<div class="box">							
					<div class="icon">
						<a href="javascript:void(0);" id="show_form" <?php print $btn_next_disabled; ?> tabindex="1"class="image bg-primary"><span class="fa fa-chevron-right fa-bounce-o"></span>
							<div class="description" style="white-space: nowrap;">
								<h3 class="title">Installa <i class="fa fa-angle-right"></i></h3>
								<p></p>
							</div>
						</a>
					</div>
					<div class="space"></div>
				</div> 
			</div>
			<span id="alert_no_internet" class="error"></span>
		</div>
	</div>
	
	<form method="post" action="" class="frm" id="install_frm" style="display: none;" onsubmit="install()">
		<div class="panel panel-default">
			<div class="panel-heading">
				<span class="lead text-primary">
				<span class="fa fa-sign-in"></span>&nbsp;&nbsp;Dati dell'amministratore <a name="Dati_admin" id="Dati_admin"></a>
					<small class="help-block">Impostazioni primarie di funzionamento</small>
				</span>
			</div>
			<div class="panel-body">
				<p><b>Nota</b>: Una volta incollata la chiave verranno immediatamente visualizzati alcuni dati contenuti nella chiave stessa. Tali dati non verranno salvati e servono unicamente per avere conferma visiva che la chiave fornita sia quella giusta.</p>
				
				<span class="form-group">
					<span class="">
						<label for="pgp_pubkey" class="required">Chiave PGP <u>pubblica</u> in formato ASCII Armored:</label>
						<textarea name="pgp_pubkey" id="pgp_pubkey" class="form-control" rows="7" autofocus tabindex="1"></textarea>
						<p><small><a style="display: none;" id="pgp_remove_key" href="javascript:void(0);" onclick="$('#pgp_pubkey').val('');getkey();" title="Cancella il testo inserito">Cancella il testo inserito</a>&nbsp;</small></p>
					</span>
				</span>
			</div>
			<div class="panel-footer" id="pgp_key_results"></div>
			<div class="panel-body">
				<span class="form-group col-lg-12">
					<label for="user_username" class="required">Indirizzo e-mail:</label>
					<input id="user_username" name="user_username" type="email" value="" autocomplete="off" tabindex="2" />
				</span>
				<span class="form-group col-lg-2">
					<label for="user_password" class="required">Password:</label>
					<input type="password" id="user_password" name="user_password" value="" autocomplete="off" tabindex="3" />
				</span>
				<span class="form-group col-lg-2">
					<label for="user_password2" class="required">Ripeti la password:</label>
					<input type="password" id="user_password2" name="user_password2" value="" autocomplete="off" tabindex="4" />
				</span>
			</div>
		</div>
		<br />
		<div class="panel panel-default">
			<div class="panel-heading">
				<span class="lead text-primary">
					<span class="fa fa-map-marker"></span>&nbsp;&nbsp;Nodo Ninux
					<small class="help-block">Dati relativi al nodo Ninux in cui si opera</small>
				</span>
			</div>
			<div class="panel-body">
				<span class="form-group">
					<label for="node_name" class="required">Nome del nodo di riferimento:</label>
					<select name="node_name" id="node_name" disabled style="width: 350px;" tabindex="5"><option value=""></option></select><span id="nlloader" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;<img src="common/media/img/loader.gif" width="16" /></span>
				</span>
				<span class="form-group">
					<label for="node_map">Indirizzo sul <a href="http://map.ninux.org/" target="_blank">MapServer</a>:</label>
					<input type="url" name="node_map" id="node_map" style="width: 50%;" autofocus value="" disabled placeholder="http://map.ninux.org/select/..." tabindex="6" />
				</span>
				<span class="form-group">
					<div id="selction-ajax"></div>
					<label for="node_type">Tipo di nodo:</label>
					<select name="node_type" id="node_type" style="width: 200px;" disabled data-placeholder="Tipo di nodo..." tabindex="7">
						<option value=""></option>
						<option value="active">Attivo</option>
						<option value="hotspot">HotSpot</option>
					</select>
				</span>
			</div>
		</div>
		<br />
		<div class="panel panel-default">
			<div class="panel-heading">
				<span class="lead text-primary">
					<span class="fa fa-hdd-o"></span>&nbsp;&nbsp;<acronym title="Network Attached Storage">NAS</acronym>  <sup><a data-toggle="collapse" href="#nas_share_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="NAS" id="NAS"></a>
					<small class="help-block">Impostazioni relative alla risorsa locale</small>
				</span>
				<div id="nas_share_info" class="panel-body panel-collapse collapse">
					<p>
						La directory di default per le condivisioni &egrave; <tt>/mnt/NAS</tt>.<br />
						Se si vuole condividere un Hard Disk esterno, &egrave; necessario che sia montato in maniera permanente in questa risorsa.<br />
						&Egrave; comunque possibile stabilire un altro percorso a propria scelta.
					</p>
					<p><b>Nota</b>: &egrave; importante che la directory principale delle condivisioni sia fuori da <tt><?php print getcwd() . "/"; ?></tt> altrimenti sar&agrave; tutto raggiungibile in chiaro!</p>
				</div>
			</div>
			<div class="panel-body">
				<span class="form-group">
					<label for="nas_name" class="required">Nome di questo NAS:</label>
					<input type="text" name="nas_name" id="nas_name" style="width: 50%;" value="" tabindex="8" />
				</span>
				<span class="form-group">
					<label for="nas_description" class="required">Descrizione (titolo della pagina):</label>
					<input type="text" name="nas_description" id="nas_description" style="width: 90%;" value="" tabindex="9" />
				</span>
				</span>
				<span class="form-group">
					<label for="root_share_dir" class="required">Directory principale dei files in condivisione:</label>
					<div class="input-group col-lg-5">
						<input type="text" name="root_share_dir" id="root_share_dir" class="form-control" value="/mnt/NAS/" placeholder="/mnt/NAS/" tabindex="10" />
						<span class="input-group-btn">
							<button type="button" id="root_share_dir_refresh_btn" class="btn btn-default" title="Carica il contenuto di questa directory"><span class="fa fa-refresh"></span></button>
						</span>
					</div>
				</span>
				<span class="form-group">
					<label for="shared_paths" class="required">Directories che si desidera siano scansionate e condivise:</label>
					<select data-placeholder="Scegli una directory" name="shared_paths" id="shared_paths" multiple tabindex="13" style="width: 350px;">
						<option value=""></option>
					</select>
				</span>
				<span class="form-group">
					<button class="btn btn-warning right" id="show_nas_advanced_options">Avanzate&nbsp;&nbsp;&nbsp;<span class="fa fa-caret-down"></button>
				</span>
			</div>
			<div id="nas_advanced_options" style="display: none;">
				<div class="panel-heading advanced">
					<span class="lead text-primary">Impostazioni <acronym title="Network Attached Storage">NAS</acronym> avanzate</span>
				</div>
				<div class="panel-footer advanced">
					<label for="uri_address">Indirizzo <acronym title="Uniform Resource Identifier">URI</acronym>:</label>
					<input type="text" name="uri_address" id="uri_address" style="width: 50%;" value="<?php print (($_SERVER["HTTPS"]) ? "https//" : "http://") . $_SERVER["SERVER_ADDR"]; ?>" tabindex="10" />
					
					
					<label for="server_root">Directory root del Server:</label>
					<input type="text" name="server_root" id="server_root" style="width: 50%;" value="<?php print getcwd() . "/"; ?>" placeholder="/var/www/" tabindex="14" />
					
					<label for="api_dir">Directory per le API:</label>
					<input type="text" name="api_dir" id="api_dir" style="width: 50%;" value="<?php print getcwd(); ?>/API/" placeholder="/var/www/API/" tabindex="15" />
				</div>
			</div>
		</div>
		<br />
		<div class="panel panel-default">
			<div class="panel-heading">
				<span class="lead text-primary">
					<button type="button" title="Installa l'interfaccia Meteo" id="install_meteo" class="btn btn-default right" tabindex="16"><span class="fa fa-fw fa-check-square-o"></span></button>
					<input type="checkbox" name="install_meteo" id="install_meteo_checkbox" checked tabindex="16" style="display: none;" />
					<span class="fa fa-sun-o"></span>&nbsp;&nbsp;Stazione Meteo <sup><a data-toggle="collapse" href="#meteo_station_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="Stazione_meteo" id="Stazione_meteo"></a>
					<small class="help-block">Dati relativi alla risorsa locale</small>
				</span>
				<div id="meteo_station_info" class="panel-body panel-collapse collapse">
					<p>
						L'interfaccia Meteo non &egrave; necessaria ai fini del funzionamento di Ninuxoo, ma &egrave; molto utile come servizio locale, soprattutto alla Community per condurre studi relativi alla resistenza dei nodi rispetto al clima.<br />
						Essendo il nodo geolocalizzato, con una piccola Stazione Meteorologica &egrave; possibile segnalare le misurazioni in tempo reale nella tua zona.<br />
						Nessun problema se non si possiede il kit di sensori: &egrave; gi&agrave; tutto predisposto per prelevare i dati da <a target="_blank" href="http://openweathermap.org/">OpenWeatherMap</a>, diversamente potresti valutare l'acquisto di una <a href="http://www.netatmo.com/it-IT/" title="Vai al sito di NetAtmo">Netatmo</a> ;)
					</p>
					<p>
						&Egrave; possibile comunque scegliere di non installare questa feature: in tal caso il link al menu superiore verr&agrave; rimosso e la directory "<tt>/Meteo</tt>" sar&agrave; disabilitata.</br>
						Per consentire eventuali implementazioni future, i dati di configurazione calcolati automaticamente sono lasciati in salvataggio.<br />
					</p>
				</div>
				<input type="hidden" id="tmp_lat" value="" />
				<input type="hidden" id="tmp_lng" value="" />
			</div>
			<div class="panel-body">
				<span class="form-group">
					<label for="meteo_name" class="required">Nome della Stazione:</label>
					<input type="text" name="meteo_name" id="meteo_name" style="width: 50%;" value="" tabindex="17" />
				</span>
				<span class="form-group">
					<button class="btn btn-warning right" id="show_meteo_advanced_options">Avanzate&nbsp;&nbsp;&nbsp;<span class="fa fa-caret-down"></button>
				</span>
			</div>
			<div id="meteo_advanced_options" style="display: none;">
				<div class="panel-heading advanced">
					<span class="lead text-primary">Impostazioni Meteo avanzate <sup><a data-toggle="collapse" href="#advanced_meteo_station_info" class="text-muted"><span class="fa fa-info"></span></a></sup></span>
				</div>
				<div id="advanced_meteo_station_info" class="panel-body advanced panel-collapse collapse">
					<p>
						<b>Nota</b>: in meteorologia &egrave; importante identificare la posizione geografica delle misurazioni, ecco il perch&eacute; di tanta accuratezza nel posizionamento.<br />
						Per i pi&ugrave; attenti alla privacy &egrave; possibile <a href="javascript: void(0);" id="paranoid_mode">cancellare i dati relativi all'interfaccia Meteo</a> <span id="calculate_meteo_data_span" style="display: none;">(<a href="javascript: void(0);" id="calculate_meteo_data">ricalcola</a>)</span> ;)
					</p>
				</div>
				<div class="panel-footer advanced">
					<span class="form-group">
						<label for="meteo_city">Citt&agrave;:</label>
						<input type="text" name="meteo_city" id="meteo_city" style="width: 50%;" value="" tabindex="18" />
					</span>
					<span class="form-group">
						<label for="meteo_zone">Zona</label>
						<input type="text" name="meteo_zone" id="meteo_zone" style="width: 50%;" value="" tabindex="19" />
					</span>
					<span class="form-group">
						<label for="meteo_region">Regione:</label>
						<input type="text" name="meteo_region" id="meteo_region" style="width: 50%;" value="" tabindex="20" />
					</span>
					<span class="form-group">
						<label for="meteo_country">Paese:</label>
						<input type="text" name="meteo_country" id="meteo_country" style="width: 50%;" value="" tabindex="21" />
					</span>
				</div>
				<div class="panel-heading advanced">
					<span class="lead text-primary">Altri dati geografici</span>
				</div>
				<div class="panel-footer advanced">
					<span class="form-group col-lg-12">
						<label for="meteo_owid">OpenWeather ID:</label>
						<input type="text" name="meteo_owid" id="meteo_owid" value="" tabindex="22" />
					</span>
					<span class="form-group col-lg-2">
						<label for="meteo_lat">Latitudine:</label>
						<input type="number" name="meteo_lat" id="meteo_lat" value="" tabindex="23" />
					</span>
					<span class="form-group col-lg-10">
						<label for="meteo_lng">Longitudine:</label>
						<input type="number" name="meteo_lng" id="meteo_lng" value="" tabindex="24" />
					</span>
					<span class="form-group col-lg-1">
						<label for="meteo_altitude_mt">metri:</label>
						<input type="number" name="meteo_altitude_mt" id="meteo_altitude_mt" size="2" value="" tabindex="25" />
					</span>
					<span class="form-group col-lg-11">
						<label for="meteo_altitude_ft">piedi:</label>
						<input type="number" name="meteo_altitude_ft" id="meteo_altitude_ft" size="4" value="" tabindex="26" />
					</span>
					<br />
					<span class="form-group">
						<label for="meteo_altitude_unit">Unit&agrave; di misura predefinita:</label>
						<select name="meteo_altitude_unit" id="meteo_altitude_unit" style="width: 100px;" tabindex="27">
							<option value=""></option>
							<option value="mt" selected>metri</option>
							<option value="ft">piedi</option>
						</select>
					</span>
				</div>
				<hr />
				<div class="panel-heading advanced">
					<span class="lead text-primary">
						<button type="button" title="Imposta la connessione al database" id="install_database" class="btn btn-default right" tabindex="16"><span class="fa fa-fw fa-square-o"></span></button>
						<input type="checkbox" name="install_database" id="install_database_checkbox" tabindex="16" style="display: none;" />
						<span class="icon-database"></span>&nbsp;&nbsp;Database <sup><a data-toggle="collapse" href="#database_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="Stazione_meteo" id="Stazione_meteo"></a>
						<small class="help-block">Dati necessari alla connessione al database</small>
					</span>
				</div>
				<div id="database_info" class="panel-body advanced panel-collapse collapse">
					<p>
						Se si dispone fisicamente di una Stazione Meteorologica bisogna fare in modo che Ninuxoo possa leggere i dati salvati dalla Stazione su un database.<br />
						Inserisci qui di seguito i dati relativi alla connessione al database.<br />
						<br />
						<strong>Nota</strong>: per motivi di sicurezza, i dati per la connessione al database saranno salvati su un file di configurazione separato da quello generale.<br />
						In caso di difficolt&agrave; ad acquisire i dati dalla propria Stazione, con poche risorse economiche &egrave; possibile valutare la possibilit&agrave; di utilizzare dei dispositivi <acronym title="Advanced RISC Machine">ARM</acronym> adattati allo scopo.
					</p>
				</div>
				<div class="panel-footer advanced">
					<span class="form-group">
						<label for="db_type">Tipo di database:</label>
						<select name="db_type" id="db_type" disabled style="width: 200px;" tabindex="28">
							<option value="mysql" selected>MySQL</option>
							<option value="sqlite">SQLite</option>
							<option value="postgresql">PostgreSQL</option>
						</select>
					</span>
					<span class="form-group">
						<label for="mysql_host">Host:</label>
						<input type="text" name="mysql_host" id="mysql_host" disabled value="localhost" tabindex="29" />
					</span>
					<span class="form-group">
						<label for="mysql_username">Username:</label>
						<input type="text" name="mysql_username" id="mysql_username" disabled value="" tabindex="30" />
					</span>
					<span class="form-group">
						<label for="mysql_password">Password:</label>
						<input type="password" name="mysql_password" id="mysql_password" disabled value="" tabindex="31" />
					</span>
					<span class="form-group">
						<label for="mysql_db_name">Nome del database:</label>
						<input type="text" name="mysql_db_name" id="mysql_db_name" disabled value="" tabindex="32" />
					</span>
					<span class="form-group">
						<label for="mysql_db_table">Nome della tabella:</label>
						<input type="text" name="mysql_db_table" id="mysql_db_table" disabled value="Meteo" tabindex="33" />
					</span>
				
				</div>
			</div>
		</div>
		<hr />
		<button class="btn btn-primary right" id="install" <?php print $btn_next_disabled; ?> tabindex="34">Installa&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
	</form>
</div>
