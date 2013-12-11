<?php
if(trim($_GET["id"]) == "Pagine") {
	require_once("common/include/lib/mime_types.php");
	?>
	<h1>Gestione delle pagine</h1>
	<br />
	<fieldset class="frm">
		<legend>Pagine salvate</legend>
		<table cellpadding="10" cellspacing="10">
			<tr style="border-bottom: #ccc 1px solid;">
				<td><strong>Nome</strong></td><td><strong>Tipo di file</strong></td><td></td>
			</tr>
			<?php
			foreach(glob("common/md/pages/*.md") as $filename) {
				$file[] = $filename;
			}
			if(count($file) > 0) {
				foreach($file as $filename) {
					$info = pathinfo($filename);
					
					print '<tr><td><a href="./Admin/Sito_locale/' . base64_encode($filename) . '">' . $info["filename"] . '</a></td><td style="color: #999;">' . $mime_type[$info["extension"]] . '</td><td></td></tr>';
				}
			} else {
				print '<tr><td colspan="2" align="center"><span class="info">Nessuna pagina personale salvata</span></td></tr>';
			}
			?>
		</table>
	</fieldset>
	<a class="btn" href="./Admin/Sito_locale/Nuova_pagina">Nuova pagina</a>
	<?php
} else {
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_pagina") {
		$dir_name = "common/md/pages";
		$file_name = '<span class="left" style="width: 50%"><label for="page_name">Nome della pagina:</label> <input type="text" value="' . $info["filename"] . '" style="width: 30%;" id="page_name" name="page_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span>';
		$remove_btn = "";
	} else {
		$filename = base64_decode($_GET["id"]);
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/pages/") ? '<span class="left" style="width: 50%"><label for="page_name">Nome della pagina:</label> <input type="text" value="' . $info["filename"] . '" style="width: 30%;" id="page_name" name="page_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span>' : '<b>Nome della pagina:</b> <input type="hidden" value="' . $info["filename"] . '" name="page_name" /><span class="info">' . $info["filename"] . '</span>');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["filename"] . '"';
		$dir_name = $info["dirname"];
		$remove_btn = '<button id="remove_btn" class="red left remove" style="margin-right: 10px;">Elimina</button>';
	}
	
	if($user_config["User"]["use_editor_always"]) {
		$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="right">Tema dell\'editor: <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div>');
		?>
		<link href="common/js/chosen/chosen.css" rel="stylesheet" />
		<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$("select").chosen({
				disable_search_threshold: 5,
				allow_single_deselect: true
			});
			
			var editor = CodeMirror.fromTextArea(document.getElementById("script"), {
			content: document.getElementById("script"),
			matchBrackets: true,
			tabMode: "indent",
			tabindex: 2,
			lineNumbers: true,
			theme: $("#code_theme").val(),
			mode: "markdown",
			fixedGutter: true,
			extraKeys: {
				"Esc": function(cm) {
					if (isFullScreen(cm)) setFullScreen(cm, false);
				},
				"F11": function(cm) {
					setFullScreen(cm, !isFullScreen(cm));
				},
				"Ctrl-S": function(cm){
					$("#page_loader").fadeIn(300);
					var password = makeid();
					$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
						var encryptedString = $.jCryption.encrypt($("#editor_frm").serialize(), password);
						
						$.ajax({
							url: "common/include/funcs/_ajax/decrypt.php",
							dataType: "json",
							type: "POST",
							data: {
								jCryption: encryptedString,
								type: "save_page"
							},
							success: function(response) {
								if (response["data"] !== "ok") {
									var risp = response["data"].split("::");
									if(risp[0] == "error") {
										alert("Si &egrave; verificato un errore durante il salvataggio:\n" + risp[1], {icon: "error", title: "Ouch!"});
									}
								} else {
									$("#page_loader").fadeOut(300);
									$("#original_name").val($("#config_name").val());
								}
							}
						});
					}, function() {
						$("#page_loader").fadeOut(300);
						alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
					});
					return false;
				},
				"Ctrl-D": function(cm){
					var password = makeid();
					$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
						var encryptedString = $.jCryption.encrypt($("#editor_frm").serialize(), password);
						
						$.download("common/include/funcs/_ajax/decrypt.php", "jCryption=" + encryptedString + "&type=download_data");
					});
				},
				"Ctrl-Enter": "autocomplete"
			},
			onChange: function(n){
				editor.save();
				window.onbeforeunload = function(){ return 'onbeforeunload' };
			}
		});
		editor.setSize("100%", 450);
		function select_theme(editor, theme) {
			editor.setOption("theme", theme);
		}
		$("#code_theme").bind("change", function() {
			select_theme(editor, $(this).val());
		});
		});
		</script>
		<?php
	} else {
		$themes_select = "";
	}
	?>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<?php require_once("common/tpl/scripts.codemirror.tpl"); ?>
	<script src="common/js/include/common.js"></script>
	<script src="common/js/include/page_editor.js"></script>
	<script type="text/javascript">
	function optimize_name(string) {
		return string.replace(/\s+/g, "_").replace(/[^a-zA-Z0-9\ \-\_\~\:]+/g, "-");
	}
	$(document).ready(function() {
		$("#page_name").bind("keyup change", function() {
			$("#script_name").text('"' + optimize_name($("#page_name").val()) + '"');
			$("#rename_suggestion").css({"display": "inline"}).find("span").text(optimize_name($("#page_name").val()));
		});
		$("#page_name").val(optimize_name($("#page_name").val()));
		$("#page_name").bind("change", function() {
			$("#page_name").val(optimize_name($("#page_name").val()));
			$("#rename_suggestion").delay(1000).fadeOut(300);
		});
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	});
	</script>
	<h1>Gestione della pagina <span id="script_name"><?php print $script_name; ?></span></h1>
	<br />
	<p><?php require_once("common/tpl/editor_status.tpl"); ?></p>
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
					<textarea name="script" id="script" style="width: 100%; height: 450px;" tabindex="2"><?php print $file; ?></textarea>
				</td>
			</tr>
		</table>
	</form>
	<hr />
	<?php print $remove_btn; ?>
	<button id="save_editor_btn">Salva</button>
	<?php
}
?>