$(document).ready(function(){
	var saved = "";
	$(".left").click(function() {
		window.onbeforeunload = null;
		return false;
	});
	$("#save_editor_btn, #export_btn, #remove_btn, #cancel_btn").click(function() {
		switch($(this).attr("id")) {
			case "save_editor_btn":
				$("#page_loader").fadeIn(300);
				
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: $.jCryption.encrypt($("#editor_frm").serialize(), password),
						type: "save_script"
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
							$("#remove_btn").attr("disabled", false);
							
							apprise('La config "' + $("#config_name").val() + '" &egrave; stata salvata con successo', {title: "Config salvata!", confirm: true, textCancel: '<span class="fa fa-angle-left">&nbsp;&nbsp;Torna al riepilogo', textOk: 'Continua la modifica&nbsp;&nbsp;<span class="fa fa-edit"></span>', icon: "success", allowExit: true}, function(r) {
								if(!r) {
									window.onbeforeunload = null;
									window.location.replace(document.referrer);
								}
							});
						}
					}
				});
				break;
			case "export_btn":
				window.onbeforeunload = null;
				$.download("common/include/funcs/_ajax/decrypt.php", "jCryption=" + $.jCryption.encrypt($("#editor_frm").serialize(), password) + "&type=download_data");
				break;
			case "remove_btn":
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: $.jCryption.encrypt($("#editor_frm").serialize(), password),
						type: "remove_script"
					},
					success: function(response) {
						if (response["data"] !== "ok") {
							var risp = response["data"].split("::");
							if(risp[0] == "error") {
								alert("Si &egrave; verificato un errore durante la rimozione:\n" + risp[1], {icon: "error", title: "Ouch!"});
							}
						} else {
							alert("Lo script in questa pagina &egrave; stato rimosso con successo", {icon: "success", title: "File rimosso"});
							$("#remove_btn").attr("disabled", "disabled");
						}
					}
				});
				break;
			case "cancel_btn":
				window.location.replace(document.referrer);
				break;
		}
		return false;
	});
	$("#code_theme").bind("change", function(){
		$.ajax({
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
						console.log("Si &egrave; verificato un errore durante il salvataggio", risp[1]);
					}
				} else {
					$("#page_loader").fadeOut(300);
					$("#original_name").val($("#config_name").val());
				}
			}
		});
	});
	
	var editor = CodeMirror.fromTextArea(document.getElementById("script"), {
		content: document.getElementById("script"),
		matchBrackets: true,
		tabMode: "indent",
		tabindex: 2,
		lineNumbers: true,
		theme: $("#code_theme").val(),
		//mode: "asterisk",
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
				
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: $.jCryption.encrypt($("#editor_frm").serialize(), password),
						type: "save_script"
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
							$("#remove_btn").attr("disabled", false);
						}
					}
				});
				return false;
			},
			"Ctrl-D": function(cm){
				window.onbeforeunload = null;
				$.download("common/include/funcs/_ajax/decrypt.php", "jCryption=" + $.jCryption.encrypt($("#editor_frm").serialize(), password) + "&type=download_data");
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
	$("#config_name").keyup(function() {
		if($(this).val().length == 0) {
			$("#export_btn").addClass("disabled");
		} else {
			$("#export_btn").removeClass("disabled");
		}
	});
	if($("#config_name").length > 0) {
		if($("#config_name").val().length == 0) {
			$("#export_btn").addClass("disabled");
		}
	}
});