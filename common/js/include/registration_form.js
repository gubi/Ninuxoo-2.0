function save() {
	$("#nlloader").show();
	if($("#user_password").val() == $("#user_password2").val()) {
		var nodename = $("#node_name").val().toLowerCase();
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt($("#registration_frm").serialize(), password),
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
	} else {
		apprise("Le password non coincidono", {icon: "error", title: "Errore di compilazione dei moduli"});
	}
}
function get_nodes() {
	$("#nlloader").show();
	if($("#checking_nodes").length == 0 || $("checking_nodes").text() == "false") {
		$("body").prepend('<div id="checking_nodes" style="display: none;">true</div>');
		
		$.ajax({
			url: "common/include/funcs/_ajax/install.get_nodes.php",
			dataType: 'json'
		}).done(function(source) {
			if(source["error"] == "no file") {
				$("#check_nodes").remove();
			} else {
				$("body").prepend('<div id="check_nodes" style="display: none;">true</div>');
				$("#checking_nodes").text("false");
				$("#nlloader").hide();
				$("#node_name").attr("disabled", false);
				$("#node_name").data("chosen").default_text = "Trova un nodo...";
				$("#node_name_chzn > .chzn-single");
				$("#node_name").trigger("liszt:updated");
				
				var nodes = $.map(source, function (key, value) {
					$("#node_name").append('<option value="' + value + '">' + $.trim(key["name"]) + '</option>');
					$("#node_name").trigger("liszt:updated");
					return { value: $.trim(key["name"]), slug: value, type: key["type"], lat: key["lat"], lng: key["lng"] };
				});
				$("#node_name").change(function(e) {
					$("#user_username").focus();
				});
			}
		}).error(function(data) {
			apprise("Non &egrave; stato possibile rilevare l'albero dei nodi dal MapServer.<br />L'errore riscontrato &egrave; il seguente:<br /><code>" + data.responseText + "</code>", {icon: "error", title: "Ouch!"});
		});
	}
}
$(document).ready(function() {
	if($("#check_nodes").length == 0) {
		get_nodes();
	}
	$("#node_name").chosen({no_results_text: "Nessun nodo per"});
	
	$("html, body").animate({ scrollTop: ($("input").eq(1).offset().top) }, 300);
	$("#user_name").focus();
});