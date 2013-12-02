<?php
$logged_users_menu = file_get_contents("common/md/logged_menu.md");
$users_menu = file_get_contents("common/md/menu.md");
$user_config = parse_ini_file("common/include/conf/user/" . sha1($username) . "/user.conf", true);

if($user_config["User"]["use_editor_always"]) {
	$themes_select = str_replace('<option>' . $user_config["User"]["editor_theme"] . '</option>', '<option selected="selected">' . $user_config["User"]["editor_theme"] . '</option>', '<div class="right">Tema degli editor: <select id="code_theme" style="width: 200px;"><option>default</option><option>3024-day</option><option>3024-night</option><option>ambiance</option><option>base16-dark</option><option>base16-light</option><option>blackboard</option><option>cobalt</option><option>eclipse</option><option>elegant</option><option>erlang-dark</option><option>lesser-dark</option><option>mbo</option><option>midnight</option><option>monokai</option><option>neat</option><option>night</option><option>paraiso-dark</option><option>paraiso-light</option><option>rubyblue</option><option>solarized dark</option><option>solarized light</option><option>the-matrix</option><option>tomorrow-night-eighties</option><option>twilight</option><option>vibrant-ink</option><option>xq-dark</option><option>xq-light</option></select></div>');
	?>
	<link href="common/js/chosen/chosen.css" rel="stylesheet" />
	<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
	<?php require_once("common/tpl/scripts.codemirror.tpl"); ?>
	<script type="text/javascript">
	function load_editor(id) {
		var editor = CodeMirror.fromTextArea(document.getElementById(id), {
			content: document.getElementById(id),
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
								type: "save_menu"
							},
							success: function(response) {
								if (response["data"] !== "ok") {
									var risp = response["data"].split("::");
									if(risp[0] == "error") {
										alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
									}
								} else {
									$("#page_loader").fadeOut(300);
								}
							}
						});
					}, function() {
						$("#page_loader").fadeOut(300);
						alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
					});
					return false;
				},
				"Ctrl-Enter": "autocomplete"
			},
			onChange: function(n){
				editor.save();
				window.onbeforeunload = function(){ return 'onbeforeunload' };
			}
		});
		editor.setSize("100%", 250);
		function select_theme(editor, theme) {
			editor.setOption("theme", theme);
		}
		$("#code_theme").bind("change", function() {
			select_theme(editor, $(this).val());
		});
	}

	$(document).ready(function() {
		$("select").chosen({
			disable_search_threshold: 5,
			allow_single_deselect: true
		});
		$("#code_theme").bind("change", function(){
			var password = makeid();
			$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
				var encryptedString = $.jCryption.encrypt("code_theme=" + $("#code_theme").chosen().val() + "&user_username=" + $("#user_username").val(), password);
				
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: encryptedString,
						type: "save_editor_theme"
					},
					success: function(response) {
						if (response["data"] !== "ok") {
							var risp = response["data"].split("::");
							if(risp[0] == "error") {
								alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
							}
						} else {
							$("#page_loader").fadeOut(300);
						}
					}
				});
			});
		});
		load_editor("logged_menu");
		load_editor("menu");
	});
	</script>
	<?php
} else {
	$themes_select = "";
}
?>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	
	$("#save_editor_btn").click(function() {
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
					type: "save_menu"
				},
				success: function(response) {
					if (response["data"] !== "ok") {
						var risp = response["data"].split("::");
						if(risp[0] == "error") {
							alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
						}
					} else {
						window.onbeforeunload = null;
						location.reload();
					}
				}
			});
		}, function() {
			$("#page_loader").fadeOut(300);
			alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
		});
		return false;
	});
});
</script>
<h1>Gestione del menu superiore</h1>
<br />
<p><?php require_once("common/tpl/editor_status.tpl"); ?></p>
<p>Per collegare una voce del menu ad una nuova pagina &egrave; sufficiente creare la pagina con lo stesso nome.<br />Ad esempio il menu "<tt>* [Test](./Test)</tt>" sar&agrave; collegato alla pagina con nome "<tt>Test</tt>"</p>
<hr />
<br />
<form method="post" action="" class="editor_frm" id="editor_frm">
	<fieldset>
		<legend>Utenti collegati</legend>
		<input type="hidden" value="<?php print $username; ?>" name="user_username" id="user_username" />
		<label for="logged_menu" class="left">Menu visibile agli utenti registrati dopo aver fatto il login</label><?php print $themes_select; ?>
		<textarea name="logged_menu" id="logged_menu" style="width: 99%; height: 250px;"><?php print $logged_users_menu; ?></textarea>
	</fieldset>
	<fieldset>
		<legend>Utenti generici</legend>
		<label for="menu" class="left">Menu visibile a tutti</label>
		<textarea name="menu" id="menu" style="width: 99%; height: 250px;"><?php print $users_menu; ?></textarea>
	</fieldset>
</form>
<hr />
<button id="save_editor_btn">Salva</button>