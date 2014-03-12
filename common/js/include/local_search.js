$.ultrie = function(resourcetrie) {
	var a = {};
	$.each(resourcetrie.children, function(index, child) {
		a[index] = {
			rank: child.rank,
			label: child.label,
			uri: child.uri,
			resources: child.resources,
			child: $.ultrie(child),
		};
	});
	return a;
};
$.explode_ultrie = function(resourcetrie) {
	var li = "",
	path_link = "";
	$.each(resourcetrie, function(rank, obj) {
		li += '<li><span class="fa fa-folder-o">&nbsp;&nbsp;' + $.explode_a_ultrie(obj);
	});
	return li;
};
$.explode_a_ultrie = function(resourcetrie) {
	var path_link = "",
	icon = "";
	if(resourcetrie.resources.length == 0) {
		if(Object.keys(resourcetrie.child).length > 1) {
			path_link += '<a href="./Esplora:?' + resourcetrie.uri + '">' + resourcetrie.label + '</a><tt> / </tt></span><ul>';
			$.each(resourcetrie.resources, function(r, res) {
				path_link += '<li><span class="' + res.icon + '">&nbsp;<a href="./Scheda:?' + res.uri + '">' + res.filename + '</a></span></li>';
			});
			$.each(resourcetrie.child, function(rank, obj) {
				path_link += '<li><span class="fa fa-folder-o">&nbsp;&nbsp;' + $.explode_a_ultrie(obj);
			});
			path_link += '</ul></li>';
		} else {
			path_link += '<a href="./Esplora:?' + resourcetrie.uri + '"> ' + resourcetrie.label + '</a><tt> / </tt>';
			$.each(resourcetrie.child, function(rank, obj) {
				path_link += $.explode_a_ultrie(obj);
			});
		}
	} else {
		if(Object.keys(resourcetrie.child).length > 1) {
			path_link += '<a href="./Esplora:?' + resourcetrie.uri + '">' + resourcetrie.label + '</a><tt> / </tt></span><ul>';
			$.each(resourcetrie.resources, function(r, res) {
				path_link += '<li><span class="' + res.icon + '">&nbsp;<a href="./Scheda:?' + res.uri + '">' + res.filename + '</a></span></li>';
			});
			$.each(resourcetrie.child, function(rank, obj) {
				path_link += '<li><span class="fa fa-folder-o">&nbsp;&nbsp;' + $.explode_a_ultrie(obj);
			});
			path_link += '</ul></li>';
		} else {
			path_link += '<a href="./Esplora:?' + resourcetrie.uri + '">' + resourcetrie.label + '</a><tt> / </tt></span>';
			path_link += '<ul>';
			$.each(resourcetrie.resources, function(r, res) {
				path_link += '<li><span class="' + res.icon + '">&nbsp;<a href="./Scheda:?' + res.uri + '">' + res.filename + '</a></span></li>';
			});
			path_link += "</ul></li>";
		}
	}
	return path_link;
};
$.get_rating = function(params, callback) {
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt($.param(params), password),
			type: "get_rating"
		},
		success: function(data) {
			if (callback) {
				callback(data);
			}
		}
	});
}
$.get_script = function(script, callback) {
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "script",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("", password),
			type: script
		},
		success: function(data) {
			if (callback) {
				console.load(data);
			}
		}
	});
}
$.get_semantic_data = function(params, callback) {
	$.get("common/include/funcs/_ajax/get_semantic_data.php", params, function(semantic_d) {
		if (callback) {
			$.each(semantic_d, function(count, semantic_data) {
				callback(semantic_data);
			});
		}
	}, "json");
}
$.decount = function(time, callback) {
	time = parseInt(time);
	
	var minutes_txt = "minuti";
	if(time == 1) {
		minutes_txt = "minuto";
	}
	$(".counter").html(time + ":00 " + minutes_txt);
	var countdown = time * 60 * 1000,
	timerId = setInterval(function(){
		countdown -= 1000;
		var min = Math.floor(countdown / (60 * 1000)),
		sec = Math.floor((countdown - (min * 60 * 1000)) / 1000),
		timer = min + ":" + sec;
		
		if (countdown <= 0) {
			clearInterval(timerId);
			setTimeout(function() {
				$.decount(time);
			}, 1000);
		}
		if(timer == "0:0") {
			timer = "0";
			if (callback) {
				callback();
			}
		}
		$(".counter").html(timer + " secondi");
		
	}, 1000);
}
$(document).ready(function() {
	var s = "";
	
	switch($("#result_type").text()) {
		case "Search":
			$("#breadcrumb").hide();
			break;
		default:
			var info = {};
			switch($("#result_filetype").text()) {
				case "ebook":
					$.get_script("local_search.semantic_data.book");
					break;
				case "image":
					$.get_script("local_search.semantic_data.image");
					break;
				case "audio":
					$.get_script("local_search.semantic_data.audio");
					break;
				case "video":
					$.get_script("local_search.semantic_data.video");
					break;
				default:
					break;
			}
			$("#semantic_info").prev(".well.text-muted").fadeIn(300);
			$.get_semantic_data({title: $("#result_semantic").text(), type: "thing"}, function(semantic_data) {
				$("#file_info").html('<dl class="dl-horizontal"></dl>');
				$.each(semantic_data, function(item, value) {
					if(semantic_data !== null) {
						if(item != "label" && item != "abstract"  && item != "commento" && item != "immagine") {
							$("#semantic_info").prev(".well.text-muted").fadeOut(300);
							$("#file_info dl").append('<dt>' + $.ucfirst(item.replace(/_/g, " ")) + ':</dt><dd>' + value + '</dd>');
							$("#semantic_info").fadeIn(300);
						}
					} else {
						$("#file_info").prev(".well.text-muted").fadeOut(300);
					}
				});
				$("#s_label").text(semantic_data.label);
				$("#semantic_results").html(((semantic_data.commento.length > 0) ? semantic_data.commento : semantic_data.abstract));
				if(semantic_data.immagine != undefined) {
					$("#semantic_results").prepend('<img src="' + semantic_data.immagine + '" style="width: 100px; vertical-align: top; border: #ccc 1px solid;" class="left" />');
				}
				if(semantic_data.abstract != undefined) {
					$("#semantic_results").append((semantic_data.commento.length > 0) ? '<br /><br /><br /><div class="panel"><div class="panel-heading"><a data-toggle="collapse" data-parent="#accordion" href="#collapseOne">Approfondimenti <span class="caret"></span></a></div><div id="collapseOne" class="panel-collapse collapse"><div class="panel-body">' + semantic_data.abstract + '</div></div></div>' : "");
				}
				$("#semantic_info").prev(".well.text-muted").fadeOut(300, function() {
					$("#semantic_info").slideDown(300);
				});
			});
			
			$("#breadcrumb").show();
			
			$("html, body").animate({ scrollTop: $("#container").offset().top }, 300);
			break;
	}
	
	$.cryptAjax({
		url: "common/include/funcs/_ajax/decrypt.php",
		dataType: "json",
		type: "POST",
		data: {
			jCryption: $.jCryption.encrypt("q=" + $("#search_term").text() + "&op=" + $("#search_type").text() + "&nresults=" + $("#search_num_results").text() + "&path=" + $("#search_path").text() + "&filetype=" + $("#search_filetype").text(), password),
			type: "local_search"
		},
		success: function(data) {
			if(data.nresults > 0){
				$("#nlabels").text(data.nlabels);
				$("#nresults").text(($("#result_type").text() == "View" ? (data.nresults - 1) : data.nresults));
				$("#searchtime").text(Math.round(data.searchtime*1000)/1000 + " secondi");
				
				index_count = 0;
				$.each(data.results, function(index, value) {
					index_count++;
					
					if(index == 0) {
						if($("#result_type").text() == "Search" || ($("#result_type").text() == "View" && (data.nresults - 1) > 0)) {
							$("#search_results").html('<div class="search_results"><ul id="treeview_' + index + '" class="exactresults filetree treeview"></ul><div id="otherresults"></div></div>');
						} else {
							$("#search_results").html('<p class="alert alert-success text-centered"><span class="fa fa-check"></span>&nbsp;&nbsp;Non sono stati trovati duplicati per questo file</p>');
						}
						var start_collapsed = false;
						
						$(".filetree a[title]").tooltip();
						$(".treecontrol a[title]").tooltip();
					} else {
						$("#otherresults").append('<ul id="treeview_' + index + '" class="otherresults filetree treeview"></ul>')
						var start_collapsed = true;
					}
					s = $.ultrie(value.resourcetrie);
					var li = "";
					li += $.explode_ultrie(s);
					var ul = '<ul>' + li + '</ul>';
					$(".filetree").append(ul);
					
					$(".filetree").treeview({
						control: "",
						animated: "fast",
						collapsed: true
					});
					$("#search_content li > span").click();
				});
				$("#search_loader").fadeOut(600);
				$("#breadcrumb").fadeIn(600);
				$("#search_content").fadeIn(600);
				$("#search_results").removeHighlight().highlight($("#search_term").text()).find("#right_menu").removeHighlight();
			} else {
				if(data.responsen == 503) {
					$("#breadcrumb").remove();
					$("#search_loader .progress-bar-warning").switchClass("progress-bar-warning", "progress-bar-danger");
					$("#search_loader .help-block").switchClass("help-block", "help-block text-danger").html('<p><span class="fa fa-times"></span>&nbsp;&nbsp;Non &egrave; stato possibile proseguire con la ricerca perch&eacute; alcune risorse locali non risultano montate.<br />Questo pu&ograve; essere dovuto a un riavvio del server.</p><br /><p><span class="fa fa-clock-o"></span>&nbsp;&nbsp;Prossimo tentativo tra <b class="counter">1 minuto</b>...</p>');
					$("#top_menu_right > form").remove();
					$.decount(1, function() {
						location.reload();
					});
				} else {
				// No results
					$.cryptAjax({
						url: "common/tpl/content.tpl",
						dataType: "text",
						type: "GET",
						success: function(content) {
							if($("#result_type").text() == "Search") {
								var search_term = $("#search_term").text();
								$("#breadcrumb").remove();
								$("#content").html(content);
								$("#top_menu_right > form").remove();
								$("#search_input").val(search_term);
								$("#resstats").addClass("text-danger").text('Nessun risultato trovato con la ricerca per \"' + search_term + '\"');
							}
						}
					});
				}
			}
		}
	});
});
