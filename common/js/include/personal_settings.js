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
		
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt($("#personal_settings_frm").serialize(), password),
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
		return false;
	});
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
});