$(document).ready(function() {
	$("#password").focus();
	
	$("#reset_btn").click(function() {
		if($("#password").val() == $("#password2").val()) {
			$("#page_loader").fadeIn(150);
			
			var password = makeid();	
			$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
				var encryptedString = $.jCryption.encrypt($("#reset_pwd_frm").serialize(), password);
				
				$.ajax({
					url: "common/include/funcs/_ajax/decrypt.php",
					dataType: "json",
					type: "POST",
					data: {
						jCryption: encryptedString,
						type: "reset_password"
					},
					success: function(result) {
						$("#page_loader").fadeOut(150);
						apprise("La password &egrave; stata salvata correttamente.<br />Da ora &egrave; possibile effettuare l'accesso con le nuove credenziali", {title: "Password salvata con successo", icon: "success"}, function(r) {
							if(r) {
								$(window.location).attr("href", "./Accedi");
							}
						});
					}
				});
			});
		} else {
			$("#password").addClass("error").focus();
			$("#password2").addClass("error");
		}
	});
});