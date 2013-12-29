$(document).ready(function() {
	$("#session_length").on("keyup change", function() {
		get_duration($(this));
	});
	
	get_duration($("#session_length"));
	if(window.location.hash) {
		var hash = window.location.hash.substring(1).replace(/\s+/g, "_"),
		target = $("#" + hash).offset().top;
	} else {
		var target = $("h1").eq(1).offset().top;
	}
	$("html, body").animate({ scrollTop: target }, 300);
	
	$("#save_settings_btn").click(function() {
		$("#page_loader").fadeIn(300);
		var password = makeid();
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt($("#settings_frm").serialize(), password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "save_settings"
				},
				success: function(response) {
					if (response["data"] !== "ok") {
						var risp = response["data"].split("::");
						if(risp[0] == "error") {
							alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
						}
					} else {
						window.location.href = "./Admin";
					}
				}
			});
		}, function() {
			$("#page_loader").fadeOut(300);
			alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
		});
		return false;
	});
	$("#show_shortcut_btn").click(function() {
		$("#shortcut_legend").slideToggle(300, function() {
			if($(this).css("display") == "none") {
				$("#show_shortcut_btn > span").text("Visualizza");
			} else {
				$("#show_shortcut_btn > span").text("Nascondi");
			}
		});
	});
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
});