$(document).ready(function(){
	var saved = "";
	$(".left").click(function() {
		window.onbeforeunload = null;
		return false;
	});
	$("#save_editor_btn, #export_btn, #remove_btn").click(function() {
		switch($(this).attr("id")) {
			case "save_editor_btn":
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
				}, function() {
					$("#page_loader").fadeOut(300);
					alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
				});
				break;
			case "export_btn":
				var password = makeid();
				$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
					var encryptedString = $.jCryption.encrypt($("#editor_frm").serialize(), password);
					
					$.download("common/include/funcs/_ajax/decrypt.php", "jCryption=" + encryptedString + "&type=download_data");
				});
				break;
			case "remove_btn":
				var password = makeid();
				$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
					var encryptedString = $.jCryption.encrypt($("#editor_frm").serialize(), password);
					
					$.ajax({
						url: "common/include/funcs/_ajax/decrypt.php",
						dataType: "json",
						type: "POST",
						data: {
							jCryption: encryptedString,
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
				});
				break;
			
		}
		return false;
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
							console.log("Si &egrave; verificato un errore durante il salvataggio", risp[1]);
						}
					} else {
						$("#page_loader").fadeOut(300);
						$("#original_name").val($("#config_name").val());
					}
				}
			});
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
				var password = makeid();
				$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
					var encryptedString = $.jCryption.encrypt($("#editor_frm").serialize(), password);
					
					$.ajax({
						url: "common/include/funcs/_ajax/decrypt.php",
						dataType: "json",
						type: "POST",
						data: {
							jCryption: encryptedString,
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
				}, function() {
					$("#page_loader").fadeOut(300);
					alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
				});
				return false;
			},
			"Ctrl-D": function(cm){
				window.onbeforeunload = null;
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