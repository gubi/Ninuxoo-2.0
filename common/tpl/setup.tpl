<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script language="Javascript" src="common/js/GnuPG/sha1.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/base64.js" type="text/javascript"></script>
<script language="Javascript" src="common/js/GnuPG/PGpubkey.js" type="text/javascript"></script>
<script type="text/javascript" src="common/js/include/setup.js"></script>
<script type="text/javascript">
	$(function () {
		'use strict';
		$.fn.show_connection_error = function() {
			if(this.is("input") && this.attr("type") != "checkbox"){
				this.addClass("error").after('<span class="error no_connection">&nbsp;&nbsp;&nbsp;Connessione ad internet assente...</span>');
			}
			this.attr("disabled", "disabled");
			if(this.is("select")){
				this.trigger("liszt:updated");
			}
		};
		$.fn.hide_connection_error = function() {
			this.attr("disabled", false);
			if(this.is("select")){
				this.trigger("liszt:updated");
			}
			if(this.hasClass("error")){
				this.removeClass("error");
			}
		};
		$("#pgp_pubkey").val("");
		$("#node_name").attr("data-placeholder", "Caricamento della lista dei nodi dal MapServer...");
		$("#node_map").val("");
		$("#node_type").val("");
		$("#nas_name").val("");
		$("#nas_description").val("");
		$("#smb_conf_paths").val("");
		$("#meteo_name").val("");
		$("#meteo_city").val("");
		$("#meteo_region").val("");
		$("#meteo_country").val("");
		$("#meteo_lat").val("");
		$("#meteo_lng").val("");
		$("#meteo_owid").val("");
		$("#meteo_altitude_mt").val("");
		$("#meteo_altitude_ft").val("");
		$("#node_name").chosen({
			disable_search_threshold: 5,
			no_results_text: "Nessun nodo rilevato per",
			allow_single_deselect: true
		});
		$("#node_type, #meteo_altitude_unit").chosen();
		check_internet();
		
		$("#nlloader").show();
		get_samba($("#root_share_dir").val());
		$("#root_share_dir").change(function() {
			get_samba($(this).val());
		});
		var title = $("title").text();
		$("#nas_name").keyup(function() {
			if($(this).val().length > 0) {
				$(this).val(ucfirst($(this).val()));
				$("#header h1").text("Setup (" + $(this).val() + ")");
				$("title").text(title + " (" + $(this).val() + ")");
				$("#nas_description").val("NAS Rete Comunitaria Ninux - " + $(this).val());
			} else {
				$("#header h1").text("Setup");
				$("title").text(title);
				$("#nas_description").val("");
			}
		});
		$("#pgp_pubkey").on('keyup change', function(e){
			getkey();
		});
		$("#user_password2").change(function() {
			if($("#user_password").length == 0 || $("#user_password2").length == 0 || $("#user_password").val() != $("#user_password2").val()) {
				$("#user_password").attr("class", "error");
				$("#user_password2").attr("class", "error");
				apprise("Le password non sono identiche", function(r) {
					$("#user_password").focus();
				});
			} else {
				$("#user_password").toggleClass("error", "", 300);
				$("#user_password2").toggleClass("error", "", 300);
			}
		});
		$("#meteo_city").change(function() {
			$.get("common/include/funcs/_ajax/read_json.php?uri=http://openweathermap.org/data/2.1/find/name?q=" + $(this).val(), function(data) {
				$("#meteo_owid").val(data.list[0].id);
			}, "json");
		});
		$("#install").click(function() {
			install();
			return false;
		});
		$("#install_meteo").change(function() {
			if($(this).is(":checked")) {
				$("#meteo_name").attr("disabled", false).focus();
				$("#meteo_city").attr("disabled", false);
				$("#meteo_region").attr("disabled", false);
				$("#meteo_country").attr("disabled", false);
				$("#meteo_lat").attr("disabled", false);
				$("#meteo_lng").attr("disabled", false);
				$("#meteo_owid").attr("disabled", false);
				$("#meteo_altitude_mt").attr("disabled", false);
				$("#meteo_altitude_ft").attr("disabled", false);
				$("#meteo_altitude_unit").attr("disabled", false).trigger("liszt:updated");
			} else {
				$("#meteo_name").attr("disabled", true);
				$("#meteo_city").attr("disabled", true);
				$("#meteo_region").attr("disabled", true);
				$("#meteo_country").attr("disabled", true);
				$("#meteo_lat").attr("disabled", true);
				$("#meteo_lng").attr("disabled", true);
				$("#meteo_owid").attr("disabled", true);
				$("#meteo_altitude_mt").attr("disabled", true);
				$("#meteo_altitude_ft").attr("disabled", true);
				$("#meteo_altitude_unit").attr("disabled", true).trigger("liszt:updated");
			}
		});
		$("#paranoid_mode").click(function() {
			if($("#node_name").val().length > 0) {
				$("#calculate_meteo_data_span").show();
				$("#meteo_name").attr("disabled", true).val("");
				$("#meteo_city").attr("disabled", true).val("");
				$("#meteo_region").attr("disabled", true).val("");
				$("#meteo_country").attr("disabled", true).val("");
				$("#meteo_lat").attr("disabled", true).val("");
				$("#meteo_lng").attr("disabled", true).val("");
				$("#meteo_owid").attr("disabled", true).val("");
				$("#meteo_altitude_mt").attr("disabled", true).val("");
				$("#meteo_altitude_ft").attr("disabled", true).val("");
				$("#meteo_altitude_unit").attr("disabled", true).trigger("liszt:updated");
				$("#install_meteo").attr("checked", false);
				
				
				$("#calculate_meteo_data").click(function() {
					$("#meteo_name").attr("disabled", false).val("Meteo " + $("#node_name").val());
					
					calculate_meteo_data($("#tmp_lat").val(), $("#tmp_lng").val());
					$("#install_meteo").attr("checked", true);
				});
			} else {
				$("#node_name").mousedown();
			}
		});
		$("#show_form").click(function() {
			$(this).hide();
			$("html, body").animate({ scrollTop: ($("hr").first().offset().top) }, 300);
			$("form.frm").slideDown(300, function() {
				$("body").prepend('<div id="form_loaded" style="display: none;">true</div>');
				$("#node_name").attr("disabled", false);
				$("#pgp_pubkey").focus();
				/*
				if($("#node_name > option").length > 1) {
					$("#node_name_chzn > .chzn-single").mousedown();
				}
				*/
			});
			return false;
		});
		$("#show_nas_advanced_options").click(function() {
			$(this).hide();
			$("#nas_advanced_options").slideDown(300);
			return false;
		});
		$("#show_meteo_advanced_options").click(function() {
			$(this).hide();
			$("#meteo_advanced_options").slideDown(300);
			return false;
		});
	});
