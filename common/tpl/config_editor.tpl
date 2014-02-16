<?php
if(!isset($_GET["id"]) || trim($_GET["id"]) == "") {
	require_once("common/include/lib/mime_types.php");
	?>
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="glyphicon glyphicon-list-alt"></span>&nbsp;&nbsp;Tutte le tue config<small class="help-block">Files di configurazione salvati</small></span></div></div>
		<table class="table" cellpadding="10" cellspacing="10">
			<thead>
				<tr>
					<th>Nome</th><th>Tipo di file</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach(glob("common/include/conf/*.ini") as $filename) {
					$info = pathinfo($filename);
					
					print '<tr><td><a href="./Admin/Config_editor/' . base64_encode($filename) . '">' . $info["basename"] . '</a></td><td style="color: #999;">File di sistema</td></tr>';
				}
				?>
			</tbody>
		</table>
		<hr />
		<table class="table" cellpadding="10" cellspacing="10">
			<thead>
				<tr style="border-bottom: #ccc 1px solid;">
					<th>Nome</th><th>Tipo di file</th>
				</tr>
			</thead>
			<tbody>
				<?php
				foreach(glob("common/include/conf/user/" . sha1($username) . "/configs/*") as $filename) {
					$file[] = $filename;
				}
				if(count($file) > 0) {
					foreach($file as $filename) {
						$info = pathinfo($filename);
						
						print '<tr><td><a href="./Admin/Config_editor/' . base64_encode($filename) . '">' . $info["basename"] . '</a></td><td style="color: #999;">' . $mime_type[$info["extension"]] . '</td></tr>';
					}
				} else {
					print '<tr><td colspan="2" align="center"><span class="info">Nessuna configurazione personale salvata</span></td></tr>';
				}
				?>
			</tbody>
		</table>
		<a class="btn btn-primary right" href="./Admin/Config_editor/Nuova_configurazione">Nuova config&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></a>
	</div>
	<?php
} else {
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_configurazione") {
		$dir_name = "common/include/conf/user/" . sha1($username) . "/configs";
		$file_name = '<div class="col-md-8"><label for="config_name">Nome del file:</label> <input type="text" value="" id="config_name" name="config_name" autofocus tabindex="1" /></div>';
	} else {
		$filename = base64_decode($_GET["id"]);
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/user/") ? '<div class="col-md-3"><label for="config_name">Nome del file:</label> <input type="text" value="' . $info["basename"] . '" id="config_name" name="config_name" autofocus tabindex="1" /></div><div class="col-md-5"><span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["basename"] . '" name="original_name" id="original_name" /></span></div>' : '<div class="col-md-8"><label>Nome del file:</label> <input type="hidden" value="' . $info["basename"] . '" name="config_name" id="config_name" /><span class="info">' . $info["basename"] . '</span></div>');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["basename"] . '"';
		$dir_name = $info["dirname"];
	}
	$remove_btn = (strpos($filename, "/user/") ? '<button id="remove_btn" class="btn btn-danger" style="margin-right: 10px;">Elimina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-trash"></span></button>' : '');
	$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="col-md-4"><div class="right"><label for="code_theme">Tema dell\'editor:</label> <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div></div>');
	?>
	<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
	<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
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
	<?php
	require_once("common/tpl/shortcut_legend.tpl");
	?>
	<hr />
	<br />
	<form method="post" action="" class="editor_frm" id="editor_frm">
		<div class="form-group">
			<div class="row">
				<?php print $file_name . $themes_select; ?></td>
			</div>
		</div>
		<div class="form-group">
			<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
			<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
			<textarea name="script" id="script" style="width: 100%; height: 450px;"><?php print $file; ?></textarea>
		</div>
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