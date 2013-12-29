<?php
if(!isset($_GET["id"]) || trim($_GET["id"]) == "") {
	require_once("common/include/lib/mime_types.php");
	?>
	<fieldset class="frm">
		<legend>Files di configurazione salvati</legend>
		<table cellpadding="10" cellspacing="10">
			<tr>
				<td><strong>Nome</strong></td><td><strong>Tipo di file</strong></td><td></td>
			</tr>
			<?php
			foreach(glob("common/include/conf/*.ini") as $filename) {
				$info = pathinfo($filename);
				
				print '<tr><td><a href="./Admin/Config_editor/' . base64_encode($filename) . '">' . $info["basename"] . '</a></td><td style="color: #999;">File di sistema</td><td></td></tr>';
			}
			?>
		</table>
		<hr />
		<table cellpadding="10" cellspacing="10">
			<tr style="border-bottom: #ccc 1px solid;">
				<td><strong>Nome</strong></td><td><strong>Tipo di file</strong></td><td></td>
			</tr>
			<?php
			foreach(glob("common/include/conf/user/" . sha1($username) . "/configs/*") as $filename) {
				$file[] = $filename;
			}
			if(count($file) > 0) {
				foreach($file as $filename) {
					$info = pathinfo($filename);
					
					print '<tr><td><a href="./Admin/Config_editor/' . base64_encode($filename) . '">' . $info["basename"] . '</a></td><td style="color: #999;">' . $mime_type[$info["extension"]] . '</td><td></td></tr>';
				}
			} else {
				print '<tr><td colspan="2" align="center"><span class="info">Nessuna configurazione personale salvata</span></td></tr>';
			}
			?>
		</table>
	</fieldset>
	<a class="btn btn-primary right" href="./Admin/Config_editor/Nuova_configurazione">Nuova config&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></a>
	<?php
} else {
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_configurazione") {
		$dir_name = "common/include/conf/user/" . sha1($username) . "/configs";
		$file_name = '<span class="left" style="width: 50%"><label for="config_name">Nome del file:</label> <input type="text" value="" style="width: 30%;" id="config_name" name="config_name" autofocus tabindex="1" /></span>';
	} else {
		$filename = base64_decode($_GET["id"]);
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/user/") ? '<span class="left" style="width: 50%"><label for="config_name">Nome del file:</label> <input type="text" value="' . $info["basename"] . '" style="width: 30%;" id="config_name" name="config_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["basename"] . '" name="original_name" id="original_name" /></span>' : '<b>Nome del file:</b> <input type="hidden" value="' . $info["basename"] . '" name="config_name" id="config_name" /><span class="info">' . $info["basename"] . '</span>');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["basename"] . '"';
		$dir_name = $info["dirname"];
	}
	$remove_btn = (strpos($filename, "/user/") ? '<button id="remove_btn" class="btn btn-danger" style="margin-right: 10px;">Elimina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-trash"></span></button>' : '');
	$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="right">Tema dell\'editor: <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div>');
	?>
	<link href="common/js/chosen/chosen.css" rel="stylesheet" />
	<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<?php require_once("common/tpl/scripts.codemirror.tpl"); ?>
	<script src="common/js/include/common.js"></script>
	<script src="common/js/include/editor.js"></script>
	<script type="text/javascript">
	function optimize_name(string) {
		return string.replace(/[^a-zA-Z0-9 \.\+\-\_\~\:]+/g, "-");
	}
	$(document).ready(function() {
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
		
		$("#config_name").bind("keyup change", function() {
			$("#script_name").text('"' + optimize_name($("#config_name").val()) + '"');
			$("#rename_suggestion").css({"display": "inline"}).find("span").text(optimize_name($("#config_name").val()));
		});
		$("#config_name").bind("change", function() {
			$("#config_name").val(optimize_name($("#config_name").val()));
			$("#rename_suggestion").delay(1000).fadeOut(300);
		});
		$("select").chosen({
			disable_search_threshold: 5,
			allow_single_deselect: true
		});
		$("#config_name").val(optimize_name($("#config_name").val()));
	});
	</script>
	
	<h1>Editor di configurazione dello script <span id="script_name"><?php print $script_name; ?></span></h1>
	<br />
	<p>
		&Egrave; possibile utilizzare le scorciatoie di tastiera per abilitare funzionalit&agrave; aggiuntive.<br />
		Trascinando un file di testo all'interno dell'editor ne verr&agrave; acquisito il testo.<br />
	</p>
	<div class="panel-group" id="accordion">
		<div class="panel panel-default">
			<div class="panel-heading">
				<a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Scorciatoie da tastiera (attivando il focus nell'editor) <span class="caret"></span></a>
			</div>
			<div id="collapseOne" class="panel-collapse collapse">
				<div class="panel-body">
					<b>F11</b>: Attiva la modalit&agrave; schermo intero<br />
					<b>Esc</b>: Esce dalla modalit&agrave; schermo intero<br />
					<b>CTRL+F</b>: Cerca nel testo<br />
					<b>Shift+CTRL+F</b>: Sostituisce un termine nel testo<br />
					<b>CTRL+Invio</b>: Attiva l'autocompletamento<br />
					<b>CTRL+S</b>: Salva il file<br />
					<b>CTRL+D</b>: Scarica il file
				</div>
			</div>
		</div>
	</div>
	<hr />
	<br />
	<form method="post" action="" class="editor_frm" id="editor_frm">
		<table cellspacing="10" cellpadding="10">
			<tr>
				<td><?php print $file_name . $themes_select; ?></td>
			</tr>
			<tr>
				<td style="padding-top: 10px;">
					<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
					<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
					<textarea name="script" id="script" style="width: 100%; height: 450px;"><?php print $file; ?></textarea>
				</td>
			</tr>
		</table>
	</form>
	<hr />
	<?php print $remove_btn; ?>
	<div class="btn-group right">
		<button id="export_btn" type="button" class="btn btn-default">Scarica&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-download-alt"></span></button>
		<button id="save_editor_btn" type="button" class="btn btn-primary">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
	</div>
	<?php
}
?>