$(document).ready(function() {
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
	$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	
	$("#start_scan_btn").click(function() {
		apprise("Scansione dei files locali in corso...", {"progress": true});
		var password = makeid();
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt("token=" + $("#token").val(), password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "start_scan"
				},
				success: function(response) {
					$("#last_scan_date").html("<b>" + response["data"]["date"] + "</b>");
					$("#last_scanning_time").html("<b>" + response["data"]["elapsed_time"] + "</b>");
					$("#last_items_count").html("<b>" + response["data"]["files_count"] + "</b>");
					$(".appriseOuter").fadeOut(300);
					$(".appriseOverlay").fadeOut(300);
				}
			});
		}, function() {
			$("#page_loader").fadeOut(300);
			alert("Si &egrave; verificato un errore durante la scansione.", {icon: "error", title: "Ouch!"});
		});
	});
	$("#save_search_params_btn").click(function() {
		$("#page_loader").fadeIn(300);
		var password = makeid();
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt($("#search_settings_frm").serialize(), password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "save_search_settings"
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
	});
});