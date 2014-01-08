function rawurlencode(str) {
	str = (str+'').toString();        
	return encodeURIComponent(str).replace(/!/g, '%21').replace(/'/g, '%27').replace(/\(/g, '%28').replace(/\)/g, '%29').replace(/\*/g, '%2A');
}
$.ultrie = function(resourcetrie, resuri, realuri, hash) {
	var res = "",
	path_link = "",
	ip = "",
	text = "",
	link = "",
	res1 = "",
	newli = false,
	fork = false,
	nres = resourcetrie.resources.length;
	$.each(resourcetrie.children, function(index, child) {
		if(child.resources.length < nres) {
			fork = true;
		}
	});
	if(fork) {
		res1 += '<span class="fa fa-folder-o">&nbsp;&nbsp;<a href="';
		res1 += "./Scheda:?" + hash;
		res1 += '">';
		res1 += decodeURI(resuri);
		res1 += "/</a>";
		res1 += "</span>";
		newli = true;
	}
	if(resourcetrie.resources.length > 0) {
		if(!fork) {
			var splitted_path = decodeURI(resuri).split("/"),
			splitted_uri = decodeURI(realuri).split("/");
			
			for (p = 0; p < splitted_path.length; p++){
				if(splitted_path[p].length > 0){
					ip = splitted_path[1];
					if(p >= 0){
						link += splitted_path[p] + "/";
						text = splitted_path[p];
					}
					if(text == ip) {
						text = '<span></span>' + ip;
					}
					if(p == splitted_path.length - 1 || text == ip){
						path_link += "<span>" + text + "</span><tt> / </tt>";
					} else {
						path_link += '<a href="./Esplora:?' + hash + '">' + text + '</a><tt> / </tt>';
					}
				}
			}
			res1 += '<span class="fa fa-folder-o">&nbsp;&nbsp;' + path_link + '</span>';
		}
		res1 += '<ul style="display: none;">';
		rescount = 0;
		$.each(resourcetrie.resources, function(index, resource) {
			var respath = resource.uri.replace(resource.filename, "");
			if(resourcetrie.resources.length == 1 || index + 1 == resourcetrie.resources.length){
				if(resource.filetype == "DIRECTORY"){
					res1 += '<li class="result last" id="' + resource.uri + '"><span class="fa fa-folder-o">&nbsp;&nbsp;<a href="./Esplora:?';
				} else {
					res1 += '<li class="result last" id="' + resource.uri + '"><span class="fa fa-file-o">&nbsp;&nbsp;<a href="./Scheda:?';
				}
			} else {
				if(resource.filetype == "DIRECTORY"){
					res1 += '<li class="result" id="' + resource.uri + '"><span class="fa fa-folder-o">&nbsp;&nbsp;<a href="./Esplora:?';
				} else {
					res1 += '<li class="result" id="' + resource.uri + '"><span class="fa fa-file-o">&nbsp;&nbsp;<a href="./Scheda:?';
				}
			}
			res1 += resource.hash;
			res1 += '" class="zoombox">';
			res1 += resource.filename;
			res1 += '</a></span></li>';
		});
		res1 += "</ul>";
		if(!fork) {
			res1 += "";
			newli = true;
			resuri = "";
		}
	}
	$.each(resourcetrie.children, function(index, child) {
		if(child.label.indexOf('smb:') != 0 && child.label.indexOf('ftp:') != 0) {
			if(child.resources.length < nres) {
				res1 += $.ultrie(child, "/" + child.label, realuri + "/" + child.label, child.hash);
			} else {
				res1 += $.ultrie(child, resuri + "/" + child.label, realuri + "/" + child.label, child.hash);
			}
		} else {
			res1 += $.ultrie(child, "/", child.label + "/", child.hash);
		}
	});
	if(fork) {
		res1 += "</li>";
	}
	if(newli) {
		res = '<li>' + res1 + '</li>\n';
	} else {
		res = res1;
	}
	return res;

};


$(document).ready(function() {
	var s = "",
	password = makeid();
	
	$.jCryption.authenticate(password, "common/include/funcs/_ajax/decrypt.php?getPublicKey=true", "common/include/funcs/_ajax/decrypt.php?handshake=true", function(AESKey) {
		var encryptedString = $.jCryption.encrypt("q=" + $("#search_term").text() + "&op=" + $("#search_type").text() + "&nresults=" + $("#search_num_results").text() + "&ip=" + $("#search_ip").text() + "&filetype=" + $("#search_filetype").text(), password);
		
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
						s = $.ultrie(value.resourcetrie, "", "");
						$(".filetree").append(s);
						
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
							$("#breadcrumb").remove();
							$("#content").html(content);
							$("#resstats").addClass("text-danger").text('Nessun risultato trovato con la ricerca per \"' + $("#search_input").val() + '\"');
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
