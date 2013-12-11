function check_nas(time) {
	time = parseInt(time);
	
	$("#counter").html(time + ":00 secondi");
	var countdown = time * 60 * 1000,
	timerId = setInterval(function(){
		countdown -= 1000;
		var min = Math.floor(countdown / (60 * 1000)),
		sec = Math.floor((countdown - (min * 60 * 1000)) / 1000),
		timer = min + ":" + sec;
		
		if (countdown <= 0) {
			clearInterval(timerId);
			setTimeout(function() {
				check_nas(time);
			}, 1000);
		}
		if(timer == "0:0") {
			timer = "0";
		}
		$("#counter").html(timer + " secondi");
	}, 1000);
	
	var password = makeid();
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("check=true", password);
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "check_neighbor"
			},
			success: function(response) {
				if(response.data["finded"] > 0) {
					$.each(response.data, function(item, finded) {
						if(item !== "finded") {
							if($("tr#nas_" + item).length == 0) {
								$("#finded_nas tr#no_nas").remove();
								$("#finded_nas").append('<tr id="nas_' + item + '"><td class="status">' + finded["img"] + '</td><td class="hostname"><i>' + finded["hostname"] + '</i><input type="hidden" class="token" value="' + finded["token"] + '" /></td><td class="owner"><a title="Contatta il proprietario di questo NAS" href="mailto:' + finded["owner"]["email"] + '">' + finded["owner"]["key"] + '</a></td><td style="color: #999;">' + finded["geo_zone"] + '</td><td style="color: #999;">' + finded["reachability"] + '</td><td class="mark_btns" style="color: #999;"><table cellpadding="0" cellspacing="0" class="mark"><tr><td class="' + finded["status_t"] + '"></td><td class="' + finded["status_u"] + '"></td></tr></table></td></tr>');
								linked_nas_functions();
							}
						}
					});
				} else {
					$("tr#no_nas span").text("Ancora nessun NAS rilevato nelle vicinanze");
				}
			}
		});
	});
}
function linked_nas_functions() {
	$(".mark td").click(function() {
		var token = $(this).closest("table").attr("id"),
		main = $(this).closest("table").parent("td").parent("tr"),
		image = main.find("td.status img"),
		image_src = main.find("td.status img").attr("src"),
		hostname = main.find("td.hostname").text(),
		token = main.find("td.hostname input.token").val(),
		owner = main.find("td.owner a").attr("href").replace("mailto:", ""),
		current_status = (main.find("td.selected").attr("class") != undefined) ? main.find("td.selected").attr("class").replace("selected", "") : "";
		
		if($(this).hasClass("selected")) {
			$(this).removeClass("selected");
		} else {
			$(this).closest("table").find(".selected").removeClass("selected");
			$(this).addClass("selected");
		}
		switch($(this).attr("class")) {
			case "trusted selected":
				apprise('<p>Il <acronym title="Network Attachewd Storage">NAS</acronym> <b>' + hostname + '</b> appartiene a ' + owner + ', lo conosci?<br />Mandagli un messaggio per farti riconoscere!</p><p>Gli verr&agrave; inviata un\'e-mail e se accettarer&agrave; i vostri NAS si collegheranno.</p>', {title: "Heylà invia un messaggio", icon: "success", textCancel: "Annulla", textOk: "Invia &rsaquo;", message: "true"}, function(r) {
					if(r) {
						$("#page_loader").fadeIn(300);
						var password = makeid();
						$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
							var encryptedString = $.jCryption.encrypt("message=" + r + "&token=" + token, password);
							
							$.ajax({
								url: "common/include/funcs/_ajax/decrypt.php",
								dataType: "json",
								type: "POST",
								data: {
									jCryption: encryptedString,
									type: "trust_nas"
								},
								success: function(response) {
									if (response["data"] !== "ok") {
										var risp = response["data"].split("::");
										if(risp[0] == "error") {
											alert("Si &egrave; verificato un errore durante il processo:\n" + risp[1], {icon: "error", title: "Ouch!"});
										}
									} else {
										/*
										$("#page_loader").fadeOut(300);
										$("#original_name").val($("#config_name").val());
										$("#remove_btn").attr("disabled", false);
										*/
									}
								}
							});
						}, function() {
							$("#page_loader").fadeOut(300);
							alert("Si &egrave; verificato un errore durante il processo.", {icon: "error", title: "Ouch!"});
						});
					} else {
						main.find(".selected").removeClass("selected");
						if(current_status != "") {
							main.find("td." + current_status).addClass("selected");
						}
						image.attr("src", image_src);
					}
				});
				image.attr("src", "common/media/img/mainframe_settings_32_333.png");
				break;
			case "untrusted selected":
				apprise('<p>Il <acronym title="Network Attachewd Storage">NAS</acronym> <b>' + hostname + '</b> ver&agrave; definitivamente scollegato e sar&agrave; marcato come &quot;untrusted&quot;.<br />Non potr&agrave; più effettuare ricerche su questo device n&eacute; acquisire alcun tipo di API.<br />Finch&eacute; sar&agrave; marcato come tale, tutte le richieste di connessione da parte sua<br />verranno rifiutate e non saranno pi&ugrave; notificate.</p><p>Si &egrave; sicuri di voler continuare?</p>', {title: "Rimozione della fiducia", icon: "warning", textCancel: "Annulla", textOk: "Prosegui &rsaquo;", confirm: "true"}, function(r) {
					if(r) {
						apprise('Rimuovo la fiducia al <acronym title="Network Attachewd Storage">NAS</acronym> <b>' + hostname + '</b>...', {progress: "true"});
						
						$("#page_loader").fadeIn(300);
						var password = makeid();
						$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
							var encryptedString = $.jCryption.encrypt("token=" + token, password);
							
							$.ajax({
								url: "common/include/funcs/_ajax/decrypt.php",
								dataType: "json",
								type: "POST",
								data: {
									jCryption: encryptedString,
									type: "untrust_nas"
								},
								success: function(response) {
									if (response["data"] !== "ok") {
										var risp = response["data"].split("::");
										if(risp[0] == "error") {
											$("#page_loader").fadeOut(300);
											$("#aOverlay").fadeOut(300);
											$(".appriseOuter").fadeOut(300);
											alert("Si &egrave; verificato un errore durante il processo:\n" + risp[1], {icon: "error", title: "Ouch!"});
										}
									} else {
										$("#page_loader").fadeOut(300);
										$("#aOverlay").fadeOut(300);
										$(".appriseOuter").fadeOut(300);
									}
								}
							});
						}, function() {
							$("#page_loader").fadeOut(300);
							$("#aOverlay").fadeOut(300);
							$(".appriseOuter").fadeOut(300);
							alert("Si &egrave; verificato un errore durante il processo.", {icon: "error", title: "Ouch!"});
						});
					} else {
						main.find(".selected").removeClass("selected");
						if(current_status != "") {
							main.find("td." + current_status).addClass("selected");
						}
						image.attr("src", image_src);
					}
				});
				image.attr("src", "common/media/img/mainframe_cancel_32_333.png");
				break;
			default:
				apprise('Si &egrave; sicuri di voler demarcare il <acronym title="Network Attachewd Storage">NAS</acronym> <b>' + hostname + '</b>?', {confirm: "true", textOk: "Si", textCancel: "Annulla"}, function(r) {
					if(r) {
						$("#page_loader").fadeIn(300);
						var password = makeid();
						$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
							var encryptedString = $.jCryption.encrypt("token=" + token, password);
							
							$.ajax({
								url: "common/include/funcs/_ajax/decrypt.php",
								dataType: "json",
								type: "POST",
								data: {
									jCryption: encryptedString,
									type: "unmark_nas"
								},
								success: function(response) {
									if (response["data"] !== "ok") {
										var risp = response["data"].split("::");
										if(risp[0] == "error") {
											$("#page_loader").fadeOut(300);
											$("#aOverlay").fadeOut(300);
											$(".appriseOuter").fadeOut(300);
											alert("Si &egrave; verificato un errore durante il processo:\n" + risp[1], {icon: "error", title: "Ouch!"});
										}
									} else {
										$("#page_loader").fadeOut(300);
										$("#aOverlay").fadeOut(300);
										$(".appriseOuter").fadeOut(300);
									}
								}
							});
						}, function() {
							$("#page_loader").fadeOut(300);
							$("#aOverlay").fadeOut(300);
							$(".appriseOuter").fadeOut(300);
							alert("Si &egrave; verificato un errore durante il processo.", {icon: "error", title: "Ouch!"});
						});
					} else {
						main.find(".selected").removeClass("selected");
						main.find("td." + current_status).addClass("selected");
						image.attr("src", image_src);
					}
				});
				image.attr("src", "common/media/img/mainframe_run_32_333.png");
				break;
		}
	});
}