function makeid() {
	var text = "",
	possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
	
	for(var i = 0; i <= 16; i++) {
		text += possible.charAt(Math.floor(Math.random() * possible.length));
	}
	return text;
}
function isFullScreen(cm) {
	return /\bCodeMirror-fullscreen\b/.test(cm.getWrapperElement().className);
}
function winHeight() {
	return window.innerHeight || (document.documentElement || document.body).clientHeight;
}
function setFullScreen(cm, full) {
	var wrap = cm.getWrapperElement(), scroll = cm.getScrollerElement();
	if (full) {
		wrap.className += " CodeMirror-fullscreen";
		scroll.style.height = winHeight() + "px";
		document.documentElement.style.overflow = "hidden";
	} else {
		wrap.className = wrap.className.replace(" CodeMirror-fullscreen", "");
		scroll.style.height = "";
		document.documentElement.style.overflow = "";
	}
	cm.refresh();
}
function get_duration(timeSecs){
	var total_time = "";
	if(parseInt(timeSecs.val()) > 0) {
		var str = [],
		units = [
			{label:"seconds",   mod:60},
			{label:"minutes",   mod:60},
			{label:"hours",	 mod:24},
			{label:"days",	  mod:7},
			{label:"weeks",	 mod:52}
		],
		duration = new Object(),
		x = timeSecs.val();
		
		for (i = 0; i < units.length; i++){
			var tmp = x % units[i].mod;
			duration[units[i].label] = Math.round(tmp);
			x = (x - tmp) / units[i].mod;
		}
		if(duration.weeks > 0) {
			str.push(duration.weeks + " settiman" + ((duration.weeks == 1) ? "a" : "e"));
		}
		if(duration.days > 0) {
			str.push(duration.days + " giorn" + ((duration.days == 1) ? "o" : "i"));
		}
		if(duration.hours > 0) {
			str.push(duration.hours + " or" + ((duration.hours == 1) ? "a" : "e"));
		}
		if(duration.minutes > 0) {
			str.push(duration.minutes + " minut" + ((duration.minutes == 1) ? "o" : "i"));
		}
		if(duration.seconds > 0) {
			str.push(duration.seconds + " second" + ((duration.seconds == 1) ? "o" : "i"));
		}
		
		if(str.length > 0) {
			var tott = "";
			for(var i = 0; i < str.length; i++) {
				if(i == 0) {
					tott = str[i];
				} else {
					if(i < (str.length - 1)) {
						tott += ", " + str[i];
					} else {
						tott += " e " + str[i];
					}
				}
			}
		} else {
			tott = str.join(", ");
		}
		total_time =  "second" + ((parseInt(timeSecs.val()) == 1) ? "o" : "i") + " (" + tott + ")";
	} else {
		total_time = "fino alla chiusura della sessione";
	}
	timeSecs.val(Math.round(timeSecs.val()));
	$("#hour").text(total_time);
}
$.download = function(url, data, method){ if( url && data ){ data = typeof data == 'string' ? data : jQuery.param(data); var inputs = ''; jQuery.each(data.split('&'), function(){ var pair = this.split('='); inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; }); jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>').appendTo('body').submit().remove(); }; };

function check_notify(active) {
	$("#check_loader").fadeIn(600);
	$("#dash_notifications").addClass("disabled");
	$("#check_notify_btn").addClass("disabled");
	var password = makeid();
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt($("#editor_frm").serialize(), password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "check_notify"
			},
			success: function(response) {
				$("#check_loader").fadeOut(600);
				if (response["messages"]["count"] > 0) {
					if(response["messages"]["pid"] !== null && response["messages"]["pid"].length > 0) {
						if($("#send_notice_area .input-group-addon").length == 0) {
							$("#send_notice_area .input-group").prepend('<span class="input-group-addon">#' + response["messages"]["pid"] + '</span>');
						} else {
							$("#send_notice_area .input-group-addon").text("#" + response["messages"]["pid"]);
						}
					}
					if($("#notify_btn > small.badge").length > 0) {
						$("#notify_btn > small.badge").text(response["messages"]["count"]);
					} else {
						$("#notify_btn").append('&nbsp;<small class="badge badge-primary">' + response["messages"]["count"] + '</small>');
					}
					if($("#dash_notifications").length > 0) {
						var ips = 0;
						$("#dash_notifications").html("");
						$.each(response["messages"]["broadcast"], function(hostname, value) {
							var ip = [];
							ips = value["ip"].length;
							$.each(value["ip"], function(ipk, ipv) {
								ip.push(ipv);
							});
							$("#dash_notifications").append('<tr id="' + response["messages"]["pid"] + '" class="notice"><td>' + ((value["own"]) ? '<a href="javascript:void(0);" title="Rimuovi questa notifica" class="text-danger remove_notice_btn" onclick="remove_notice()"><span class="glyphicon glyphicon-remove"></span></a>' : '') + '</td><td><span class="text-primary" title="Indirizz' + (ips > 1 ? 'i' : 'o') + ' di origine" data-content="<small>' + ip.join("&emsp;<br />") + '</small>">' + hostname + ((value["own"]) ? ' <span class="text-muted">(tu)</span>' : '') + '</span></td><td>' + value["message"] + '</td></tr>');
							$("span[title]").popover({placement: "auto", trigger: "hover", html: "true"});
							if(value["own"]) {
								$("#send_previous_notice").val(value["message_raw"]);
								if($("#send_notice").val() == "") {
									$("#send_notice").val(value["message_raw"]);
								}
							}
							if($("#system-search").val() != "") {
								filter($("#system-search"));
							}
							$("#dash_notifications a[title]").tooltip({placement: "auto"});
							$("#dash_notifications").removeClass("disabled");
							$("#check_notify_btn").removeClass("disabled");
						});
					}
				} else {
					$("#check_notify_btn").removeClass("disabled");
					if($("#notify_btn > small.badge").length > 0) {
						$("#notify_btn > small.badge").remove();
					}
					$("#dash_notifications").html("").append('<tr><td colspan="3" align="center"><span class="info">Nessun messaggio rilevato</span></td></tr>');
				}
			}
		});
	});
	// If user sent a notification the area will be refreshed
	// each 5 seconds for 1 minute, then the refresh returns to 30 seconds
	var time = 30000;
	if(active != undefined && active != null) {
		time = 5000;
		if(active >= 1 && active < 10) { active++; }
		if(active >= 10) { active = null; time = 30000; }
		if(active == "0") { active = 1; }
		if(active == "true") { active = "0"; }
	} else {
		time = 30000;
	}
	setTimeout(function() {
		check_notify(active);
	}, time);
	$("#notify_btn").click(function() {
		$(this).find("small.badge").remove();
	});
}
