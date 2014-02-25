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
				
				$.cryptAjax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: $.jCryption.encrypt($("#editor_frm").serialize(), password),
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
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("code_theme=" + $("#code_theme").chosen().val() + "&user_username=" + $("#user_username").val(), password),
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
	load_editor("logged_menu");
	load_editor("menu");
});