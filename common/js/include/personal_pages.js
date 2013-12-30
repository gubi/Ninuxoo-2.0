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
	
	$("textarea#page_content").pagedownBootstrap({
		"sanatize": false,
		"help": function () {
			alert("Do you need help?");
			return false;
		},
		"hooks": [{
			"event": "preConversion",
			"callback": function (text) {
				return text.replace(/\b(a\w*)/gi, "*$1*");
			}
		}, {
			"event": "plainLinkText",
			"callback": function (url) {
				return "This is a link to " + url.replace(/^https?:\/\//, "");
			}
		}]
	});
	$("#preview_btn").toggle(function() {
		$(".wmd-button-bar").fadeOut(300);
		$(".wmd-input").fadeOut(300, function() {
			$(".wmd-preview").fadeIn(300);
		});
		$(this).html('Modifica&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-edit"></span>');
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	}, function() {
		$(".wmd-preview").fadeOut(300, function() {
			$(".wmd-button-bar").fadeIn(300);
			$(".wmd-input").fadeIn(300);
		});
		$(this).html('Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span>');
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	});
	
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
	$("#save_editor_btn, #remove_btn").click(function() {
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
								$("#remove_btn").attr("disabled", false);
								alert("Questa pagina &egrave; stata salvata con successo.", {icon: "success", title: "File aggiunto"});
							}
						}
					});
				}, function() {
					$("#page_loader").fadeOut(300);
					alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
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
							type: "remove_personal_page"
						},
						success: function(response) {
							if (response["data"] !== "ok") {
								var risp = response["data"].split("::");
								if(risp[0] == "error") {
									alert("Si &egrave; verificato un errore durante la rimozione:\n" + risp[1], {icon: "error", title: "Ouch!"});
								}
							} else {
								alert("Questa pagina &egrave; stata rimossa con successo.", {icon: "success", title: "File rimosso"});
								$("#remove_btn").attr("disabled", "disabled");
							}
						}
					});
				});
				break;
			
		}
		return false;
	});
});