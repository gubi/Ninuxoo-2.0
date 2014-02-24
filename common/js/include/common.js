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

$.fn.get_duration = function(options) {
	var options = $.extend({
		timetype: "milliseconds",
		time: $(this).val()
	}, options);
	if(options.timetype == "milliseconds") {
		milliseconds = true;
	} else {
		milliseconds = false;
	}
	var total_time = "";
	if(parseInt(options.time) > 0) {
		var str = [],
		units = [
			{label:"milliseconds",   mod:1000},
			{label:"seconds",   mod:60},
			{label:"minutes",   mod:60},
			{label:"hours",	 mod:24},
			{label:"days",	  mod:7},
			{label:"weeks",	 mod:52}
		],
		duration = new Object();
		if(milliseconds) {
			var x = options.time;
		} else {
			var x = options.time*1000;
		}
		
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
		if(duration.milliseconds > 0) {
			str.push(duration.milliseconds + " millisecond" + ((duration.milliseconds == 1) ? "o" : "i"));
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
		if(milliseconds) {
			total_time =  "millisecond" + ((parseInt(options.time) == 1) ? "o" : "i") + " (" + tott + ")";
		} else {
			total_time =  "second" + ((parseInt(options.time) == 1) ? "o" : "i") + " (" + tott + ")";
		}
	} else {
		total_time = "fino alla chiusura della sessione";
	}
	var final_res = (Math.round(options.time)) ? Math.round(options.time) : 0;
	$(this).val(final_res);
	if($(this).next(".hour").length == 0) {
		$(this).after('&nbsp;&nbsp;<span class="hour">' + total_time + '</span>');
	} else {
		$(this).next(".hour").text(total_time);
	}
}
var knob_options = {knob: {width: 24, height: 24, displayInput: true, stopper: true, readOnly: true, fgColor: "#999"}},
timer = null;
$.fn.setKnobTimeout = function(options, callback, limit) {
	var settings = $.extend({
		start: 0,
		cycle: false,
		knob: {
			stopper: true,
			readOnly: true,
			cursor: "gauge",
			thickness: 0.35,
			lineCap: "butt",
			width: 200,
			height: 200,
			displayInput: false,
			displayPrevious: false,
			fgColor: "#87CEEB",
			inputColor: "",
			font: "Ubuntu",
			fontWeight: "bold",
			step: 1,
			draw: null,
			change: null,
			cancel: null,
			release: null
		}
        }, options),
	$s = $(this),
	seconds = limit/10;
	
	$s.val(settings.start).trigger("change");
	if(settings.start < (seconds)) {
		settings.start++;
		window.clearTimeout(timer);
		
		timer = setTimeout(function() {
			$s.setKnobTimeout({
				start: settings.start,
				cycle: settings.cycle,
				knob: settings.knob
			}, callback, limit);
		}, 1);
	} else {
		if(settings.start == seconds) {
			if(typeof callback == "function") {
				callback.call(this);
			}
		}
		settings.start = 0;
		if(settings.cycle) {
			window.clearTimeout(timer);
			
			timer = setTimeout(function() {
				$s.setKnobTimeout({
					start: settings.start,
					cycle: settings.cycle,
					knob: settings.knob
				}, callback, limit);
			}, 1);
		}
	}
	
	$s.knob({min: 0, max: seconds, stopper: settings.knob.stopper, readOnly: settings.knob.readOnly, cursor: settings.knob.cursor, thickness: settings.knob.thickness, lineCap: settings.knob.lineCap, width: settings.knob.width, height: settings.knob.height, displayInput: settings.knob.displayInput, displayPrevious: settings.knob.displayPrevious, fgColor: settings.knob.fgColor, inputColor: settings.knob.inputColor, font: settings.knob.font, fontWeight: settings.knob.fontWeight, step: settings.knob.step, draw: settings.knob.draw, change: settings.knob.change, cancel: settings.knob.cancel, release: settings.knob.release});
};
$.rawurlencode = function(str) { str = (str+'').toString(); return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A'); };
$.rawurldecode = function(str) { return decodeURIComponent((str + '').replace(/%(?![\da-f]{2})/gi, function () { return '%25'; })); };
$.utf8_to_b64 = function(str) { return window.btoa(unescape(encodeURIComponent(str))); };
$.b64_to_utf8 = function(str) { return decodeURIComponent(escape(window.atob(str))); };
$.ucfirst = function(str) { var firstLetter = str.substr(0, 1); return firstLetter.toUpperCase() + str.substr(1); };
$.strpos = function(haystack, needle, offset) { var i = (haystack + "").indexOf(needle, (offset || 0)); return i === -1 ? false : i; };
$.download = function(url, data, method){ if( url && data ){ data = typeof data == 'string' ? data : jQuery.param(data); var inputs = ''; jQuery.each(data.split('&'), function(){ var pair = this.split('='); inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />'; }); jQuery('<form action="'+ url +'" method="'+ (method||'post') +'">'+inputs+'</form>').appendTo('body').submit().remove(); }; };
$.chat_panel = function(status) {
	if (status == "close") {
		$(".chat_btn").attr("data-original-title", "Apri il pannello delle chat");
		$(".fa-angle-left").fadeIn(300);
		$(".fa-angle-right").fadeOut(300);
		$("#chat").animate({"right": "-" + $("#chat").css("width")}).switchClass("open", "closed");
		$("body").animate({"left": "0px"});
		return "closed";
	} else {
		$(".chat_btn").attr("data-original-title", "Chiudi il pannello delle chat");
		$(".fa-angle-left").fadeOut(300);
		$(".fa-angle-right").fadeIn(300);
		$("#chat").animate({"right": "0px"}).switchClass("closed", "open");
		if($("#chat").hasClass("floating")) {
			$("body").animate({"left": "-" + $("#chat").css("width")});
		}
		$("#chat_message").focus();
		return "open";
	}
}
function setDeceleratingTimeout(callback, factor, times) {
	var internalCallback = function(t, counter) {
		return function() {
			if (--t > 0) {
				window.setTimeout(internalCallback, ++counter * factor);
				callback();
			}
		}
	}( times, 0 );
	window.setTimeout(internalCallback, factor);
};

var password = makeid();
$.makeCryption = function() {
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {});
};

function check_notify(active, autoupdate) {
	$("#check_loader").fadeIn(600);
	$("#dash_notifications").addClass("disabled");
	$("#check_notify_btn").addClass("disabled");
	$("#check_notify_btn span").addClass("fa-spin");
	
	$.ajax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("", password),
			type: "check_notify"
		},
		success: function(response) {
			var notify = false,
			tot = null;
			
			$("#check_loader").fadeOut(900);
			if (response["messages"]["count"] > 0) {
				if(response["messages"]["pid"] !== null && response["messages"]["pid"].length > 0) {
					if($("#send_notice_area .input-group-addon").length == 0) {
						$("#send_notice_area .input-group").prepend('<span class="input-group-addon">#' + response["messages"]["pid"] + '</span>');
					} else {
						$("#send_notice_area .input-group-addon").text("#" + response["messages"]["pid"]);
					}
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
						ip.sort();
						$("#dash_notifications").append('<tr id="' + value["id"] + '" class="notice' + ((value["own"]) ? ' warning' : '') + '"><td>' + ((value["own"]) ? '<a href="javascript:void(0);" title="Rimuovi questa notifica" class="text-danger remove_notice_btn" onclick="remove_notice()"><span class="glyphicon glyphicon-remove"></span></a>' : '') + '</td><td><small>' + ((value["own"]) ? response["messages"]["pid"] : value["id"]) + '</small></td><td><span class="text-primary" title="Indirizz' + (ips > 1 ? 'i' : 'o') + ' di origine' + ((value["ip_hidden"]) ? '<span class=&quot;help-block&quot;>(celati dall\'utente)</span>' : '') + '" data-content="<small>' + ip.join("&emsp;<br />") + '</small>">' + hostname + ((value["own"]) ? ' <span class="text-muted">(tu)</span>' : '') + '</span></td><td>' + value["message"] + '</td></tr>');
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
						if(parseInt($("#notification_refresh").text()) > 6000) {
							$("#check_notify_btn").removeClass("disabled");
							$("#check_notify_btn span").removeClass("fa-spin");
						}
					});
					mark_id_as_read();
				}
				tot = 0;
				$.each(response["messages"]["broadcast"], function(hostname, value) {
					var arr = JSON.parse(localStorage.getItem("read"));
					if(!value["own"]) {
						notify = true;
						//console.log(value["id"], arr);
						//console.log(jQuery.inArray(value["id"], arr));
						if(jQuery.inArray(value["id"], arr) == "-1") {
							tot ++;
						}
					}
				});
				//console.log("tot: ", tot);
				if(tot == null) {
					tot = response["messages"]["count"];
				}
				if(tot == 0) {
					notify = false;
				}
				if(!$("#notify_btn").hasClass("notify")) {
					notify = false;
				}
				if(notify) {
					if($("#notify_btn > span.badge").length > 0) {
						$("#notify_btn > span.badge").text(tot);
					} else {
						$("#notify_btn").append('&nbsp;<span class="badge badge-primary">' + tot + '</span>');
					}
					if($("#dash_notify_btn > span.badge").length > 0) {
						$("#dash_notify_btn > span.badge").text(tot);
					} else {
						$("#dash_notify_btn").append('&nbsp;<span class="badge badge-primary">' + tot + '</span>');
					}
					if($("#top_menu_right .dropdown-toggle > span.badge").length > 0) {
						if($("#top_menu_right .dropdown-toggle > span.badge").text() != tot) {
							$("#top_menu_right .dropdown-toggle > span.badge").animate({"font-size": "2em"}, 300, "easeOutBounce", function() {
								setTimeout(function() {
									$("#top_menu_right .dropdown-toggle > span.badge").text(tot);
								}, 500);
								$(this).delay(1000).animate({"font-size": "0.3em"}, 300, "easeOutBounce");
							});
						} else {
							$("#top_menu_right .dropdown-toggle > span.badge").text(tot);
						}
					} else {
						$("#top_menu_right .dropdown-toggle > .caret").before('<span class="badge badge-primary" style="font-size: 0;">' + tot + '</span>&nbsp;');
						$("#top_menu_right .dropdown-toggle > span.badge").animate({"font-size": "0.3em"}, 300, "easeOutBounce");
					}
				}
			} else {
				if(parseInt($("#notification_refresh").text()) > 6000) {
					$("#check_notify_btn").removeClass("disabled");
					$("#check_notify_btn span").removeClass("fa-spin");
				}
				if($("#notify_btn > span.badge").length > 0) {
					$("#notify_btn > span.badge").remove();
				}
				$("#dash_notifications").html("").append('<tr><td colspan="4" align="center"><span class="info">Nessun messaggio rilevato</span></td></tr>');
			}
		}
	});
	if(autoupdate == undefined) {
		autoupdate = true;
	}
	if(autoupdate) {
		// If user sent a notification the area will be refreshed
		// each 5 seconds for 1 minute, then the refresh returns to 30 seconds
		var time = parseInt($("#notification_refresh").text());
		if(active != undefined && active != null) {
			time = 5000;
			if(active >= 1 && active < 10) { active++; }
			if(active >= 10) { active = null; time = parseInt($("#notification_refresh").text()); }
			if(active == "0") { active = 1; }
			if(active == "true") { active = "0"; }
		} else {
			time = parseInt($("#notification_refresh").text());
		}
		if($(".knob").length > 0) {
			$(".knob").setKnobTimeout(knob_options, function() {
				check_notify(active);
			}, time);
		} else {
			setTimeout(function() {
				check_notify(active);
			}, time);
		}
		$("#notify_btn").click(function() {
			$(this).find("span.badge").remove();
		});
	}
	$("#chat > .panel").resizable({
		handles: "w",
		autoHide: true,
		minWidth: 250,
		maxWidth: Math.round($(document).width()*0.5),
		resize: function(event, ui) {
			if($("#chat").hasClass("floating")) {
				$("body").css({"left": "-" + (ui.size.width - 1) + "px"});
			}
			$("#chat > .panel").css("left", "0");
			$("#chat").css("width", ui.size.width + "px");
			$("#chat_panel_width").text(ui.size.width + "px");
		},
		stop: function() {
			$.ajax({
				url: "common/include/funcs/_ajax/decrypt.php",
				dataType: "json",
				type: "POST",
				data: {
					jCryption: $.jCryption.encrypt("user_username=" + $("#user_email").text() + "&size=" + $("#chat").css("width"), password),
					type: "save_chat_panel_size"
				}
			});
		}
	});
	
	$("#panel_position_btn").tooltip("destroy").tooltip({placement: "bottom", container: "#chat"});
	$("body .chat_btn").tooltip("destroy").tooltip({placement: "auto", container: "body", delay: {show: 1000, hide: 0}});
	$("#chat .chat_btn").tooltip("destroy").tooltip({placement: "auto", container: "#chat", delay: {show: 1000, hide: 0}});
	$("#panel_position_btn").click(function() {
		var panel_window = "";
		
		if($("#chat").hasClass("floating")) {
			panel_window = "fixed";
			$("#panel_position_btn").attr("data-original-title", "Aggancia alla pagina").tooltip("destroy").tooltip({placement: "bottom", container: "#chat"});
			$("#panel_position_btn > span").removeClass("fa-rotate-180");
			$("#chat").switchClass("floating", "fixed");
			$("body").animate({"left": "0px"}, 50);
		} else {
			panel_window = "floating";
			$("#panel_position_btn").attr("data-original-title", "Sgancia dalla pagina").tooltip("destroy").tooltip({placement: "bottom", container: "#chat"});
			$("#panel_position_btn > span").addClass("fa-rotate-180");
			$("#chat").switchClass("fixed", "floating");
			$("body").animate({"left": "-" + $("#chat").css("width")}, 50);
		}
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("user_username=" + $("#user_email").text() + "&panel_window=" + panel_window, password),
				type: "save_chat_panel_window"
			}
		});
	});
	if($("#chat").hasClass("open")) {
		$.chat_panel("open");
	}
	$(".chat_btn").click(function() {
		if($("#chat").hasClass("open")) {
			var panel_status = $.chat_panel("close");
		} else {
			var panel_status = $.chat_panel("open");
		}
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt("user_username=" + $("#user_email").text() + "&status=" + panel_status, password),
				type: "save_chat_panel_status"
			}
		});
	});
	$(".smiley_btn").popover({ 
		html : true,
		placement: "top",
		content: function() {
			return $('#popover_content_wrapper').html();
		}
	});
}
