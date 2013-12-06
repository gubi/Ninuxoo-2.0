// Apprise 1.5 by Daniel Raftery
// http://thrivingkings.com/apprise
//
// Button text added by Adam Bezulski
//
// Cached jQuery variables, position center added by Josiah Ruddell

function apprise(string, args, callback) {
    var default_args =
		{
		    'confirm': false, 		// Ok and Cancel buttons
		    'verify': false, 	// Yes and No buttons
		    'input': false, 		// Text input (can be true or string for default text)
		    'message': false, 		// Textarea (can be true or string for default text)
		    'animate': false, 	// Groovy animation (can true or number, default is 400)
		    'textOk': 'Ok', 	// Ok button default text
		    'textCancel': 'Cancel', // Cancel button default text
		    'textYes': 'Yes', 	// Yes button default text
		    'textNo': 'No', 	// No button default text
		    'position': 'center',// position center (y-axis) any other option will default to 100 top
		    'icon': '',
		     'title': '',
		     'progress': false
		}

    if (args) {
        for (var index in default_args) {
		if (typeof args[index] == "undefined") args[index] = default_args[index];
	}
    }

    var aHeight = $(window).height(),
		aWidth = $(window).width(),
		apprise = $('<div class="appriseOuter"></div>'),
		overlay = $('<div class="appriseOverlay" id="aOverlay"></div>'),
		inner = $('<div class="appriseInner"></div>'),
        buttons = $('<div class="aButtons"></div>'),
		posTop = 100;

    overlay.css({ height: aHeight, width: aWidth })
		.appendTo('body')
        .fadeIn(100,function(){$(this).css('filter','alpha(opacity=70)');});

    apprise.appendTo('body');
	
	if (!args || !args['icon']) {
		inner.append(string).appendTo(apprise);
	}

    

    if (args) {
        if (args['icon']) {
	   if (args['title'].length > 0) {
		var title = '<h2>' + args['title'] + '</h2>';
	    } else {
		 var title = "";
	    }
	    var table_open = '<table cellspacing="0" cellpadding="0"><tr>';
	    var table_close = '</td></tr></table>';
           
	    if (args['icon'] == 'success') {
		if(title == "") {
			title = '<h2>Oh yeah!</h2>';
		}
                inner.prepend(table_open + '<td><img src="common/media/img/accept_128_ccc.png" /></td><td valign="top">' + title + '<p>' + string + '</p>' + table_close).appendTo(apprise);
            }
            if (args['icon'] == 'warning') {
		if(title == "") {
			title = '<h2>Attenzione</h2>';
		}
                inner.prepend(table_open + '<td><img src="common/media/img/warning_128_ccc.png" /></td><td valign="top">' + title + '<p>' + string + '</p>' + table_close).appendTo(apprise);
            }
            if (args['icon'] == 'error') {
		if(title == "") {
			title = '<h2>Si &egrave; verificato un problema</h2>';
		}
                inner.prepend(table_open + '<td><img src="common/media/img/cancel_128_ccc.png" /></td><td valign="top">' + title + '<p>' + string + '</p>' + table_close).appendTo(apprise);
            }
        }
    }
    
    if (args) {
        if (args['input']) {
            if (typeof (args['input']) == 'string') {
                inner.append('<div class="aInput"><input type="text" class="aTextbox" t="aTextbox" value="' + args['input'] + '" /></div>');
            }
            if (typeof (args['input']) == 'object') {
                inner.append($('<div class="aInput"></div>').append(args['input']));
            }
            else {
                inner.append('<div class="aInput"><input type="text" class="aTextbox" t="aTextbox" /></div>');
            }
            $('.aTextbox').focus();
        }
        if (args['message']) {
            if (typeof (args['input']) == 'string') {
                inner.append('<div class="aInput"><textarea rows="5" class="aTextarea" t="aTextarea">' + args['input'] + '"</textarea></div>');
            }
            if (typeof (args['input']) == 'object') {
                inner.append($('<div class="aInput"></div>').append(args['input']));
            }
            else {
                inner.append('<div class="aInput"><textarea rows="5" class="aTextarea" t="aTextarea"></textarea></div>');
            }
            $('.aTextarea').focus();
        }
    }

    inner.append(buttons);
    if (args) {
        if (args['confirm'] || args['input'] || args['message']) {
            buttons.append('<button value="cancel">' + args['textCancel'] + '</button>');
            buttons.append('<button value="ok">' + args['textOk'] + '</button>');
        }
        else if (args['verify']) {
            buttons.append('<button value="cancel">' + args['textNo'] + '</button>');
            buttons.append('<button value="ok">' + args['textYes'] + '</button>');
        }
        else if (args['progress']) {
            buttons.append('<div class="progress progress-striped active"><div style="width: 100%" class="bar bar-warning"></div></div>');
        }
        else { buttons.append('<button value="ok">' + args['textOk'] + '</button>'); }
    }
    else { buttons.append('<button value="ok">Ok</button>'); }

    // position after adding buttons

    if (!args || !args['icon']) {
	apprise.css("left", (($(window).width() - $('.appriseOuter').width()) / 2 + $(window).scrollLeft()) - 10 + "px");
    } else {
	apprise.css("left", ($(window).width() - $('.appriseOuter').width()) / 2 + $(window).scrollLeft() + "px");
    }
    // get center
    if (args) {
        if (args['position'] && args['position'] === 'center') {
            posTop = (aHeight - apprise.height()) / 2;
        }

        if (args['animate']) {
            var aniSpeed = args['animate'];
            if (isNaN(aniSpeed)) { aniSpeed = 400; }
            apprise.css('top', '-200px').show().animate({ top: posTop }, aniSpeed);
        }
        else { apprise.css('top', posTop).fadeIn(200); }
    }
    else { apprise.css('top', posTop).fadeIn(200); }


    $(document).keydown(function (e) {
        if (overlay.is(':visible') && !$(".aTextarea").is(":focus")) {
            if (e.keyCode == 13)
            { $('.aButtons > button[value="ok"]').click(); }
            if (e.keyCode == 27)
            { $('.aButtons > button[value="cancel"]').click(); }
        }
    });

    var aText = $('.aTextbox').val();
    var aText = $('.aTextarea').val();
    if (!aText) { aText = false; }
    if (!aText) { aText = false; }
    $('.aTextbox').keyup(function ()
    { aText = $(this).val(); });
    $('.aTextarea').keyup(function ()
    { aText = $(this).val(); });

    $('.aButtons > button').click(function () {
        overlay.remove();
        apprise.remove();
        if (callback) {
            $(this).text("");
            var wButton = $(this).attr("value");
            if (wButton == 'ok') {
                if (args) {
                    if (args['input'] || args['message']) { callback(aText); }
                    else { callback(true); }
                }
                else {
                    callback(true); 
                }
            }
            else if (wButton == 'cancel') {
                callback(false); 
            }
        }
    });
}
