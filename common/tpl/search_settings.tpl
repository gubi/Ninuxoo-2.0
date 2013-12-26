<?php
require_once("common/include/classes/rsa.class.php");

$rsa = new rsa();
$pubkey = file_get_contents("common/include/conf/rsa_2048_pub.pem");
$token = $rsa->get_token($pubkey);
$crypted = base64_encode($rsa->simple_private_encrypt($token));
?>
<link href="common/js/chosen/chosen.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/search_settings.js"></script>
<h1>Impostazioni di ricerca</h1>
<br />
<form method="post" action="" id="search_settings_frm" onsubmit="return false;">
	<fieldset class="frm">
		<legend>Scansioni locali <a name="Scansioni_locali" id="Scansioni_locali"></a></legend>
		
		<table cellpadding="0" cellspacing="0" style="width: auto;">
			<?php
			$config = parse_ini_file("common/include/conf/config.ini", true);
			$settings = parse_ini_file("common/include/conf/general_settings.ini", true);
			?>
			<tr><td>Data ultima scansione:</td><td id="last_scan_date"><b><?php print $config["NAS"]["last_scan_date"]; ?></b></td></tr>
			<tr><td>Durata della scansione:</td><td id="last_scanning_time"><b><?php print $config["NAS"]["last_scanning_time"]; ?></b></td></tr>
			<tr><td>Elementi scansionati:</td><td id="last_items_count"><b><?php print $config["NAS"]["last_items_count"]; ?></b></td></tr>
		</table>
		<p>
			&rsaquo; <a id="start_scan_btn" href="javascript:void(0);" tabindex="1">Avvia una scansione manuale</a>
		</p>
	</fieldset>
	<fieldset class="frm">
		<legend>Ricerche <a name="Ricerche" id="Ricerche"></a></legend>
		
		<p>
			Durante le ricerche tutti i NAS collegati sono coinvolti.<br />
			Per impostazione predefinita i NAS non riportano mai il proprio indirizzo IP, di conseguenza  non &egrave; possibile sapere dove risiedono i files dei risultati.<br />
			&Egrave; tuttavia possibile stabilire se <u>questo NAS</u> mostrer&agrave; in chiaro il proprio indirizzo IP.<br />
			<br />
			<b>IMPORTANTE</b>: questa opzione &egrave; disponibile solo per risolvere particolari esigenze.<br />
			Se non si &egrave; veramente sicuri di ci&ograve; che si fa e si vuole rimanere in sicurezza, lasciare il campo NON spuntato.
		</p>
		<label><input type="checkbox" name="show_ip" id="show_ip" <?php print ($settings["searches"]["show_ip"] == "true") ? "checked" : ""; ?> tabindex="2" /> Consenti l'indirizzamento diretto (attenzione: mostra il tuo indirizzo IP)</label>
		<hr />
		<label><input type="checkbox" name="allow_advanced_research" id="allow_advanced_research" <?php print ($settings["searches"]["allow_advanced_research"] == "true") ? "checked" : ""; ?> tabindex="2" /> Consenti le ricerche avanzate</label>
		<br />
		<span class="left">
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
		</span>
		<br />
		<span class="left">
			<label for="research_results">Risultati mostrati: </label>
			<input type="number" name="research_results" id="research_results" value="<?php print $settings["searches"]["research_results"]; ?>" size="2" maxlength="3" tabindex="4" />
		</span>
	</fieldset>
	<button id="save_search_params_btn">Salva</button>
</form>