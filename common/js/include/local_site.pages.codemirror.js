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
				
				$.cryptAjax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: $.jCryption.encrypt($("#editor_frm").serialize(), password),
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
				return false;
			},
			"Ctrl-D": function(cm){
				$.download("common/include/funcs/_ajax/decrypt.php", "jCryption=" + $.jCryption.encrypt($("#editor_frm").serialize() + "&type=download_data");
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