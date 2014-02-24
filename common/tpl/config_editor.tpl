<?php
if(!isset($_GET["id"]) || trim($_GET["id"]) == "") {
	require_once("common/include/lib/mime_types.php");
	require_once("common/include/funcs/personal_configs.explode_tree.php");
	
	$dir_name = "common/include/conf/user/" . sha1($username) . "/configs";
	?>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<script src="common/js/include/common.js"></script>
	<script type="text/javascript">
	$.btn_collapse = function() {
		$(".dir_collapse").click(function() {
			var md5 = $(this).closest("tr").attr("id");
			if($("." + md5).hasClass("collapsed")) {
				$(this).attr("data-original-title", "Contrai la directory").prev("span.fa-fw").removeClass("fa-caret-right").addClass("fa-caret-down");
				$("." + md5).slideDown(600).removeClass("collapsed");
			} else {
				$(this).attr("data-original-title", "Espandi la directory").prev("span.fa-fw").removeClass("fa-caret-down").addClass("fa-caret-right");
				$("." + md5).slideUp().addClass("collapsed");
			}
		});
		$("#personal_configs tbody tr").hover(function() {
			$(this).find("a.remove_item").show();
			$(this).find("a.edit_item").show();
		}, function() {
			$(this).find("a.remove_item").hide();
			$(this).find("a.edit_item").hide();
		});
		
		$("a.remove_item, a.edit_item").click(function() {
			var md5 = $(this).closest("tr").attr("id"),
			dirname = $("tr#" + md5).find("td .dirname").attr("id");
			if($(this).hasClass("remove_item")) {
				apprise("Si &egrave; sicuri di voler rimuovere la directory \"" + dirname.replace("./", "") + "\" e tutto il suo contenuto?<br />Questo rimover&agrave; anche tutti i files al suo interno.", {title: "Rimozione dell'elemento", icon: "warning", confirm: "true"}, function(r) {
					if(r) {
						$.ajax({
							url: "common/include/funcs/_ajax/decrypt.php",
							dataType: "json",
							type: "POST",
							data: {
								jCryption: $.jCryption.encrypt("dir_name=" + dirname + "&username=" + $("#user_username").text(), password),
								type: "remove_config"
							},
							success: function(response) {
								if(response.error) {
									alert("Si &egrave; verificato un errore durante il salvataggio:<br />" + response.error_msg, {icon: response.icon, title: "Ouch!"});
								} else {
									$.update_list();
								}
							}
						});
					}
				});
			}
			if($(this).hasClass("edit_item")) {
				apprise("Inserire il nuovo nome della directory:", {title: "Rinomina la directory", icon: "fa-edit", input: dirname.replace("./", ""), textOk: "Rinomina"}, function(r) {
					if(r) {
						$.ajax({
							url: "common/include/funcs/_ajax/decrypt.php",
							dataType: "json",
							type: "POST",
							data: {
								jCryption: $.jCryption.encrypt("dir_name=" + dirname + "&username=" + $("#user_username").text() + "&new_name=" + r, password),
								type: "rename_dir"
							},
							success: function(response) {
								if(response.error) {
									alert("Si &egrave; verificato un errore durante il salvataggio:<br />" + response.error_msg, {icon: response.icon, title: "Ouch!"});
								} else {
									$.update_list();
								}
							}
						});
					}
				});
			}
		});
	};
	$.update_list = function() {
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "text",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("username=" + $("#user_username").text(), password),
				type: "get_personal_configs"
			},
			success: function(response) {
				$("#personal_configs tbody").remove();
				$("#personal_configs").append(response).slideDown();
				$("#page_loader").fadeOut(300);
				$(".new_dir_btn").attr("disabled", false);
				
				$.btn_collapse();
			}
		});
	}
	$(document).ready(function(){
		$(".new_dir_btn").attr("disabled", false);
		$(".new_dir_btn, .new_config_btn").click(function() {
			if($(this).hasClass("new_dir_btn")) {
				apprise("Nome della nuova directory:", {title: "Crea una nuova directory", icon: "fa-folder-open-o", input: true, allowExit: true}, function(r) {
					if(r) {
						$("#page_loader").fadeIn(300);
						$(".new_dir_btn").attr("disabled", "disabled");
						
						$.ajax({
							url: "common/include/funcs/_ajax/decrypt.php",
							dataType: "json",
							type: "POST",
							data: {
								jCryption: $.jCryption.encrypt("dir_name=" + r + "&user_conf_dir=" + $("#user_conf_dir").text(), password),
								type: "create_dir"
							},
							success: function(response) {
								if (response.error) {
									alert("Si &egrave; verificato un errore durante il salvataggio:<br />" + response.error_msg, {icon: response.icon, title: "Ouch!"});
								} else {
									$.update_list();
								}
							}
						});
					}
				});
			} else if($(this).hasClass("new_config_btn")) {
				window.location.replace("./Admin/Config_editor/Nuova_configurazione");
			}
		});
		$.btn_collapse();
	});
	</script>
	<div class="panel panel-default">
		<span style="display: none;" id="user_conf_dir"><?php print $dir_name; ?></span>
		<span style="display: none;" id="user_username"><?php print $username; ?></span>
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
					
					print '<tr><td><span class="' . $mime_type["ini"]["icon"] . '"></span>&nbsp;&nbsp;<a href="./Admin/Config_editor/' . base64_encode($filename) . '">' . $info["basename"] . '</a></td><td style="color: #999;">File di sistema</td></tr>';
				}
				?>
			</tbody>
		</table>
		<hr />
		<div class="btn-group right">
			<button class="btn btn-default new_dir_btn">Nuova directory&nbsp;&nbsp;&nbsp;<span class="fa fa-folder-open-o"></button>
			<button class="btn btn-primary new_config_btn">Nuova config&nbsp;&nbsp;&nbsp;<span class="fa fa-file-text-o"></button>
		</div>
		<br />
		<br />
		<table class="table" id="personal_configs" cellpadding="10" cellspacing="10">
			<thead>
				<tr style="border-bottom: #ccc 1px solid;">
					<th style="width: 20px;"></th><th style="width: 70%;">Nome</th><th>Tipo di file</th>
				</tr>
			</thead>
			<?php
			chdir($dir_name);
			if(exec('find ./ -mindepth 1 -maxdepth 2 \( ! -iname ".*" \) | sort', $scan)){
				foreach($scan as $file) {
					if(is_dir($file)) {
						$dirs[] = $file;
					} else {
						$files[] = $file;
					}
				}
				natcasesort($dirs);
				natcasesort($files);
				$all = array_merge($dirs, $files);
				$key_files = array_combine(array_values($all), array_values($all));
				$tree = explodeTree($key_files, "/");
				plotTree($tree, 0, true, $dir_name, $mime_type);
			}
			?>
		</table>
		<div class="btn-group right">
			<button class="btn btn-default new_dir_btn">Nuova directory&nbsp;&nbsp;&nbsp;<span class="fa fa-folder-open-o"></button>
			<button class="btn btn-primary new_config_btn">Nuova config&nbsp;&nbsp;&nbsp;<span class="fa fa-file-text-o"></button>
		</div>
	</div>
	<?php
} else {
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_configurazione") {
		$dir_name = "common/include/conf/user/" . sha1($username) . "/configs";
		$file_name = '<div class="col-md-8"><label for="config_name" class="col-md-2 control-label">Nome del file:</label><div class="col-md-10"><input type="text" value="" id="config_name" class="form-control" name="config_name" autofocus tabindex="1" /></div></div>';
	} else {
		$filename = str_replace("../../", "common/include/", base64_decode($_GET["id"]));
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/user/") ? '<div class="col-md-8"><label for="config_name" class="col-md-2 control-label">Nome del file:</label><div class="col-md-10"><input type="text" value="' . $info["basename"] . '" id="config_name" name="config_name" class="form-control" autofocus tabindex="1" /></div><input type="hidden" value="' . $info["basename"] . '" name="original_name" id="original_name" /></div>' : '<div class="col-md-8"><label for="config_name" class="col-md-2 control-label">Nome del file:</label><div class="col-md-10"><input type="hidden" value="' . $info["basename"] . '" name="config_name" id="config_name" /><span class="info">' . $info["basename"] . '</span></div></div>');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["basename"] . '"';
		$dir_name = $info["dirname"];
	}
	$remove_btn = (strpos($filename, "/user/") ? '<div class="btn-group"><button id="cancel_btn" class="btn btn-default"><span class="fa fa-angle-left"></span>&nbsp;&nbsp;&nbsp;Annulla</button><button id="remove_btn" class="btn btn-danger">Elimina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-trash"></span></button></div>' : '<button id="cancel_btn" class="btn btn-default"><span class="fa fa-angle-left"></span>&nbsp;&nbsp;&nbsp;Annulla</button>');
	$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="col-md-4"><div class="right"><label for="code_theme">Tema dell\'editor:</label> <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div></div>');
	?>
	<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
	<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<?php require_once("common/tpl/scripts.codemirror.tpl"); ?>
	<script src="common/js/include/common.js"></script>
	<script src="common/js/include/editor.js"></script>
	<script type="text/javascript">
	$.optimize_name = function(string) { return string.replace(/[^a-zA-Z0-9 \.\+\-\_\~\:]+/ig, "-"); };
	$(document).ready(function() {
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
		
		$("#config_name").bind("keyup change", function() {
			$(this).val($.optimize_name($("#config_name").val())).popover('destroy').popover({
				content: 'Sar&agrave; rinominato in "' + $.optimize_name($.trim($("#config_name").val())) + '"',
				placement: "top",
				html: "true"
			}).popover("show");
			$("#script_name").text('"' + $.optimize_name($("#config_name").val()) + '"');
		});
		$("#config_name").bind("blur", function() {
			$("#config_name").popover("hide");
		});
		$("select").chosen({
			disable_search_threshold: 5,
			allow_single_deselect: true
		});
		$("#config_name").val($.optimize_name($("#config_name").val()));
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
	<form method="post" action="" class="editor_frm form-horizontal" id="editor_frm">
		<div class="form-group">
			<?php print $file_name . $themes_select; ?></td>
		</div>
		<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
		<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
		<textarea name="script" id="script" style="width: 100%; height: 450px;"><?php print $file; ?></textarea>
		<span class="help-block">Puoi trascinare il file di testo direttamente in questa textarea...</span>
		
		<div class="form-group">
			<label for="save_script_dir" class="col-md-2 control-label">Directory di destinazione: </label>
			<div class="col-md-8">
				<select name="save_script_dir" id="save_script_dir" style="width: 200px;">
					<option>root</option>
					<?php
					$root_path = "common/include/conf/user/" . sha1($username) . "/configs";
					$scanned_directory = array_diff(scandir($root_path), array('..', '.'));
					foreach ($scanned_directory as $dir) {
						if(is_dir($root_path . "/" . $dir)) {
							if($dir_name !== $root_path && $dir == str_replace($root_path . "/", "", $dir_name)) {
								print '<option value="' . $dir . '" selected>' . $dir . '</option>';
							} else {
								print '<option value="' . $dir . '">' . $dir . '</option>';
							}
							
						}
					}
					?>
				</select>
			</div>
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