</script>
<div>
	<div id="setup_loader"><h1></h1><span></span></div>
	<div id="abstract">
		<img src="common/media/img/ninuxoo_claim.png" class="left" />
		<h1>Benvenuti in Ninuxoo!</h1>
		<p>Ninuxoo &egrave; un motore di indicizzazione decentralizzato dei files presenti sui NAS nella Rete Comunitaria Ninux.</p>
		<p>
			Compilando il modulo a seguire si installer&agrave; una versione locale, beneficiando cos&igrave; di un Motore di Ricerca personale e aiutando la Rete a moltiplicare i punti di accesso al servizio.<br />
			Il processo &egrave; abbastanza automatizzato, il ch&eacute; consente un'installazione rapida con pochi moduli da dover compilare.<br />
			Non &egrave; richiesto nessun tipo di database, ma potrebbe essere necessario collegarne uno nel caso in cui si disponga fisicamente di una Stazione Meteorologica.
		</p>
		<p>Per maggiori informazioni, &egrave; possibile consultare:</p>
		<ul>
			<li><a href="http://wiki.ninux.org/Ninuxoo" target="_blank">Ninuxoo sul wiki ufficiale</a></li>
			<li><a href="https://github.com/gubi/Ninuxoo-Semantic-Decentralized/wiki" target="_blank">Documentazione sul repository ufficiale su Github</a></li>
			<li><a href="http://ml.ninux.org/mailman/listinfo/ninux-dev" target="_blank">Mailing List dedicata allo sviluppo (richiede iscrizione)</a></li>
		</ul>
	</div>
	<hr />
	<span id="alert_no_internet" class="error"></span>
	<button class="btn btn-primary right" id="show_form" <?php print $btn_next_disabled; ?> tabindex="34">Prosegui&nbsp;&nbsp;&nbsp;<span class="fa fa-angle-right"></button>
	
	<form method="post" action="" class="frm" id="install_frm" style="display: none;" onsubmit="install()">
		<fieldset>
			<legend>Dati per l'accesso</legend>
			<p>I dati a seguire consentiranno l'accesso al pannello di controllo, dal quale si potr&agrave;:</p>
			<ul>
				<li>Modificare i dati inseriti in questo modulo</li>
				<li>Impostare le preferenze di ricerca predefinite</li>
				<li>Avviare scansioni manualmente</li>
				<li>Gestire le connessioni ad altri Ninuxoo<br /><small><a target="_blank" href="https://github.com/gubi/Ninuxoo-Semantic-Decentralized/wiki/Tokens-e-processo-di-scambio-chiavi-tra-device">Informazioni sui meccanismi di connessione</a></small></li>
				<li>Gestire alcune funzionalit&agrave; dell'interfaccia Meteo</li>
				<li>Memorizzare le configurazioni dei device personali</li>
			</ul>
			<hr />
			<p><b>Nota</b>: Una volta incollata la chiave verranno immediatamente visualizzati alcuni dati contenuti nella chiave stessa. Tali dati non verranno salvati e servono unicamente per avere conferma visiva che la chiave fornita sia quella giusta.</p>
			
			<img class="left" src="common/media/img/gnupg_logo.png">
			<span class="left">
				<label for="pgp_pubkey" class="required">Chiave PGP <u>pubblica</u> in formato ASCII Armored:</label>
				<textarea name="pgp_pubkey" id="pgp_pubkey" rows="5" style="width: 100%; height: 150px;" autofocus tabindex="1"></textarea>
				<p><small><a style="display: none;" id="pgp_remove_key" href="javascript:void(0);" onclick="$('#pgp_pubkey').val('');getkey();" title="Cancella il testo inserito">Cancella il testo inserito</a>&nbsp;</small></p>
			</span>
			<br />
			<br />
			<label for="user_username" class="required">Username:</label>
			<input id="user_username" name="user_username" type="email" value="" autocomplete="off" tabindex="2" />
			<br />
			<br />
			<span class="left">
				<label for="user_password" class="required">Password:</label>
				<input type="password" id="user_password" name="user_password" value="" autocomplete="off" tabindex="3" />
			</span>
			<span class="left">
				<label for="user_password2" class="required">Ripeti la password:</label>
				<input type="password" id="user_password2" name="user_password2" value="" autocomplete="off" tabindex="4" />
			</span>
			<div id="pgp_key_results"></div>
		</fieldset>
		<fieldset>
			<legend>Nodo Ninux</legend>
			<label for="node_name" class="required">Nome del nodo di riferimento:</label>
			<select name="node_name" id="node_name" disabled style="width: 350px;" tabindex="5"><option value=""></option></select><span id="nlloader" style="display: none;">&nbsp;&nbsp;&nbsp;&nbsp;<img src="common/media/img/loader.gif" width="16" /></span>
			
			<label for="node_map">Indirizzo sul <a href="http://map.ninux.org/" target="_blank">MapServer</a>:</label>
			<input type="url" name="node_map" id="node_map" style="width: 50%;" autofocus value="" disabled placeholder="http://map.ninux.org/select/..." tabindex="6" />
			
			<div id="selction-ajax"></div>
			<label for="node_type">Tipo di nodo:</label>
			<select name="node_type" id="node_type" style="width: 200px;" disabled data-placeholder="Tipo di nodo..." tabindex="7">
				<option value=""></option>
				<option value="active">Attivo</option>
				<option value="hotspot">HotSpot</option>
			</select>
		</fieldset>
		<fieldset>
			<legend><acronym title="Network Attached Storage">NAS</acronym></legend>
			
			<label for="nas_name" class="required">Nome di questo NAS:</label>
			<input type="text" name="nas_name" id="nas_name" style="width: 50%;" value="" tabindex="8" />
			
			<label for="nas_description" class="required">Descrizione (titolo della pagina):</label>
			<input type="text" name="nas_description" id="nas_description" style="width: 90%;" value="" tabindex="9" />
			<br />
			<br />
			
			<p>La directory di default per le condivisioni &egrave; <tt>/mnt/NAS</tt>.<br />
			Se si vuole condividere un Hard Disk esterno, &egrave; necessario che sia montato in maniera permanente in questo NAS.<br />
			Ricaricare quindi la pagina per poter operare sulle nuove modifiche.<br />
			&Egrave; comunque possibile stabilire un altro percorso a propria scelta.</p>
			<p><b>Nota</b>: &egrave; importante che la directory principale delle condivisioni sia fuori da <tt><?php print getcwd() . "/"; ?></tt> altrimenti sar&agrave; tutto raggiungibile in chiaro!</p>
			<label for="root_share_dir" class="required">Directory principale dei files in condivisione:</label>
			<input type="text" name="root_share_dir" id="root_share_dir" style="width: 25%;" value="/mnt/NAS/" placeholder="/mnt/NAS/" tabindex="12" />
			
			<label for="shared_paths" class="required">Directories che si desidera siano scansionate e condivise:</label>
			<textarea name="shared_paths" id="shared_paths" rows="5" style="width: 50%;" disabled tabindex="13"></textarea>
			<hr />
			<button class="btn btn-default right" id="show_nas_advanced_options">Avanzate&nbsp;&nbsp;&nbsp;<span class="fa fa-caret-down"></button>
			<div class="advanced" id="nas_advanced_options" style="display: none;">
				<h3>Impostazioni <acronym title="Network Attached Storage">NAS</acronym> avanzate</h3>
				<label for="uri_address">Indirizzo <acronym title="Uniform Resource Identifier">URI</acronym>:</label>
				<input type="text" name="uri_address" id="uri_address" style="width: 50%;" value="<?php print (($_SERVER["HTTPS"]) ? "https//" : "http://") . $_SERVER["SERVER_ADDR"]; ?>" tabindex="10" />
				
				
				<label for="server_root">Directory root del Server:</label>
				<input type="text" name="server_root" id="server_root" style="width: 50%;" value="<?php print getcwd() . "/"; ?>" placeholder="/var/www/" tabindex="14" />
				
				<label for="api_dir">Directory per le API:</label>
				<input type="text" name="api_dir" id="api_dir" style="width: 50%;" value="<?php print getcwd(); ?>/API/" placeholder="/var/www/API/" tabindex="15" />
			</div>
		</fieldset>
		<fieldset>
			<legend>Stazione Meteo</legend>
			<p>
				L'interfaccia Meteo non &egrave; necessaria ai fini del funzionamento di Ninuxoo, ma &egrave; molto utile come servizio.<br />
				Essendo il nodo geolocalizzato, con una piccola Stazione Meteorologica &egrave; possibile segnalare le misurazioni in tempo reale e, volendo, anche per placare i vicini fastidiosi perch&eacute; "<i>si segnala il tempo al <a href="http://www.meteonetwork.it/" target="_blank">Servizio Meteorologico Nazionale</a></i>"<br />
				Nessun problema se non si possiede il kit di sensori: &egrave; gi&agrave; tutto predisposto per prelevare i dati da <a target="_blank" href="http://openweathermap.org/">OpenWeatherMap</a> e incentivare a coltivare tale passione...
			</p>
			<p>
				&Egrave; possibile comunque scegliere di non installare questa feature: in tal caso il link al menu superiore verr&agrave; rimosso e la directory "<tt>/Meteo</tt>" sar&agrave; disabilitata.</br>
				Per consentire eventuali implementazioni future, i dati di configurazione calcolati automaticamente sono lasciati in salvataggio.<br />
			</p>
			<input type="hidden" id="tmp_lat" value="" />
			<input type="hidden" id="tmp_lng" value="" />
			<label><input type="checkbox" name="install_meteo" id="install_meteo" checked tabindex="16" /> Installa l'interfaccia Meteo</label>
			<br />
			<label for="meteo_name" class="required">Nome della Stazione:</label>
			<input type="text" name="meteo_name" id="meteo_name" style="width: 50%;" value="" tabindex="17" />
			<hr />
			<button class="btn btn-default right" id="show_meteo_advanced_options">Avanzate...&nbsp;&nbsp;&nbsp;<span class="fa fa-caret-down"></button>
			<div class="advanced" id="meteo_advanced_options" style="display: none;">
				<h3>Impostazioni Meteo avanzate</h3>
				<p>
					<b>Nota</b>: in meteorologia &egrave; importante identificare la posizione geografica delle misurazioni, ecco il perch&eacute; di tanta accuratezza nel posizionamento.<br />
					Per i pi&ugrave; attenti alla privacy &egrave; possibile <a href="javascript: void(0);" id="paranoid_mode">cancellare i dati relativi all'interfaccia Meteo</a> <span id="calculate_meteo_data_span" style="display: none;">(<a href="javascript: void(0);" id="calculate_meteo_data">ricalcola</a>)</span> ;)
				</p>
				<label for="meteo_city">Citt&agrave;:</label>
				<input type="text" name="meteo_city" id="meteo_city" style="width: 50%;" value="" tabindex="18" />
				
				<label for="meteo_zone">Zona</label>
				<input type="text" name="meteo_zone" id="meteo_zone" style="width: 50%;" value="" tabindex="19" />
				
				<label for="meteo_region">Regione:</label>
				<input type="text" name="meteo_region" id="meteo_region" style="width: 50%;" value="" tabindex="20" />
				
				<label for="meteo_country">Paese:</label>
				<input type="text" name="meteo_country" id="meteo_country" style="width: 50%;" value="" tabindex="21" />
				
				<br />
				
				<label for="meteo_owid">OpenWeather ID:</label>
				<input type="text" name="meteo_owid" id="meteo_owid" value="" tabindex="22" />
				
				<br />
				<br />
				<span class="left">
					<label for="meteo_lat">Latitudine:</label>
					<input type="number" name="meteo_lat" id="meteo_lat" value="" tabindex="23" />
				</span>
				<span class="left">
					<label for="meteo_lng">Longitudine:</label>
					<input type="number" name="meteo_lng" id="meteo_lng" value="" tabindex="24" />
				</span>
				<br />
				<label>Altitudine:</label>
				<span class="left">
					<label for="meteo_altitude_mt">metri:</label>
					<input type="number" name="meteo_altitude_mt" id="meteo_altitude_mt" size="2" value="" tabindex="25" />
				</span>
				<span class="left">
					<label for="meteo_altitude_ft">piedi:</label>
					<input type="number" name="meteo_altitude_ft" id="meteo_altitude_ft" size="4" value="" tabindex="26" />
				</span>
				<br />
				<span class="left">
					<label for="meteo_altitude_unit">Unit&agrave; di misura predefinita:</label>
					<select name="meteo_altitude_unit" id="meteo_altitude_unit" style="width: 100px;" tabindex="27">
						<option value=""></option>
						<option value="mt" selected>metri</option>
						<option value="ft">piedi</option>
					</select>
				</span>
				<hr />
				<h3>Database MySQL</h3>
				<p>
					Se si dispone fisicamente di una Stazione Meteorologica bisogna fare in modo che Ninuxoo possa leggere i dati salvati dalla Stazione su un database MySQL.<br />
					Inserire qui di seguito i dati relativi alla connessione al database.<br />
					<br />
					<strong>Nota</strong>: per motivi di sicurezza, i dati per la connessione al database saranno salvati su un file di configurazione separato da quello generale.<br />
					In caso di difficolt&agrave; ad acquisire i dati dalla propria Stazione, con poche risorse economiche &egrave; possibile valutare la possibilit&agrave; di utilizzare dei dispositivi <acronym title="Advanced RISC Machine">ARM</acronym> adattati allo scopo.
				</p>
				<br />
				<span class="left">
					<label for="db_type">Tipo di database:</label>
					<select name="db_type" id="db_type" style="width: 100px;" tabindex="28">
						<option value="mysql" selected>MySQL</option>
						<option value="sqlite">SQLite</option>
						<option value="postgresql">PostgreSQL</option>
					</select>
				</span>
				<br />
				<br />
				<label for="mysql_host">Host:</label>
				<input type="text" name="mysql_host" id="mysql_host" value="localhost" tabindex="29" />
				<br />
				<br />
				<span class="left">
					<label for="mysql_username">Username:</label>
					<input type="text" name="mysql_username" id="mysql_username" value="" tabindex="30" />
				</span>
				<span class="left">
					<label for="mysql_password">Password:</label>
					<input type="password" name="mysql_password" id="mysql_password" value="" tabindex="31" />
				</span>
				<br />
				<br />
				<span class="left">
					<label for="mysql_db_name">Nome del database:</label>
					<input type="text" name="mysql_db_name" id="mysql_db_name" value="" tabindex="32" />
				</span>
				<span class="left">
					<label for="mysql_db_table">Nome della tabella:</label>
					<input type="text" name="mysql_db_table" id="mysql_db_table" value="Meteo" tabindex="33" />
				</span>
			</div>
		</fieldset>
		<hr />
		<button class="btn btn-primary right" id="install" <?php print $btn_next_disabled; ?> tabindex="34">Installa&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
	</form>
</div>