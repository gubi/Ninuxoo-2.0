info = {
	title: (($("#media_title").length > 0) ? $("#media_title").text() : ""),
	artist: (($("#media_artist").length > 0) ? $("#media_artist").text() : ""),
	album: (($("#media_album").length > 0) ? $("#media_album").text() : ""),
	year: (($("#media_year").length > 0) ? $("#media_year").text() : ""),
	length: (($("#media_length").length > 0) ? $("#media_length").text() : ""),
	track: (($("#media_track").length > 0) ? $("#media_track").text() : ""),
	genre: (($("#media_genre").length > 0) ? $("#media_genre").text() : ""),
	comments: (($("#media_comments").length > 0) ? $("#media_comments").text() : "")
};
$("#audio_spectrum").prev(".well.text-muted").fadeIn(300);
$.get("common/include/lib/php-waveform-svg.php", {file: $("#hash").text()}, function(waveform) {
	if(waveform !== "no file") {
		$("#audio_spectrum").prev(".well.text-muted").fadeOut(300, function() {
			$("#audio_spectrum").html('<div style="width: 100%; height: 5em; padding: 0 10px; border: #ddd 1px solid;">' + waveform + '</iframe>').fadeIn(600);
		});
	}
}, "text");

if(info.title.length > 0) {
	$("#semantic_album").prev(".well.text-muted").fadeIn(300);
	
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
	$.get_semantic_data({type: "album", title: info.title}, function(semantic_data) {
		if(semantic_data !== null) {
			var album_data = "",
			obj_size = $.map(semantic_data, function(n, i) { return i; }).length,
			i = 0;
			$("#album_name").text(semantic_data.label);
			$.each(semantic_data, function(key, val) {
				if(key != "label" && key != "abstract"  && key != "commento" && key != "immagine") {
					i++;
					if(i == Math.round((obj_size-3)/2)) {
						album_data += '</dl></div><div class="col-lg-6"><dl class="dl-horizontal">';
					}
					album_data += "<dt>" + $.ucfirst(key.replace(/_/g, " ")) + ":</dt><dd>" + val + "</dd>";
				}
			});
			$("#semantic_album").append('<div class="panel panel-body">' + ((semantic_data.commento != undefined) ? semantic_data.commento : semantic_data.abstract) + '</div>');
			if(semantic_data.immagine != undefined) {
				$("#semantic_album .panel-body").prepend('<div id="gallery"><a href="' + semantic_data.immagine + '" title="' + ((semantic_data.titolo_in_italiano != undefined) ? semantic_data.titolo_in_italiano : semantic_data.label) + ": " + semantic_data.didascalia.toLowerCase() + '" data-gallery><img alt="' + semantic_data.didascalia + '" src="' + semantic_data.thumbnail + '" class="left" style="max-width: 200px;" /></a></div>');
			}
			$("#semantic_album").append('<div class="panel panel-footer"><div class="col-lg-6"><dl class="dl-horizontal">' + album_data + '</dl></div></div>');
			$("#semantic_album").prev(".well.text-muted").fadeOut(300, function() {
				$("#semantic_artist").after("<hr />");
				$("#semantic_album").slideDown(300);
			});
			
			
			if(semantic_data.artista.length > 0) {
				$("#semantic_artist").prev(".well.text-muted").fadeIn(300);
				$.get_semantic_data({type: "person", artist: semantic_data.artista}, function(semantic_data) {
					if(semantic_data !== null) {
						var artist_data = "",
						obj_size = $.map(semantic_data, function(n, i) { return i; }).length,
						i = 0;
						$("#artist_name").text(semantic_data.label);
						$.each(semantic_data, function(key, val) {
							if(key != "label" && key != "abstract"  && key != "commento" && key != "immagine") {
								i++;
								if(i == Math.round((obj_size-3)/2)) {
									artist_data += '</dl></div><div class="col-lg-6"><dl class="dl-horizontal">';
								}
								artist_data += "<dt>" + $.ucfirst(key.replace(/_/g, " ")) + ":</dt><dd>" + val + "</dd>";
							}
						});
						$("#semantic_artist").append('<div class="panel panel-body">' + ((semantic_data.commento != undefined && semantic_data.commento.length > 0) ? semantic_data.commento : (semantic_data.abstract.length > 1) ? semantic_data.abstract : '<span class="text-muted">Non sono presenti cenni riguardo all\'autore</span>') + '</div>');
						if(semantic_data.immagine != undefined) {
							$("#semantic_artist .panel-body").prepend('<img src="' + semantic_data.immagine + '" class="left" style="max-width: 200px;" />');
						}
						$("#semantic_artist").append('<div class="panel panel-footer"><div class="col-lg-6"><dl class="dl-horizontal">' + artist_data + '</dl></div></div>');
						$("#semantic_artist").prev(".well.text-muted").fadeOut(300, function() {
							$("#semantic_artist").slideDown(300);
						});
					} else {
						$("#semantic_artist").prev(".well.text-muted").fadeOut(300);
					}
				});
			}
		} else {
			$("#semantic_album").prev(".well.text-muted").fadeOut(300);
		}
	});
}