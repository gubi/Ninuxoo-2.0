<?php
require_once("common/include/classes/rsa.class.php");

$rsa = new rsa();
$pubkey = file_get_contents("common/include/conf/rsa_2048_pub.pem");
$token = $rsa->get_token($pubkey);
$crypted = base64_encode($rsa->simple_private_encrypt($token));
?>
<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/search_settings.js"></script>
<h1>Impostazioni di ricerca</h1>
<br />
<form method="post" action="" id="search_settings_frm" role="form" onsubmit="return false;">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-hdd"></span>&nbsp;&nbsp;Scansioni locali <a name="Scansioni_locali" id="Scansioni_locali"></a><small class="help-block">Report delle scansioni dei files</small></span></div>
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
	<a id="start_scan_btn" class="btn btn-warning right" href="javascript:void(0);" tabindex="1">Scansione manuale&nbsp;&nbsp;&nbsp;<span class="fa fa-refresh"></span></a>
	<br />
	<br />
	<br />
	<div class="panel panel-default clearfix">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-search"></span>&nbsp;&nbsp;Ricerche <a name="Ricerche" id="Ricerche"></a><small class="help-block">Impostazioni relative ai risultati delle ricerche</small></span></div>
		
		<div class="panel-body">
			Durante le ricerche tutti i NAS collegati sono coinvolti.<br />
			Per impostazione predefinita i NAS non riportano mai il proprio indirizzo IP, di conseguenza  non &egrave; possibile sapere dove risiedono i files dei risultati.<br />
			&Egrave; tuttavia possibile stabilire se <u>questo NAS</u> mostrer&agrave; in chiaro il proprio indirizzo IP.<br />
			<br />
			<b>IMPORTANTE</b>: questa opzione &egrave; disponibile solo per risolvere particolari esigenze.<br />
			Se non si &egrave; veramente sicuri di ci&ograve; che si fa e si vuole rimanere in sicurezza, lasciare il campo <strong>NON</strong> spuntato.
		</div>
		<div class="panel-body">
			<div class="checkbox">
				<label class="text-danger">
					<input type="checkbox" name="show_ip" id="show_ip" <?php print ($settings["searches"]["show_ip"] == "true") ? "checked" : ""; ?> tabindex="2" />
					Consenti l'indirizzamento diretto (questo mostrer&agrave; il tuo indirizzo IP)&nbsp;&nbsp;<span class="glyphicon glyphicon-warning-sign"></span>
				</label>
			</div>
		</div>
		<hr />
		<div class="panel-body">
			<div class="form-group">
				<div class="checkbox">
					<label>
						<input type="checkbox" name="allow_advanced_research" id="allow_advanced_research" <?php print ($settings["searches"]["allow_advanced_research"] == "true") ? "checked" : ""; ?> tabindex="2" />
						Consenti le ricerche avanzate
					</label>
				</div>
			</div>
			<div class="form-group">
				<label for="research_type">Tipo di ricerca predefinita: </label>
				<select name="research_type" id="research_type" tabindex="3">
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
				<input type="number" class="input-lg" name="research_results" id="research_results" value="<?php print $settings["searches"]["research_results"]; ?>" size="2" maxlength="3" tabindex="4" />
			</div>
		</div>
	</div>
	<button class="btn btn-primary right" id="save_search_params_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></button>
</form>