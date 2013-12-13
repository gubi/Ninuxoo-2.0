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