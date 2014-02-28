// Bootstrap - FontAwesome star rating

$.fn.rating = function(params, callback) {
	$.str_repeat = function(input, multiplier) { var y = ""; while (true) { if (multiplier & 1) { y += input; } multiplier >>= 1; if (multiplier) { input += input; } else { break; }} return y; };
	var settings = $.extend({
		startRate: 0,
		total: 0,
		
		stars: 5,
		textVote: "voto",
		textVotes: "voti",
		readOnly: false,
		readOnlyMessage: "Per votare devi effettuare il login",
		readOnlyLink: "./Accedi"
	}, params);
	
	var rat = "" + settings.startRate,
	split_rate = rat.split("."),
	container = $(this),
	box =  ((settings.readOnly) ? $('<a href="' + settings.readOnlyLink + '" class="text-star" title="' + settings.readOnlyMessage + '"></a>') : $('<span class="text-star"></span>')),
	total_rates = $('<small id="total_rates"></small>'),
	current_poll = $.str_repeat('<small class="fa fa-star"></small>', split_rate[0]) + ((split_rate[1] != null) ? '<small class="fa fa-star-half-o"></small>' : "") + $.str_repeat('<small class="fa fa-star-o"></small>', (settings.stars - Math.ceil(settings.startRate))),
	empty_poll = $.str_repeat('<small class="fa fa-star-o"></small>', settings.stars);
	
	box.html(current_poll);
	total_rates.html("(<small>" + settings.startRate + "</small>/" + settings.total + " " + ((settings.total == 1) ? settings.textVote : settings.textVotes) + ")");
	
	$(this).html(box);
	$(this).append(" ");
	$(this).append(total_rates);
	
	if(!settings.readOnly) {
		var i = 0;
		$(this).hover(function() {
			$(".text-star").css({cursor: "default"});
			
			$.each($(".text-star small"), function(item, value) {
				$(this).hover(function() {
					i = 0;
					selected = item + 1;
					
					$(this).css({cursor: "pointer"}).removeClass("fa-star-o").removeClass("fa-star-half-o").addClass("fa-star").css("font-size", "1.3em");
					$(this).prevAll().each(function() {
						$(this).removeClass("fa-star-o").removeClass("fa-star-half-o").addClass("fa-star").css("font-size", "1em");
					});
					$(this).nextAll().each(function() {
						$(this).removeClass("fa-star-half-o").removeClass("fa-star").addClass("fa-star-o").css("font-size", "1em");
					});
					$(this).click(function() {
						i++;
						var amount = $(this).index() + 1;
						if (callback && typeof(callback) === "function") {
							if(i == 1) {
								callback(amount);
							}
						}
					});
				});
			});
		}, function() {
			$(".text-star").html(current_poll);
		});
	} else {
		box.tooltip();
	}
}