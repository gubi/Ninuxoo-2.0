// Check internet connection...
function check_internet() {
	$.get("common/include/funcs/_ajax/check_internet_status.php", {check: "true"}, function(data) {
		if(data == "disabled") {
			if($("#show_form[disabled]")) {
				$("#show_form").attr("disabled", "disabled");
				$("#install").attr("disabled", "disabled");
				if($("#form_loaded").length == 0) {
					$("#alert_no_internet").html("Non &egrave; stato possibile connettersi ad internet. Controllare la connessione di questo dispositivo...").show();
				} else {
					$("#calculate_meteo_data_span").show_connection_error();
					$("#meteo_city").show_connection_error();
					$("#meteo_region").show_connection_error();
					$("#meteo_country").show_connection_error();
					$("#meteo_lat").show_connection_error();
					$("#meteo_lng").show_connection_error();
					$("#meteo_owid").show_connection_error();
					$("#meteo_altitude_mt").show_connection_error();
					$("#meteo_altitude_ft").show_connection_error();
					$("#meteo_altitude_unit").show_connection_error();
					$("#install_meteo").show_connection_error();
				}
			}
			setTimeout(function() {
				check_internet();
			}, 15000);
		} else if(data == "ok") {
			if($("#show_form[disabled]")) {
				$("#show_form").attr("disabled", false);
				
					$("#calculate_meteo_data_span").hide_connection_error();
					$("#meteo_city").hide_connection_error();
					$("#meteo_region").hide_connection_error();
					$("#meteo_country").hide_connection_error();
					$("#meteo_lat").hide_connection_error();
					$("#meteo_lng").hide_connection_error();
					$("#meteo_owid").hide_connection_error();
					$("#meteo_altitude_mt").hide_connection_error();
					$("#meteo_altitude_ft").hide_connection_error();
					$("#meteo_altitude_unit").hide_connection_error();
					$("#install_meteo").hide_connection_error();
				$(".no_connection").remove();
				
				$("#install").attr("disabled", false);
				$("#alert_no_internet").text("").hide();
			}
			
			if($("#check_nodes").length == 0) {
				get_nodes();
			}
			setTimeout(function() {
				check_internet();
			}, 30000);
		}
	});
}
function ucfirst(str) {
	var firstLetter = str.substr(0, 1);
	return firstLetter.toUpperCase() + str.substr(1);
}
function get_nodes() {
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
					var selected = $(e.target).val();
					if(selected.length > 0) {
						$.each(nodes, function(k, v) {
							if(selected == v.slug) {
								$("#switch-button-background").mousedown();
								
								$("#node_map").val("http://map.ninux.org/select/" + v.slug + "/").attr("disabled", false);
								$("#node_type").val(v.type).attr("disabled", "disabled");
								
								calculate_meteo_data(v.lat, v.lng);
								$("#tmp_lat").val(v.lat);
								$("#tmp_lng").val(v.lng);
							}
						});
						$("#node_type").trigger("liszt:updated");
						if($("#form.frm").css("display") != "none") {
							$("#nas_name").focus();
						}
						$("#nas_name").focus();
					} else {
						$("#map_lat").val();
						$("#map_lng").val();
						$("#node_map").val("").attr("disabled", "disabled");
						$("#node_type").val("1").attr("disabled", "disabled");
						$("#meteo_name").val("");
						$("#meteo_city").val("");
						$("#meteo_region").val("");
						$("#meteo_lat").val("");
						$("#meteo_lng").val("");
						$("#meteo_owid").val("");
						$("#meteo_altitude_mt").val("");
						$("#meteo_altitude_ft").val("");
						$("#node_type").trigger("liszt:updated");
					}
				});
			}
		});
	}
}
function get_samba(remote_nas) {
	$("#smb_conf_dir_error").remove();
	$("#smb_conf_dir").after('<span id="lloader">&nbsp;&nbsp;&nbsp;&nbsp;<img src="common/media/img/loader.gif" width="16" /> Controllo la configurazione SAMBA...</span>');
	if (remote_nas == undefined) {
		var remote_nas = "";
	}
	var password = makeid();	
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt(remote_nas, password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "get_samba"
			},
			success: function(result) {
				$("#lloader").remove();
				if(result["error"] == false) {
					$.each(result["smb"], function(item, data) {
						if(data["valid_smb_conf"]) {
							var paths = "";
							$("#remote_nas").attr("checked", true);
							$("#smb_conf_dir").val("/mnt/NAS/");
							$("#info_mount_btn").show();
							$.each(data["smb_shares"], function(key, val) {
								paths += val + "\n";
							});
							$("#smb_conf_paths").val(paths).attr("disabled", false);
							$("#smb_conf_dir_error").remove();
						}
					});
				} else {
					$("#info_mount_btn").hide();
					$("#remote_nas").attr("checked", false);
					$("#smb_conf_dir").after('<span id="smb_conf_dir_error" class="error">&nbsp;&nbsp;&nbsp;&nbsp;' + result["error"] + '</span>');
					$("smb_conf_paths").attr("disabled", false);
				}
			}
		});
	});
}
function calculate_meteo_data(latitude, longitude) {
	String.prototype.multi_replace = function (hash) {
		var str = this, key;
		for (key in hash) {
			str = str.replace(new RegExp(key, 'g'), hash[key]);
		}
		return str;
	};
	
	$("#meteo_lat").attr("disabled", false).val(latitude);
	$("#meteo_lng").attr("disabled", false).val(longitude);
	
	$.get("common/include/funcs/_ajax/read_json.php?uri=http://nominatim.openstreetmap.org/reverse?format=json%26lat=" + latitude + "%26lon=" + longitude, function(geodata) {
		var hash = {
			"Via dello": "",
			"Via della": "",
			"Via dei": "",
			"Via degli": "",
			"Via delle": "",
			"Via del": "",
			"Via di": "",
			"Viale dello": "",
			"Viale della": "",
			"Viale dei": "",
			"Viale degli": "",
			"Viale delle": "",
			"Viale del": "",
			"Viale": "",
			"Via": "",
			"Vicolo": ""
		};
		var regioni = new Array();
		regioni["ABR"] = "Abruzzo";
		regioni["BAS"] = "Basilicata";
		regioni["CAL"] = "Calabria";
		regioni["CAM"] = "Campania";
		regioni["EMI"] = "Emilia-Romagna";
		regioni["EMR"] = "Emilia-Romagna";
		regioni["ERO"] = "Emilia-Romagna";
		regioni["FVG"] = "Friuli-Venezia Giulia";
		regioni["FRI"] = "Friuli-Venezia Giulia";
		regioni["LAZ"] = "Lazio";
		regioni["LIG"] = "Liguria";
		regioni["LOM"] = "Lombardia";
		regioni["MAR"] = "Marche";
		regioni["MOL"] = "Molise";
		regioni["PIE"] = "Piemonte";
		regioni["PUG"] = "Puglia";
		regioni["SAR"] = "Sardegna";
		regioni["SIC"] = "Sicilia";
		regioni["TOS"] = "Toscana";
		regioni["TAA"] = "Trentino-AltoAdige";
		regioni["TRE"] = "Trentino-Alto] Adige";
		regioni["UMB"] = "Umbria";
		regioni["VDA"] = "Valle d'Aosta";
		regioni["VAO"] = "Valle d'Aosta";
		regioni["VEN"] = "Veneto";
		var zona = (geodata["address"].suburb != undefined) ? geodata["address"].suburb : (geodata["address"].bus_stop != undefined) ? geodata["address"].bus_stop.replace(/via /gi, "") : geodata["address"].road.multi_replace(hash);
		$("#meteo_name").val("Meteo " + $.trim($("#node_name").val()) + " (" + geodata["address"].city + " ~ " + $.trim(zona) + ")");
		$("#meteo_city").attr("disabled", false).val(geodata["address"].city);
		$("#meteo_region").attr("disabled", false).val(regioni[geodata["address"].state]);
		$("#meteo_country").attr("disabled", false).val(geodata["address"].country);
		
		$.get("common/include/funcs/_ajax/read_json.php?uri=http://openweathermap.org/data/2.1/find/name?q=" + geodata["address"].city, function(data) {
			$("#meteo_owid").attr("disabled", false).val(data.list[0].id);
		}, "json");
		$.get("common/include/funcs/_ajax/read_json.php?uri=http://www.earthtools.org/height/" + latitude + "/" + longitude, function(heightdata) {
			var xml = heightdata,
			xmlDoc = $.parseXML(xml),
			$xml = $(xmlDoc),
			$height_mt = $xml.find("meters"),
			$height_ft = $xml.find("feet");
			$("#meteo_altitude_mt").attr("disabled", false).val($height_mt.text());
			$("#meteo_altitude_ft").attr("disabled", false).val($height_ft.text());
			$("#meteo_altitude_unit").attr("disabled", false).trigger("liszt:updated");
		});
	}, "json");
}
function makeid() {
	var text = "",
	possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	
	for(var i = 0; i <= 16; i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return text;
}
function install() {
	if($("#pgp_pubkey").val() != "") {
		$("#pgp_pubkey").removeClass("error");
		if($("#user_password").val() == $("#user_password2").val()) {
			$("#user_password").removeClass("error");
			$("#user_password2").removeClass("error");
			if($("#node_name").val().length > 0 && $("#nas_name").val().length > 0 && $("#nas_description").val().length > 0 && $("#meteo_name").val().length > 0) {
				$("#node_name").removeClass("error");
				$("#nas_name").removeClass("error");
				$("#nas_description").removeClass("error");
				$("#meteo_name").removeClass("error");
				
				$("#setup_loader > h1").text("Installazione di Ninuxoo...");
				$("#setup_loader").fadeIn(450, function() {
					$("#setup_loader > span").text("Creazione del file di config...");
					var password = makeid();
					
					$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
						var encryptedString = $.jCryption.encrypt($("#content > form").serialize(), password);
						
						$.ajax({
							url: "common/include/funcs/_ajax/decrypt.php",
							dataType: "json",
							type: "POST",
							data: {
								jCryption: encryptedString,
								type: "install"
							},
							success: function(response) {
								if (response["data"] !== "ok") {
									var risp = response["data"].split("::");
									if(risp[0] == "error") {
										alert("Si &egrave; verificato un errore durante l'installazione:\n" + risp[1], {icon: "error", title: "Ouch!"});
									}
								} else {
									$("#setup_loader > span").text("Scansione dei files...");
									$.get("scan.php", {ajax: "true"}, function(scan_return) {
										if($.trim(scan_return) == "done.") {
											alert("Ho creato il file \"config.ini\".<br />Ho salvato i tuoi parametri di accesso e la chiave pubblica PGP.<br />Ho creato il file di connessione al database.<br />Ho configurato il cronjob e fatto una scansione dei files...<br /><br />Non resta che ricaricare la pagina e Ninuxoo &egrave; pronto per l'uso...<br />Grazie per la pazienza <tt>:)</tt>", {icon: "success", title: "Installazione avvenuta con successo!", textOk: "Prego"}, function(r) {
												if(r) {
													location.reload();
												}
											});
										} else {
											alert("Si &egrave; verificato un errore durante l'installazione:\n" + scan_return, {icon: "error", title: "Ouch!"});
										}
									});
								}
							}
						});
					}, function() {
						$("#setup_loader").fadeOut(300);
						$("#setup_loader > h1").text("");
						$("#setup_loader > span").text("");
						alert("Si &egrave; verificato un errore durante l'installazione:\nErrore di autenticazione.", {icon: "error", title: "Ouch!"});
					});
				});
			} else {
				if($("#node_name").val().length == 0) {
					$("#node_name").addClass("error").focus();
				} else if($("#nas_name").val().length == 0) {
					$("#nas_name").addClass("error").focus();
				} else if($("#nas_description").val().length == 0) {
					$("#nas_description").addClass("error").focus();
				} else if($("#meteo_name").val().length == 0) {
					$("#meteo_name").addClass("error").focus();
				}
			}
		} else {
			$("#user_password").addClass("error");
			$("#user_password2").addClass("error");
			apprise("Le password non sono identiche", function(r) {
				if(r) {
					$("#user_password").focus();
				}
			});
		}
	} else {
		$("#pgp_pubkey").addClass("error");
		apprise("Non &egrave; stata inserita la chiave PGP", function(r) {
			if(r) {
				$("#pgp_pubkey").focus();
			}
		});
	}
}
function extractEmails(text) {
	return text.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
}
function getkey() {
	var pu = new getPublicKey($("#pgp_pubkey").val()),
	fingerprint = pu.fp.match(/.{4}/g);
	if(pu.vers == -1) return;
	
	$("#pgp_version").text(pu.vers);
	$("#pgp_user").html('<a href="mailto:' + pu.user + '">' + pu.user.replace("<", "&lt;").replace(">", "&gt;") + '</a>');
	$("#user_username").val(extractEmails(pu.user));
	if(fingerprint != null && fingerprint.length > 2) {
		$("#pgp_fingerprint").text(fingerprint.join(" "));
	} else {
		$("#pgp_fingerprint").text("");
	}
	$("#pgp_key_id").text(pu.keyid);
	$("#pgp_remove_key").show();
	$("#user_password").focus();
	if($("#pgp_pubkey").val() == "") {
		$('#pgp_remove_key').hide();
	}
}