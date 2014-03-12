info = {
	title: (($("#media_title").length > 0) ? $("#media_title").text() : ""),
	director: (($("#media_director").length > 0) ? $("#media_director").text() : ""),
	year: (($("#media_year").length > 0) ? $("#media_year").text() : "")
};
if(info.title.length > 0) {
	$("#semantic_film").prev(".well.text-muted").fadeIn(300);
	
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
	$.get_semantic_data({type: "film", title: info.title}, function(semantic_data) {
		if(semantic_data !== null) {
			var film_data = "",
			obj_size = $.map(semantic_data, function(n, i) { return i; }).length,
			i = 0;
			$("#film_name").text(semantic_data.label);
			
			$.each(semantic_data, function(key, val) {
				if(key != "titolo" && key != "label" && key != "abstract" && key != "commento" && key != "didascalia" && key != "immagine" && key != "depiction" && key != "thumbnail" && key !== "durata") {
					i++;
					if(i == Math.round((obj_size-3)/2)) {
						film_data += '</dl></div><div class="col-lg-6"><dl class="dl-horizontal">';
					}
					if($.strpos(val, "<dt>")) {
						film_data += '<dt>' + $.ucfirst(key.replace(/_/g, " ")) + ':</dt><dd><a data-toggle="collapse" data-parent="#collapse_' + key.replace(/_/g, " ") + '" href="#' + key.replace(/_/g, " ") + '_link"><small><span class="fa fa-caret-down"></span>&nbsp;espandi</small></a></dd><div style="padding-left: 20px; border-left: #ccc 2px solid; border-bottom: #ccc 1px solid;" class="panel-collapse" id="collapse_' + key.replace(/_/g, " ") + '">' + val + '</div>';
					} else {
						film_data += "<dt>" + $.ucfirst(key.replace(/_/g, " ")) + ':</dt><dd class="' + key + '">' + val + "</dd>";
					}
				}
			});
			$("#semantic_film").append('<div class="panel panel-body">' + ((semantic_data.commento != undefined && semantic_data.commento.length > 0) ? semantic_data.commento : (semantic_data.abstract.length > 1) ? semantic_data.abstract : '<span class="text-muted">Non sono presenti descrizioni riguardo all\'autore</span>') + '</div>');
			if(semantic_data.immagine != undefined) {
				$("#semantic_film .panel-body").prepend('<div id="gallery"><a href="' + semantic_data.immagine + '" title="' + ((semantic_data.titolo_in_italiano != undefined) ? semantic_data.titolo_in_italiano : semantic_data.label) + ": " + semantic_data.didascalia.toLowerCase() + '" data-gallery><img alt="' + semantic_data.didascalia + '" src="' + semantic_data.thumbnail + '" class="left" style="max-width: 200px;" /></a></div>');
			}
			$("#semantic_film").append('<div class="panel panel-footer"><div class="col-lg-6"><dl class="dl-horizontal">' + film_data + '</dl></div></div>');
			$("#semantic_film").prev(".well.text-muted").fadeOut(300, function() {
				$("#semantic_film").slideDown(300);
			});
			
			if($(semantic_data.regia).text().length > 0) {
				$("#semantic_director").prev(".well.text-muted").fadeIn(300);
				$.get_semantic_data({type: "person", artist: $(semantic_data.regia).text()}, function(director_data) {
					if(director_data !== null) {
						var artist_data = "",
						obj_size = $.map(director_data, function(n, i) { return i; }).length,
						i = 0;
						$("#director_name").text(director_data.label);
						$("#search_author_ninuxoo").attr("href", "./Cerca:" + director_data.label);
						$.each(director_data, function(key, val) {
							if(key != "label" && key != "abstract"  && key != "commento" && key != "immagine" && key != "depiction" && key != "thumbnail"&& key != "didascalia" && key != "attivita" && key != "altre_attivita"  && key != "nazionalita" && key != "postnazionalita") {
								i++;
								if(i == Math.round((obj_size-3)/2)) {
									artist_data += '</dl></div><div class="col-lg-6"><dl class="dl-horizontal">';
								}
								artist_data += "<dt>" + $.ucfirst(key.replace(/_/g, " ")) + ":</dt><dd>" + val + "</dd>";
							}
						});
						var desc = "<p>" + director_data.nome + " " + director_data.cognome + " &egrave un " + director_data.attivita + " " + ((director_data.altre_attivita != undefined) ? director_data.altre_attivita : "") + " " + director_data.nazionalita + ((director_data.postnazionalita != undefined) ? director_data.postnazionalita : "") + ".</p>";
						$("#semantic_director").append('<div class="panel panel-body">' + desc + ((director_data.commento != undefined && director_data.commento.length > 0) ? director_data.commento : (director_data.abstract.length > 1) ? director_data.abstract : '') + '</div>');
						if(director_data.immagine != undefined || director_data.depiction != undefined) {
							director_data.immagine = (director_data.depiction != undefined) ? director_data.depiction : director_data.immagine;
							$("#semantic_director .panel-body").prepend('<div id="gallery"><a href="' + director_data.immagine + '" title="' + director_data.nome + " " + director_data.cognome + '" data-gallery><img alt="' + director_data.didascalia + '" src="' + director_data.immagine + '" class="left" style="max-width: 200px;" /></a></div>');
						}
						$("#semantic_director").append('<div class="panel panel-footer"><div class="col-lg-6"><dl class="dl-horizontal">' + artist_data + '</dl></div></div>');
						$("#semantic_director").prev(".well.text-muted").fadeOut(300, function() {
							$("#semantic_director").slideDown(300);
						});
					} else {
						$("#semantic_director").prev(".well.text-muted").fadeOut(300);
					}
				});
			}
		} else {
			$("#semantic_film").prev(".well.text-muted").fadeOut(300);
		}
	});
}