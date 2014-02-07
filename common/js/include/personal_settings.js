$(document).ready(function() {
	$("#refresh_interval").on("keyup change", function() {
		$(this).get_duration();
	});
	
	$("#refresh_interval").get_duration();
	if(window.location.hash) {
		var hash = window.location.hash.substring(1).replace(/\s+/g, "_"),
		target = $("#" + hash).offset().top;
	} else {
		var target = $("h1").eq(1).offset().top;
	}
	$("html, body").animate({ scrollTop: target }, 300);
	
	$("#save_settings_btn").click(function() {
		$("#page_loader").fadeIn(300);
		
		if(password == undefined) {
			var password = makeid();
		}
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt($("#personal_settings_frm").serialize(), password);
			console.log($("#personal_settings_frm").serialize());
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "save_personal_settings"
				},
				success: function(response) {
					if (response["data"] !== "ok") {
						var risp = response["data"].split("::");
						if(risp[0] == "error") {
							alert("Si &egrave; verificato un errore durante il salvataggio:\n" + risp[1], {icon: "error", title: "Ouch!"});
						}
					} else {
						window.location.href = "./Dashboard";
					}
				}
			});
		}, function() {
			$("#page_loader").fadeOut(300);
			alert("Si &egrave; verificato un errore durante il salvataggio.", {icon: "error", title: "Ouch!"});
		});
		return false;
	});
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
});