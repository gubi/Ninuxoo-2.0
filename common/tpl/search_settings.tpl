<?php
require_once("common/include/classes/rsa.class.php");

$rsa = new rsa();
$pubkey = file_get_contents("common/include/conf/rsa_2048_pub.pem");
$token = $rsa->get_token($pubkey);
$crypted = base64_encode($rsa->simple_private_encrypt($token));
?>
<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/multiselect/js/bootstrap-multiselect.js"></script>
<link rel="stylesheet" href="common/js/multiselect/css/bootstrap-multiselect.css" type="text/css"/>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/get_shares.js"></script>
<script type="text/javascript" src="common/js/include/search_settings.js"></script>
<h1>Impostazioni di ricerca</h1>
<br />
<form method="post" action="" id="search_settings_frm" role="form" onsubmit="return false;">
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary"><span class="glyphicon glyphicon-hdd"></span>&nbsp;&nbsp;Scansioni locali <a name="Scansioni_locali" id="Scansioni_locali"></a><small class="help-block">Report delle scansioni dei files</small></span>
		</div>
		<?php
		$config = parse_ini_file("common/include/conf/config.ini", true);
		$settings = parse_ini_file("common/include/conf/general_settings.ini", true);
		?>
		<table class="table">
			<thead>
				<tr><th>Data ultima scansione:</th><th>Durata della scansione:</th><th>Elementi scansionati:</th></tr>
			</thead>
			<tbody>
				<tr>
					<td id="last_scan_date"><?php print $config["NAS"]["last_scan_date"]; ?></td>
					<td id="last_scanning_time"><?php print $config["NAS"]["last_scanning_time"]; ?></td>
					<td id="last_items_count"><?php print $config["NAS"]["last_items_count"]; ?></td>
				</tr>
			</tbody>
		</table>
	</div>
	<input type="hidden" value="<?php print $crypted; ?>" id="token" />
	<a id="start_scan_btn" class="btn btn-grey right" href="javascript:void(0);" tabindex="1">Scansione manuale&nbsp;&nbsp;&nbsp;<span class="fa fa-refresh"></span></a>
	<br />
	<br />
	<br />
	
	<div class="panel panel-default">
		<div class="panel-heading">
			<span class="lead text-primary">
				<span class="fa fa-hdd-o"></span>&nbsp;&nbsp;<acronym title="Network Attached Storage">NAS</acronym> <sup><a data-toggle="collapse" href="#nas_share_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="NAS" id="NAS"></a>
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
			<div class="form-group">
				<label for="nas_name" class="required">Nome di questo NAS:</label>&nbsp;
				<input type="text" name="nas_name" id="nas_name" class="input-lg" value="<?php print $config["NAS"]["nas_name"]; ?>" tabindex="5" />
			</div>
			<div class="form-group row">
				<span class="col-lg-8">
					<label for="nas_description" class="required">Descrizione (titolo della pagina):</label>
					<input type="text" name="nas_description" id="nas_description" class="form-control" value="<?php print $config["NAS"]["nas_description"]; ?>" tabindex="10" />
				</span>
			</div>
			<div class="form-group">
				<label for="root_share_dir" class="required">Directory principale dei files in condivisione:</label>
				<div class="input-group col-lg-5">
					<input type="text" name="root_share_dir" id="root_share_dir" class="form-control" value="<?php print $config["NAS"]["root_share_dir"]; ?>" placeholder="/mnt/NAS/" tabindex="15" />
					<span class="input-group-btn">
						<button type="button" id="root_share_dir_refresh_btn" class="btn btn-default" title="Carica il contenuto di questa directory"><span class="glyphicon glyphicon-repeat"></span></button>
					</span>
				</div>
			</div>
			<div class="form-group row">
				<span class="col-lg-4">
					<label for="shared_paths" class="required">Directories che si desidera siano scansionate e condivise:</label>
				</span>
				<span class="col-lg-12">
					<span id="selected_shared_dirs" style="display: none;"><?php print implode(",", str_replace("/", "", explode("\n", file_get_contents(str_replace("//", "/", $config["NAS"]["root_dir"] . "/") . "common/include/conf/scan_shares")))); ?></span>
					<select data-placeholder="Scegli una directory" name="shared_paths" id="shared_paths" multiple tabindex="20" style="width: 350px;"></select>
					
					<button class="btn btn-warning right" id="show_nas_advanced_options">Avanzate&nbsp;&nbsp;&nbsp;<span class="fa fa-caret-down"></button>
				</span>
			</div>
		</div>
		<div id="nas_advanced_options" style="display: none;">
			<div class="panel-heading advanced">
				<span class="lead text-primary"><span class="fa fa-wrench"></span>&nbsp;&nbsp;Impostazioni <acronym title="Network Attached Storage">NAS</acronym> avanzate</span>
			</div>
			<div class="panel-footer advanced">
				<div class="form-group">
					<label for="uri_address">Indirizzo <acronym title="Uniform Resource Identifier">URI</acronym>:</label>
					<input type="text" name="uri_address" id="uri_address" class="input-lg" value="<?php print (($_SERVER["HTTPS"]) ? "https//" : "http://") . $_SERVER["SERVER_ADDR"]; ?>" tabindex="25" />
				</div>
				<div class="form-group">
					<label for="server_root">Directory root del Server:</label>
					<input type="text" name="server_root" id="server_root" class="input-lg" value="<?php print getcwd() . "/"; ?>" placeholder="/var/www/" tabindex="30" />
				</div>
				<div class="form-group">
					<label for="api_dir">Directory per le API:</label>
					<input type="text" name="api_dir" id="api_dir" class="input-lg" value="<?php print getcwd(); ?>/API/" placeholder="/var/www/API/" tabindex="35" />
				</div>
			</div>
		</div>
	</div>
	<br />
	<div class="panel panel-default clearfix">
		<div class="panel-heading">
			<span class="lead text-primary">
				<span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;Ricerche <sup><a data-toggle="collapse" href="#research_info" class="text-muted"><span class="fa fa-info"></span></a></sup><a name="Ricerche" id="Ricerche"></a>
				<small class="help-block">Impostazioni relative ai risultati delle ricerche</small>
			</span>
			<div id="research_info" class="panel-body panel-collapse collapse">
				<p>
					Durante le ricerche tutti i NAS collegati sono coinvolti.<br />
					Per impostazione predefinita i NAS non riportano mai il proprio indirizzo IP, di conseguenza  non &egrave; possibile sapere dove risiedono i files dei risultati.<br />
					&Egrave; tuttavia possibile stabilire se <u>questo NAS</u> mostrer&agrave; in chiaro il proprio indirizzo IP.<br />
					<br />
					<b>IMPORTANTE</b>: questa opzione &egrave; disponibile solo per risolvere particolari esigenze.<br />
					Se non si &egrave; veramente sicuri di ci&ograve; che si fa e si vuole rimanere in sicurezza, lasciare il campo <strong>NON</strong> spuntato.
				</p>
			</div>
		</div>
		<div class="panel-body">
			<div class="checkbox">
				<label class="text-danger">
					<input type="checkbox" name="show_ip" id="show_ip" <?php print ($settings["searches"]["show_ip"] == "true") ? "checked" : ""; ?> tabindex="40" />
					Consenti l'indirizzamento diretto (questo mostrer&agrave; il tuo indirizzo IP)&nbsp;&nbsp;<span class="glyphicon glyphicon-warning-sign"></span>
				</label>
			</div>
		</div>
		<hr />
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="allow_advanced_research" id="allow_advanced_research" <?php print ($settings["searches"]["allow_advanced_research"] == "true") ? "checked" : ""; ?> tabindex="45" />
						Consenti le ricerche avanzate
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="research_type">Tipo di ricerca predefinita: </label>
				<select name="research_type" id="research_type" tabindex="50" style="width: 200px;">
					<?php
					$research_types = array("query" => "Tutti i risultati possibili", "exactquery" => "Per frase esatta", "orquery" => "Per singola parola", "likequery" => "Per parole simili", "whatsnew" => "Sull'ultima scansione");
					foreach($research_types as $value => $txt) {
						if($settings["searches"]["research_type"] == $value) {
							$checked = ' selected="selected"';
						} else {
							$checked = "";
						}
						print '<option value="' . $value . '"' . $checked . '>' . $txt . '</option>';
					}
					?>
				</select>
			</div>
			<div class="form-group">
				<label for="research_results">Risultati mostrati: </label>
				<input type="number" class="input-lg" name="research_results" id="research_results" value="<?php print $settings["searches"]["research_results"]; ?>" size="2" maxlength="3" tabindex="55" />
			</div>
		</div>
	</div>
	<button class="btn btn-primary right" id="save_search_params_btn" tabindex="60">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>