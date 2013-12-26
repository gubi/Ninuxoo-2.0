function check_nodes() {
	$.ajax({
		url: "common/include/funcs/_ajax/install.get_nodes.php",
		dataType: 'json'
	}).done(function(source) {
		if($("#user_password").val() == $("#user_password2").val()) {
			if(source["error"] == "no file") {
				apprise("Si &egrave; verificato un errore durante la verifica dei nodi attivi.<br />Per favore, riprova in un secondo momento", {icon: "error", title: "Non riesco a validare i nodi attivi"});
			} else {
				var nodename = $("#node_name").val().toLowerCase();
				
				var nodes = $.map(source, function (key, value) {
					if($.trim(key["name"]).toLowerCase() == nodename) {
						var password = makeid();
						$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
							var encryptedString = $.jCryption.encrypt($("#registration_frm").serialize(), password);
							$.ajax({
								url: "common/include/funcs/_ajax/decrypt.php",
								dataType: "json",
								type: "POST",
								data: {
									jCryption: encryptedString,
									type: "register_user"
								},
								success: function(response) {
									switch(response["data"]) {
										case "ok":
											$("#page_loader").fadeOut(300);
											apprise("Un'e-mail con i dettagli per proseguire &egrave; stata inviata all'indirizzo specificato.<br />Controlla la posta!", {icon: "success", title: "Controlla la posta"}, function(r) {
												if(r) {
													$(window.location).attr("href", "./Accedi");
												}
											});
											break;
										case "user exists":
											$("#page_loader").fadeOut(300);
											apprise("Si &egrave; verificato un errore durante il salvataggio dei dati<br />perch&eacute; un utente con questo nome esiste gi&agrave;", {icon: "error", title: "L'utente esiste gi&agrave;"});
											break
										default:
											$("#page_loader").fadeOut(300);
											apprise("Si &egrave; verificato un errore durante il salvataggio dei dati.<br />Per favore, riprova in un secondo momento", {icon: "error", title: "Non riesco a salvare i dati"});
											break;
									}
								}
							});
						});
					}
				});
			}
		} else {
			apprise("Le password non coincidono", {icon: "error", title: "Errore di compilazione dei moduli"});
		}
	}).error(function(data) {
		apprise("Non &egrave; stato possibile rilevare l'albero dei nodi dal MapServer.<br />L'errore riscontrato &egrave; il seguente:<br /><code>" + data.responseText + "</code>", {icon: "error", title: "Ouch!"});
	});
}
$(document).ready(function() {
	$("#register_btn").click(function() {
		$("#page_loader").fadeIn(300);
		check_nodes();
	});
	
	$("html, body").animate({ scrollTop: ($("input").eq(1).offset().top) }, 300);
	$("#user_name").focus();
});