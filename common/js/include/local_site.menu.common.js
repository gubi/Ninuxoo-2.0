$(document).ready(function() {
	$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	
	$("#save_menu_btn").click(function() {
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
					window.onbeforeunload = null;
					location.reload();
				}
			}
		});
		return false;
	});
});