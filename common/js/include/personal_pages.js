function save_page() {
	$("#page_loader").fadeIn(300);
	
	$.ajax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt($("#editor_frm").serialize(), password),
			type: "save_personal_page"
		},
		success: function(response) {
			if (response["data"] !== "new" && response["data"] !== "edit") {
				var risp = response["data"].split("::");
				alert("Si &egrave; verificato un errore durante il salvataggio:\n" + risp[1], {icon: "error", title: "Ouch!"});
			} else {
				$("#save_editor_btn").addClass("disabled");
				$("#page_loader").fadeOut(300);
				$("#remove_btn").attr("disabled", false);
				if($("#response .alert").length > 0) {
					$("#response .alert").switchClass("alert-info", "alert-success").find(".fa").switchClass("fa-info", "fa-check");
					$("#response .alert span:not(.fa)").html('Il file &egrave; stato ' + ((response["data"] == "new") ? 'creato' : 'salvato') + ' correttamente');
				} else {
					$("#response").html('<div class="alert alert-success"><span class="fa fa-check"></span>&nbsp;&nbsp;<span>Il file &egrave; stato ' + ((response["data"] == "new") ? 'creato' : 'salvato') + ' correttamente</span></div>').slideDown(300);
				}
				window.onbeforeunload = null;
			}
		}
	});
}
$(document).ready(function() {
	if($("h1").eq(1).length > 0) {
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	}
	
	if($("textarea").length > 0) {
		$("textarea#page_content").pagedownBootstrap({
			"sanatize": true,
			"hooks": [{
				"event": "preConversion"
			}, {
				"event": "plainLinkText",
				"callback": function (url) {
					return url.replace(/^https?:\/\//, "");
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
	}
	$(".form-control").keydown(function() {
		window.onbeforeunload = function(){ return 'onbeforeunload' };
		
		$("#save_editor_btn").removeClass("disabled");
		if($("#response .alert").length > 0) {
			if($("#response .alert-info").length == 0) {
				$("#response .alert").switchClass("alert-success", "alert-info").find(".fa").switchClass("fa-check", "fa-info");
				$("#response .alert span:not(.fa)").text("In corso di modifica...");
			}
		} else {
			$("#response").html('<div class="alert alert-info"><span class="fa fa-info"></span>&nbsp;&nbsp;<span>In corso di modifica...</span></div>').slideDown(300);
		}
	});
	
	$(".remove_btn").click(function() {
		var script_dir = $(this).closest("tr").find(".script_dir").val(),
		page_name = $(this).closest("tr").find(".page_name").val();
		
		apprise("Si &egrave; sicuri di voler rimuovere questa pagina?", {title: "Conferma della rimozione", icon: "warning", inverted: true}, function(r) {
			if(r) {
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: $.jCryption.encrypt("script_dir=" + script_dir + "&page_name=" + page_name, password),
						type: "remove_personal_page"
					},
					success: function(response) {
						if (response["data"] !== "ok") {
							var risp = response["data"].split("::");
							if(risp[0] == "error") {
								alert("Si &egrave; verificato un errore durante la rimozione:\n" + risp[1], {icon: "error", title: "Ouch!"});
							}
						} else {
							alert({icon: "success", title: "File rimosso"}, function(r) {
								if(r) {
									$.ajax({
										url: "common/include/funcs/_ajax/decrypt.php",
										dataType: "text",
										type: "POST",
										data: {
											jCryption: $.jCryption.encrypt("script_dir=" + script_dir, password),
											type: "check_personal_page"
										},
										success: function(response) {
											$("#pages_dash").html(response);
										}
									});
								}
							});
							$("#remove_btn").attr("disabled", "disabled");
						}
					}
				});
			}
		});
	});
});