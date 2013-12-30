$(document).ready(function() {
	$("textarea.editor").pagedownBootstrap({
		"sanatize": false,
		"help": false,
		"hooks": [{
			"event": "preConversion",
			"callback": function (text) {
				$("#wmd-button-group1-0, #wmd-button-group1-1").remove();
				$("#wmd-quote-button-0, #wmd-quote-button-1").remove();
				$("#wmd-code-button-0, #wmd-code-button-1").remove();
				$("#wmd-image-button-0, #wmd-image-button-1").remove();
				$("#wmd-olist-button-0, #wmd-olist-button-1").remove();
				$("#wmd-heading-button-0, #wmd-heading-button-1").remove();
				$("#wmd-hr-button-0, #wmd-hr-button-1").remove();
				return text.replace(/\b(a\w*)/gi, "*$1*");
			}
		}, {
			"event": "postConversion",
			"callback": function (text) {
				var menu_right = $("#top_menu_right").html();
				return '<nav class="navbar navbar-inverse" role="navigation"><div class="collapse navbar-collapse">' + text.replace("ul", 'ul class="nav navbar-nav"') + menu_right + '</div></nav>';
			}
		}, {
			"event": "plainLinkText",
			"callback": function (url) {
				return "This is a link to " + url.replace(/^https?:\/\//, "");
			}
		}]
	});
	$(".preview_btn").toggle(function() {
		$(".wmd-button-bar").fadeOut(300);
		$(".wmd-input").fadeOut(300, function() {
			$(".wmd-preview").fadeIn(300);
		});
		$(".preview_btn").html('Modifica&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-edit"></span>');
		//$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	}, function() {
		$(".wmd-preview").fadeOut(300, function() {
			$(".wmd-button-bar").fadeIn(300);
			$(".wmd-input").fadeIn(300);
		});
		$(".preview_btn").html('Anteprima&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-eye-open"></span>');
		$("html, body").animate({ scrollTop: ($("h1").eq(1).offset().top) }, 300);
	});
	
	window.onbeforeunload = function(){ return 'onbeforeunload' };
});