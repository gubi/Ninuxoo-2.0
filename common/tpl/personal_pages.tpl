<?php
if(trim($_GET["id"]) !== "Nuova_pagina") {
	require_once("common/include/lib/mime_types.php");
	
	?>
	<fieldset class="frm">
		<legend>Pagine personali</legend>
		<table cellpadding="10" cellspacing="10">
			<tr style="border-bottom: #ccc 1px solid;">
				<td><strong>Nome</strong></td><td><strong>Tipo di file</strong></td><td></td>
			</tr>
			<?php
			if(!file_exists("common/md/pages/" . sha1($data[1]))) {
				mkdir("common/md/pages/" . sha1($data[1]));
				chmod("common/md/pages/" . sha1($data[1]), 0777);
			}
			foreach(glob("common/md/pages/" . sha1($data[1]) . "/*.md") as $filename) {
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
	<a class="btn btn-primary right" href="./Dashboard/Pagine/Nuova_pagina">Nuova pagina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-plus"></span></a>
	<?php
} else {
	$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);
	
	if($_GET["id"] == "Nuova_pagina") {
		$dir_name = "common/md/pages";
		$file_name = '<span><label for="page_name">Nome del file della pagina:</label> <input type="text" value="' . $info["filename"] . '" style="width: 30%;" id="page_name" name="page_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span><br /><br />';
		$remove_btn = "";
	} else {
		$filename = base64_decode($_GET["id"]);
		$info = pathinfo($filename);
		$file_name = (strpos($filename, "/pages/") ? '<span><label for="page_name">Nome del file della pagina:</label> <input type="text" value="' . $info["filename"] . '" style="width: 30%;" id="page_name" name="page_name" autofocus tabindex="1" />&emsp;<span class="info" id="rename_suggestion" style="display: none;">Sar&agrave; rinominato in "<span></span>"</span><input type="hidden" value="' . $info["filename"] . '" name="original_name" id="original_name" /></span>' : '<b>Nome della pagina:</b> <input type="hidden" value="' . $info["filename"] . '" name="page_name" /><span class="info">' . $info["filename"] . '</span><br /><br />');
		$file = file_get_contents($filename);
		$script_name = '"' . $info["filename"] . '"';
		$dir_name = $info["dirname"];
		$remove_btn = '<button id="remove_btn" class="btn btn-danger" style="margin-right: 10px;">Elimina&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-trash"></span></button>';
	}
	
	if($user_config["User"]["use_editor_always"]) {
		?>
		<link href="common/js/chosen/chosen.css" rel="stylesheet" />
		<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
		<script type="text/javascript">
		$(document).ready(function() {
			$("select").chosen({
				disable_search_threshold: 5,
				allow_single_deselect: true
			});
			function save() {
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
							type: "save_personal_page"
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
			}
		});
		</script>
		<?php
	} else {
	}
	?>
	<script src="common/js/include/common.js"></script>
	<link rel="stylesheet/less" type="text/css" href="common/js/pagedown-bootstrap/demo/browser/demo.less" />
	<script src="common/js/pagedown-bootstrap/demo/browser/less/less-1.2.2.min.js"></script>
	<script type="text/javascript" src="common/js/pagedown-bootstrap/Markdown.Converter.js"></script>
	<script type="text/javascript" src="common/js/pagedown-bootstrap/Markdown.Sanitizer.js"></script>
	<script type="text/javascript" src="common/js/pagedown-bootstrap/Markdown.Editor.js"></script>
	<script type="text/javascript" src="common/js/pagedown-bootstrap/local/Markdown.local.it.js"></script>
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
		
		$("#preview_btn").toggle(function() {
			$("#wmd-button-bar").fadeOut(300);
			$("#wmd-input").fadeOut(300, function() {
				$("#wmd-preview").fadeIn(300);
			});
			$(this).html('Modifica&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-edit"></span>');
			$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
		}, function() {
			$("#wmd-preview").fadeOut(300, function() {
				$("#wmd-button-bar").fadeIn(300);
				$("#wmd-input").fadeIn(300);
			});
			$(this).html('Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span>');
			$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
		});
	});
	</script>
	<h1>Gestione della pagina <span id="script_name"><?php print $script_name; ?></span></h1>
	<hr />
	<br />
	<form method="post" action="" class="editor_frm" id="editor_frm">
		<table cellspacing="10" cellpadding="10">
			<tr>
				<td><?php print $file_name; ?></td>
			</tr>
			<tr>
				<td style="padding-top: 10px;">
					<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
					<input type="hidden" value="<?php print $dir_name; ?>" name="script_dir" />
					<div class="wmd-panel">
						<div id="wmd-button-bar"></div>
						<textarea name="wmd-input" class="wmd-input" id="wmd-input" style="width: 100%; height: 450px;" tabindex="2"><?php print $file; ?></textarea>
					</div>
					<div id="wmd-preview" class="wmd-panel wmd-preview" style="display: none;"></div>
					<script type="text/javascript">
					(function () {
						var converter = Markdown.getSanitizingConverter();
						var editor = new Markdown.Editor(converter, null, { strings: Markdown.local.it });
						editor.hooks.set("insertImageDialog", function (callback) {
							apprise('Inserisci l\'indirizzo dell\'immagine oppure <a href="javascript: void(0);">caricala</a>', {input: true, icon: "success", title: "Inserisci immagine"});
							
							return true; // tell the editor that we'll take care of getting the image url
						});
						editor.run();
					})();
					</script>
				</td>
			</tr>
		</table>
	</form>
	<hr />
	<?php print $remove_btn; ?>
	<button class="btn btn-default" id="preview_btn">Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span></button>
	<button class="btn btn-primary right" id="save_editor_btn">Salva&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-ok"></span></button>
	<?php
}
?>