$(document).ready(function() {
	$("#username").focus();
	
	$("#reset_btn").click(function() {
		$("#page_loader").fadeIn(150);
		
		var password = makeid();	
		$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
			var encryptedString = $.jCryption.encrypt("username=" + $("#username").val(), password);
			
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: encryptedString,
					type: "reset_password.send_mail"
				},
				success: function(result) {
					if(result["error"]) {
						apprise(result["message"], {title: "Ooops... qualcosa &egrave; andato storto!", icon: "error"}, function(r) {
							if(r) {
								$("#username").addClass("error").focus();
								$("#page_loader").fadeOut(150);
							}
						});
					} else {
						$("#page_loader").fadeOut(150);
						apprise(result["message"], {title: "Okay", icon: "success"}, function(r) {
							if(r) {
								$(window.location).attr("href", "./Accedi");
							}
						});
					}
				}
			});
		});
	});
});