info = {
	title: (($("#media_title").length > 0) ? $("#media_title").text() : ""),
	artist: (($("#media_artist").length > 0) ? $("#media_artist").text() : "")
};
var readonly = (($("#user").text().length > 0) ? false : true);
$.get_rating(info, function(rates) {
	$("#rating").rating({
		startRate: rates.medium_rates,
		total: rates.total,
		readOnly: readonly
	}, function(selected) {
		var infoo = info;
		infoo.rate = selected;
		infoo.user = $("#user").text();
		
		$.cryptAjax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: $.jCryption.encrypt($.param(infoo), password),
				type: "set_rating"
			},
			success: function(data) {
				$("#rating").html("").rating({
					startRate: data.medium_rates,
					total: data.total,
					readOnly: readonly
				});
			}
		});
	});
});