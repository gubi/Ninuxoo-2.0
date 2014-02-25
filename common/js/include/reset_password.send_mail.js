$(document).ready(function() {
	$("#username").focus();
	
	$("#reset_btn").click(function() {
		$("#page_loader").fadeIn(150);
		
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("username=" + $("#username").val(), password),
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