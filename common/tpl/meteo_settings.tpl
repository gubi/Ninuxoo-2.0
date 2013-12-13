<?php
$config = parse_ini_file("common/include/conf/config.ini", true);
?>
<link href="common/js/chosen/chosen.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.min.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	if(window.location.hash) {
		var hash = window.location.hash.substring(1).replace(/\s+/g, "_"),
		target = $("#" + hash).offset().top;
	} else {
		var target = $("h1").eq(1).offset().top;
	}
	$("html, body").animate({ scrollTop: target }, 300);
	
	$("#save_editor_btn").click(function() {
		$("#page_loader").fadeIn(300);
		var password = makeid();
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt($("#meteo_settings_frm").serialize(), password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "save_meteo_settings"
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
	$("select").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
});
</script>
<h1>Impostazioni Meteo</h1>
<br />
<form class="frm" method="post" action="" id="meteo_settings_frm" onsubmit="return false;">
	<hr />
</form>