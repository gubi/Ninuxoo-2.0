$.check_internet = function() {
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
				$.check_internet();
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
				$.get_nodes();
			}
			setTimeout(function() {
				$.check_internet();
			}, 30000);
		}
	});
};
$.ucfirst = function(str) {
	var firstLetter = str.substr(0, 1);
	return firstLetter.toUpperCase() + str.substr(1);
};
$.get_nodes = function() {
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
								
								$.calculate_meteo_data(v.lat, v.lng);
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
		}).error(function(data) {
			apprise("Non &egrave; stato possibile rilevare l'albero dei nodi dal MapServer.<br />L'errore riscontrato &egrave; il seguente:<br /><code>" + data.responseText + "</code>", {icon: "error", title: "Ouch!"});
		});
	}
};
$.get_shares = function(remote_nas) {
	if (remote_nas == undefined) {
		var remote_nas = "";
	}
	var password = $.makeid();	
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("file=" + remote_nas, password);
		$("#root_share_dir_refresh_btn > span").addClass("fa-spin");
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "get_shares"
			},
			success: function(result) {
				$("#root_share_dir_refresh_btn > span").removeClass("fa-spin");
				if(!result["alert"]) {
					var paths = "";
					$.each(result["shares"], function(item, data) {
						paths += '<option value="' + data + '" selected>' + data + "</option>\n";
						$("#shared_paths").html(paths).attr("disabled", false).multiselect("rebuild");
						if($("#root_share_dir_error").length > 0) {
							$("#root_share_dir").removeClass("text-block");
						}
						$("#root_share_dir_error").remove();
						$("#root_share_dir").closest(".form-group").removeClass("has-error");
					});
				} else {
					if($("#root_share_dir_error").length == 0) {
						$("#root_share_dir").closest(".form-group").addClass("has-error").after('<span id="root_share_dir_error" class="text-block"><span class="text-danger">' + result["alert"] + '</span></span>');
					}
					$("#shared_paths").val("").attr("disabled", "disabled").multiselect("rebuild");
					apprise(result["alert"], {icon: "warning", title: "Mmmm..."});
				}
			}
		});
	});
};
$.calculate_meteo_data = function(latitude, longitude) {
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
		var hash = {"Via dello": "", "Via della": "", "Via dei": "", "Via degli": "", "Via delle": "", "Via del": "", "Via di": "", "Viale dello": "", "Viale della": "", "Viale dei": "", "Viale degli": "", "Viale delle": "", "Viale del": "", "Viale": "", "Via": "", "Vicolo": ""};
		var regioni = {"ABR": "Abruzzo", "BAS": "Basilicata", "CAL": "Calabria", "CAM": "Campania", "EMI": "Emilia-Romagna", "EMR": "Emilia-Romagna", "ERO": "Emilia-Romagna", "FVG": "Friuli-Venezia Giulia", "FRI": "Friuli-Venezia Giulia", "LAZ": "Lazio", "LIG": "Liguria", "LOM": "Lombardia", "MAR": "Marche", "MOL": "Molise", "PIE": "Piemonte", "PUG": "Puglia", "SAR": "Sardegna", "SIC": "Sicilia", "TOS": "Toscana", "TAA": "Trentino-AltoAdige", "TRE": "Trentino-Alto] Adige", "UMB": "Umbria", "VDA": "Valle d'Aosta", "VAO": "Valle d'Aosta", "VEN": "Veneto"};
		var zona = (geodata["address"].suburb != undefined) ? geodata["address"].suburb : (geodata["address"].bus_stop != undefined) ? geodata["address"].bus_stop.replace(/via /gi, "") : geodata["address"].road.multi_replace(hash);
		$("#meteo_name").val("Meteo " + $.trim($("#node_name").val()) + " (" + geodata["address"].city + " ~ " + $.trim(zona) + ")");
		$("#meteo_zone").attr("disabled", false).val($.trim(zona));
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
};
$.makeid = function() {
	var text = "",
	possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	
	for(var i = 0; i <= 16; i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return text;
};
$.install = function() {
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
					var password = $.makeid();
					
					$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
						var encryptedString = $.jCryption.encrypt($("#install_frm").serialize() + "&shared_paths=" + $("#shared_paths").val().join(","), password);
						
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
										apprise("Si &egrave; verificato un errore durante l'installazione:<br />" + risp[1].replace("\n", "<br />"), {icon: "error", title: "Ouch!"}, function(r) {
											if(r) {
												$("#setup_loader").hide();
											}
										});
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
				} else if($("#root_share_dir").val().length == 0) {
					$("#root_share_dir").closest(".form-group").addClass("has-error");
					$("#root_share_dir").focus();
				} else if($("#shared_paths").val().length == 0) {
					$("#shared_paths").addClass("error").focus();
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
};
$.extractEmails = function(text) {
	return text.match(/([a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.[a-zA-Z0-9._-]+)/gi);
};
$.getkey = function() {
	var pu = new getPublicKey($("#pgp_pubkey").val()),
	fingerprint = pu.fp.match(/.{4}/g);
	if(pu.vers == -1) return;
	
	if($("#pgp_key_results").html().length > 0) {
		$("#pgp_version").text(pu.vers);
		$("#pgp_user").html('<a href="mailto:' + pu.user + '">' + pu.user.replace("<", "&lt;").replace(">", "&gt;") + '</a>');
		$("#user_username").val($.extractEmails(pu.user));
		if(fingerprint != null && fingerprint.length > 2) {
			$("#pgp_fingerprint").html('<span class="fingblock">' + fingerprint.join('</span><span class="fingblock">') + '</span>');
		} else {
			$("#pgp_fingerprint").text("");
		}
		$("#pgp_key_id").text(pu.keyid);
		$("#pgp_remove_key").show();
		if($("#pgp_pubkey").val() == "") {
			$('#pgp_remove_key').hide();
			$("#pgp_key_results").slideUp(300);
			$("#pgp_pubkey").focus();
		} else {
			if(fingerprint != null && fingerprint.length > 2) {
				$("#pgp_key_results").slideDown(300);
				$("#user_password").focus();
			}
		}
	} else {
		$("#user_username").val($.extractEmails(pu.user));
		if(fingerprint != null && fingerprint.length > 2) {
			$("#pgp_fingerprint").html('<span class="fingblock">' + fingerprint.join('</span><span class="fingblock">') + '</span>');
		} else {
			$("#pgp_fingerprint").text("");
		}
		$("#pgp_remove_key").show();
		if($("#pgp_pubkey").val() == "") {
			$('#pgp_remove_key').hide();
			$("#pgp_key_results").slideUp(300);
			$("#pgp_pubkey").focus();
		} else {
			if(fingerprint != null && fingerprint.length > 2) {
				$("#pgp_key_results").html('<hr /><table cellpadding="2" cellspacing="2"><caption><b>Dati ricavati dalla tua chiave pubblica PGP</b></caption><tbody><tr><th>Versione:</th><td id="pgp_version">' + pu.vers + '</td></tr><tr><th>User ID:</th><td id="pgp_user"><a href="mailto:' + pu.user + '">' + pu.user.replace("<", "&lt;").replace(">", "&gt;") + '</a></td></tr><tr><th>Fingerprint:</th><td id="pgp_fingerprint"></td></tr><tr><th>ID della chiave:</th><td id="pgp_key_id">' + pu.keyid + '</td></tr></tbody></table>');
				$("#pgp_key_results").slideDown(300);
				$("#user_password").focus();
			}
		}
	}
};
$(function () {
	'use strict';
	$.fn.show_connection_error = function() {
		if(this.is("input") && this.attr("type") != "checkbox"){
			this.addClass("error").after('<span class="error no_connection">&nbsp;&nbsp;&nbsp;Connessione ad internet assente...</span>');
		}
		this.attr("disabled", "disabled");
		if(this.is("select")){
			this.trigger("liszt:updated");
		}
	};
	$.fn.hide_connection_error = function() {
		this.attr("disabled", false);
		if(this.is("select")){
			this.trigger("liszt:updated");
		}
		if(this.hasClass("error")){
			this.removeClass("error");
		}
	};
	$("#pgp_pubkey").val("");
	$("#node_name").attr("data-placeholder", "Caricamento della lista dei nodi dal MapServer...");
	$("#node_map").val("");
	$("#node_type").val("");
	$("#nas_name").val("");
	$("#nas_description").val("");
	$("#smb_conf_paths").val("");
	$("#shared_paths").multiselect({
		buttonClass: 'btn btn-default',
		maxHeight: 400,
		enableCaseInsensitiveFiltering: true,
		filterPlaceholder: "Filtra...",
		includeSelectAllOption: true,
		selectAllText: "Seleziona tutte",
		selectAllValue: 'multiselect-all',
		buttonWidth: 'auto',
		buttonContainer: '<div class="btn-group" />',
		buttonText: function(options) {
			if (options.length == 0) {
				return 'Nessuna directory selezionata <b class="caret"></b>';
			} else if (options.length > 6) {
				return options.length + ' selezionate <b class="caret"></b>';
			} else {
				var selected = '';
				options.each(function() {
					selected += $(this).text() + ', ';
				});
				return selected.substr(0, selected.length -2) + ' <b class="caret"></b>';
			}
		}
	});
	$("#meteo_name").val("");
	$("#meteo_city").val("");
	$("#meteo_zone").val("");
	$("#meteo_region").val("");
	$("#meteo_country").val("");
	$("#meteo_lat").val("");
	$("#meteo_lng").val("");
	$("#meteo_owid").val("");
	$("#meteo_altitude_mt").val("");
	$("#meteo_altitude_ft").val("");
	$("#node_name").chosen({
		disable_search_threshold: 5,
		no_results_text: "Nessun nodo rilevato per",
		allow_single_deselect: true
	});
	$("#node_type, #meteo_altitude_unit").chosen();
	$("#db_type").chosen({
		disable_search_threshold: 5,
		allow_single_deselect: true
	});
	$.check_internet();
	
	$("#nlloader").show();
	var title = $("title").text();
	$("#nas_name").bind("keyup change", function() {
		if($(this).val().length > 0) {
			$(this).val($.ucfirst($(this).val()));
			$("#header h1").text("Setup (" + $(this).val() + ")");
			$("title").text(title + " (" + $(this).val() + ")");
			$("#nas_description").val("NAS Rete Comunitaria Ninux - " + $(this).val());
		} else {
			$("#header h1").text("Setup");
			$("title").text(title);
			$("#nas_description").val("");
		}
	});
	$("#pgp_pubkey").on('keyup change', function(e){
		$.getkey();
	});
	$("#user_password2").change(function() {
		if($("#user_password").length == 0 || $("#user_password2").length == 0 || $("#user_password").val() != $("#user_password2").val()) {
			$("#user_password").attr("class", "error");
			$("#user_password2").attr("class", "error");
			apprise("Le password non sono identiche", function(r) {
				$("#user_password").focus();
			});
		} else {
			$("#user_password").toggleClass("error", "", 300);
			$("#user_password2").toggleClass("error", "", 300);
		}
	});
	$("#root_share_dir").change(function(){
		$.get_shares($("#root_share_dir").val());
		$(".multiselect-search").focus();
	});
	$("#root_share_dir_refresh_btn").click(function(){
		$.get_shares($("#root_share_dir").val());
	});
	$("#meteo_city").change(function() {
		$.get("common/include/funcs/_ajax/read_json.php?uri=http://openweathermap.org/data/2.1/find/name?q=" + $(this).val(), function(data) {
			$("#meteo_owid").val(data.list[0].id);
		}, "json");
	});
	$("#install").click(function() {
		$.install();
		return false;
	});
	$("#install_meteo").click(function() {
		if($("#install_meteo > span").hasClass("fa-check-square-o")) {
			$("#install_meteo > span").removeClass("fa-check-square-o").addClass("fa-square-o");
			$("#install_meteo_checkbox").attr("checked", false);
			$("#meteo_name").attr("disabled", true);
			$("#meteo_city").attr("disabled", true);
			$("#meteo_zone").attr("disabled", true);
			$("#meteo_region").attr("disabled", true);
			$("#meteo_country").attr("disabled", true);
			$("#meteo_lat").attr("disabled", true);
			$("#meteo_lng").attr("disabled", true);
			$("#meteo_owid").attr("disabled", true);
			$("#meteo_altitude_mt").attr("disabled", true);
			$("#meteo_altitude_ft").attr("disabled", true);
			$("#meteo_altitude_unit").attr("disabled", true).trigger("liszt:updated");
		} else {
			$("#install_meteo > span").removeClass("fa-square-o").addClass("fa-check-square-o");
			$("#install_meteo_checkbox").attr("checked", "checked");
			$("#meteo_name").attr("disabled", false).focus();
			$("#meteo_city").attr("disabled", false);
			$("#meteo_zone").attr("disabled", false);
			$("#meteo_region").attr("disabled", false);
			$("#meteo_country").attr("disabled", false);
			$("#meteo_lat").attr("disabled", false);
			$("#meteo_lng").attr("disabled", false);
			$("#meteo_owid").attr("disabled", false);
			$("#meteo_altitude_mt").attr("disabled", false);
			$("#meteo_altitude_ft").attr("disabled", false);
			$("#meteo_altitude_unit").attr("disabled", false).trigger("liszt:updated");
		}
	});
	$("#install_database").click(function() {
		if($("#install_database > span").hasClass("fa-check-square-o")) {
			$("#install_database > span").removeClass("fa-check-square-o").addClass("fa-square-o");
			$("#install_database_checkbox").attr("checked", false);
			$("#db_type").attr("disabled", true);
			$("#mysql_host").attr("disabled", true);
			$("#mysql_username").attr("disabled", true);
			$("#mysql_password").attr("disabled", true);
			$("#mysql_db_name").attr("disabled", true);
			$("#mysql_db_table").attr("disabled", true);
			$("#db_type").attr("disabled", true).trigger("liszt:updated");
		} else {
			$("#install_database > span").removeClass("fa-square-o").addClass("fa-check-square-o");
			$("#install_database_checkbox").attr("checked", "checked");
			$("#db_type").attr("disabled", false).focus();
			$("#mysql_host").attr("disabled", false);
			$("#mysql_username").attr("disabled", false);
			$("#mysql_password").attr("disabled", false);
			$("#mysql_db_name").attr("disabled", false);
			$("#mysql_db_table").attr("disabled", false);
			$("#db_type").attr("disabled", false).trigger("liszt:updated");
		}
	});
	$("#paranoid_mode").click(function() {
		if($("#node_name").val().length > 0) {
			$("#calculate_meteo_data_span").show();
			$("#meteo_name").attr("disabled", true).val("");
			$("#meteo_city").attr("disabled", true).val("");
			$("#meteo_zone").attr("disabled", true).val("");
			$("#meteo_region").attr("disabled", true).val("");
			$("#meteo_country").attr("disabled", true).val("");
			$("#meteo_lat").attr("disabled", true).val("");
			$("#meteo_lng").attr("disabled", true).val("");
			$("#meteo_owid").attr("disabled", true).val("");
			$("#meteo_altitude_mt").attr("disabled", true).val("");
			$("#meteo_altitude_ft").attr("disabled", true).val("");
			$("#meteo_altitude_unit").attr("disabled", true).trigger("liszt:updated");
			$("#install_meteo").attr("checked", false);
			
			
			$("#calculate_meteo_data").click(function() {
				$("#meteo_name").attr("disabled", false).val("Meteo " + $("#node_name").val());
				
				$.calculate_meteo_data($("#tmp_lat").val(), $("#tmp_lng").val());
				$("#install_meteo").attr("checked", true);
			});
		} else {
			$("#node_name").mousedown();
		}
	});
	$("#show_form").click(function() {
		$(this + ", #abstract").hide();
		$("html, body").animate({ scrollTop: ($("form").offset().top) }, 300);
		$("form.frm").slideDown(300, function() {
			$("body").prepend('<div id="form_loaded" style="display: none;">true</div>');
			$("#node_name").attr("disabled", false);
			$("#pgp_pubkey").focus();
			$.get_shares($("#root_share_dir").val());
		});
		return false;
	});
	$("#show_nas_advanced_options").click(function() {
		$(this).hide();
		$("#nas_advanced_options").slideDown(300);
		return false;
	});
	$("#show_meteo_advanced_options").click(function() {
		$(this).hide();
		$("#meteo_advanced_options").slideDown(300);
		return false;
	});
});
