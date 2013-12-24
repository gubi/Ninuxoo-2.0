function login() {
	if($("#username").val() !== "") {
		if($("#password").val() !== "") {
			$("#page_loader").fadeIn(150);
			var password = makeid();	
			$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
				var encryptedString = $.jCryption.encrypt($("#login_frm").serialize(), password);
				
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: encryptedString,
						type: "login"
					},
					success: function(result) {
						if(result["error"]) {
							apprise(result["message"], {}, function(r) {
								if(r) {
									$("#username").addClass("error").focus();
									$("#password").addClass("error");
									$("#page_loader").fadeOut(150);
								}
							});
						} else {
							$(window.location).attr("href", "./Admin");
						}
					}
				});
			});
		} else {
			$("#password").addClass("error").focus();
		}
	} else {
		$("#username").addClass("error").focus();
	}
	return false;
}
$(document).ready(function() {
	$("#username, #password").on("keyup change", function() {
		if($(this).val().length > 0) {
			$(this).removeClass("error");
		} else {
			$(this).addClass("error");
		}
	});
	$("#username").focus();
});