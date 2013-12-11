<link href="common/js/chosen/chosen.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
	$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
});
</script>
<h1>Impostazioni di ricerca</h1>
<br />
<form method="post" action="" id="settings_frm" onsubmit="false;">
	<fieldset class="frm">
		<legend>Scansioni locali <a name="Scansioni_locali" id="Scansioni_locali"></a></legend>
		
		<table cellpadding="0" cellspacing="0" style="width: auto;">
			<?php
			$mesi = array(1=>'gennaio', 'febbraio', 'marzo', 'aprile', 'maggio', 'giugno', 'luglio', 'agosto', 'settembre', 'ottobre', 'novembre', 'dicembre');
			
			$config = parse_ini_file("common/include/conf/config.ini", true);
			list($Y, $M, $d) = explode("-", $config["NAS"]["last_scan_date"]);
			//list($anno, $mese, $giorno) = explode("-", "2013-08-01");
			?>
			<tr><td>Data ultima scansione:</td><td><b><?php print $d . " " . ucfirst($mesi[(int)$M]) . " " . $Y; ?></b></td></tr>
			<tr><td>Durata della scansione:</td><td><b><?php print $config["NAS"]["last_scanning_time"]; ?></b></td></tr>
			<tr><td>Elementi scansionati:</td><td><b><?php print $config["NAS"]["last_items_count"]; ?></b></td></tr>
		</table>
		<p>
			&rsaquo; <a href="javascript:start_scan();" tabindex="1">Avvia una scansione manuale</a>
		</p>
	</fieldset>
	<fieldset class="frm">
		<legend>Ricerche <a name="Ricerche" id="Ricerche"></a></legend>
		<hr />
		<label><input type="checkbox" name="allow_advanced_research" id="allow_advanced_research" <?php print ($config["NAS"]["allow_advanced_research"] == "true") ? "checked" : ""; ?> tabindex="2" /> Consenti le ricerche avanzate</label>
		<br />
		<span class="left">
			<label for="research_type">Tipo di ricerca: </label>
			<select name="research_type" id="research_type" tabindex="3">
				<?php
				$research_types = array("query" => "Tutti i risultati possibili", "exactquery" => "Per frase esatta", "orquery" => "Per singola parola", "likequery" => "Per parole simili", "whatsnew" => "Sull'ultima scansione");
				foreach($research_types as $value => $txt) {
					if($config["NAS"]["research_type"] == $value) {
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
			<input type="number" name="research_results" id="research_results" value="<?php print $config["NAS"]["research_results"]; ?>" size="2" maxlength="3" tabindex="4" />
		</span>
	</fieldset>
	<button id="save_editor_btn">Salva</button>
</form>