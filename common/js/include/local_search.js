function rawurlencode(str) {
	str = (str+'').toString();        
	return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A');
}
$.ultrie = function(resourcetrie) {
	var a = {};
	
	$.each(resourcetrie.children, function(index, child) {
		a[index] = {
			rank: child.rank,
			label: child.label,
			hash: child.hash,
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
			path_link += '<a href="./Esplora:?' + resourcetrie.hash + '">' + resourcetrie.label + '</a><tt> / </tt></span><ul>';
			$.each(resourcetrie.resources, function(r, res) {
				path_link += '<li><span class="fa ' + res.icon + '">&nbsp;&nbsp;<a href="./Scheda:?' + res.hash + '">' + res.filename + '</a></span></li>';
			});
			$.each(resourcetrie.child, function(rank, obj) {
				path_link += '<li><span class="fa fa-folder-o">&nbsp;&nbsp;' + $.explode_a_ultrie(obj);
			});
			path_link += '</ul></li>';
		} else {
			path_link += '<a href="./Esplora:?' + resourcetrie.hash + '"> ' + resourcetrie.label + '</a><tt> / </tt>';
			$.each(resourcetrie.child, function(rank, obj) {
				path_link += $.explode_a_ultrie(obj);
			});
		}
	} else {
		if(Object.keys(resourcetrie.child).length > 1) {
			path_link += '<a href="./Esplora:?' + resourcetrie.hash + '">' + resourcetrie.label + '</a><tt> / </tt></span><ul>';
			$.each(resourcetrie.resources, function(r, res) {
				path_link += '<li><span class="fa ' + res.icon + '">&nbsp;&nbsp;<a href="./Scheda:?' + res.hash + '">' + res.filename + '</a></span></li>';
			});
			$.each(resourcetrie.child, function(rank, obj) {
				path_link += '<li><span class="fa fa-folder-o">&nbsp;&nbsp;' + $.explode_a_ultrie(obj);
			});
			path_link += '</ul></li>';
		} else {
			path_link += '<a href="./Esplora:?' + resourcetrie.hash + '">' + resourcetrie.label + '</a><tt> / </tt></span>';
			path_link += '<ul>';
			$.each(resourcetrie.resources, function(r, res) {
				path_link += '<li><span class="fa ' + res.icon + '">&nbsp;&nbsp;<a href="./Scheda:?' + res.hash + '">' + res.filename + '</a></span></li>';
			});
			path_link += "</ul></li>";
		}
	}
	return path_link;
};

$(document).ready(function() {
	var s = "",
	password = makeid();
	
	switch($("#result_type").text()) {
		case "Search":
			$("#breadcrumb").hide();
			break;
		default:
			$("#breadcrumb").show();
			
			$("html, body").animate({ scrollTop: $("#container").offset().top }, 300);
			break;
	}
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("q=" + $("#search_term").text() + "&op=" + $("#search_type").text() + "&nresults=" + $("#search_num_results").text() + "&path=" + $("#search_path").text() + "&filetype=" + $("#search_filetype").text(), password);
		
		$.ajax({
			url: "common/include/funcs/_ajax/decrypt.php",
			dataType: "json",
			type: "POST",
			data: {
				jCryption: encryptedString,
				type: "local_search"
			},
			success: function(data) {
				if(data.nresults > 0){
					$("#nlabels").text(data.nlabels);
					$("#nresults").text(data.nresults);
					$("#searchtime").text(Math.round(data.searchtime*1000)/1000 + " secondi");
					
					index_count = 0;
					$.each(data.results, function(index, value) {
						index_count++;
						
						if(index == 0) {
							$("#search_results").html('<div class="search_results"><ul id="treeview_' + index + '" class="exactresults filetree treeview"></ul><div id="otherresults"></div></div>');
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
					// No results
					$.ajax({
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
		});
	}, function() {
		$("#page_loader").fadeOut(300);
		alert("Si &egrave; verificato un errore durante la ricerca :(", {icon: "error", title: "Ouch!"});
	});
});
