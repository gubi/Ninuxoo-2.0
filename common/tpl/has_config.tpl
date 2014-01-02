<!-- Treeview -->
<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.edit.js"></script>
<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.async.js"></script>

<?php if(isset($_GET["s"]) && trim($_GET["s"]) == "advanced") { ?>
	<!--Chosen-->
	<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>/common/js/chosen/chosen.css" />
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/chosen/chosen.jquery.js"></script>
<?php } ?>
<!--Zoombox-->
<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/zoombox/zoombox.js"></script>
<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>/common/js/zoombox/zoombox.css" />
<?php
if(isset($_GET["s"]) && trim($_GET["s"]) !== "" && in_array(strtolower($_GET["s"]), $advanced_pages)) {
	?>
	<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
	<script type="text/javascript" src="common/js/include/common.js"></script>
	<?php
}
?>
<script type="text/javascript">
$.ultrie = function(resourcetrie, resuri, realuri) {
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
	if(fork && (resourcetrie.label.indexOf('smb:') != 0 && resourcetrie.label.indexOf('ftp:') != 0)) {
		res1 += "<span class='folder'><a href='";
		res1 += "/Scheda/?url=" + decodeURI(realuri) + "/";
		res1 += "'>";
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
					if(p > 0){
						link += splitted_path[p] + "/";
						text = splitted_path[p];
						
						link = link.replace(ip + "/", "");
					}
					if(text == ip) {
						if(navigator.platform.search("Linux") != -1) {
							text = '<span>smb://</span>' + ip;
						} else {
							text = '<span style="margin-left: -26px;">file://</span>' + ip;
						}
					}
					if(p == splitted_path.length - 1 || text == ip){
						path_link += "" + text + "<span>/</span>";
					} else {
						path_link += '<a href="?op=browse&path=/' + link + '">' + text + '</a><span>/</span>';
					}
				}
			}
			res1 += '<span class="folder">' + path_link + '</span>';
		}
		res1 += '<ul style="display: none;">';
		rescount = 0;
		$.each(resourcetrie.resources, function(index, resource) {
			var respath = resource.uri.replace(resource.filename, "");
			if(resourcetrie.resources.length == 1 || index + 1 == resourcetrie.resources.length){
				if(resource.filetype == "DIRECTORY"){
					res1 += '<li class="result last" id="' + resource.uri + '"><span class="folder"><a href="?op=browse&path=';
				} else {
					res1 += '<li class="result last" id="' + resource.uri + '"><span class="file"><a href="?op=detail&url=';
				}
			} else {
				if(resource.filetype == "DIRECTORY"){
					res1 += '<li class="result" id="' + resource.uri + '"><span class="folder"><a href="?op=browse&path=';
				} else {
					res1 += '<li class="result" id="' + resource.uri + '"><span class="file"><a href="?op=detail&url=';
				}
			}
			res1 += encodeURI(resource.uri);
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
				res1 += $.ultrie(child, "/" + child.label, realuri + "/" + child.label);
			} else {
				res1 += $.ultrie(child, resuri + "/" + child.label, realuri + "/" + child.label);
			}
		} else {
			res1 += $.ultrie(child, "/", child.label + "/");
		}
	});
	if(fork && (resourcetrie.label.indexOf('smb:') != 0 && resourcetrie.label.indexOf('ftp:') != 0)) {
		res1 += "</li>";
	}
	if(newli) {
		res = '<li class="label folder">' + res1 + '</li>\n';
	} else {
		res = res1;
	}
	return res;

};
$.extend({getUrlVars: function(){ var vars = [], hash; var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&'); for(var i = 0; i < hashes.length; i++) { hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1]; } return vars; }, _GET: function(name){ return $.getUrlVars()[name]; }});


function get_stats(){
	$.getJSON("common/include/classes/local_search.class.php", {op: "resourcestats"}, function(resource) {
		$("#resstats").html();
		$("#resstats").fadeOut(300, function(){
			$(this).html(resource.result + " files indicizzati da questa risorsa locale (<?php print $config["NAS"]["name"]; ?>)").fadeIn(300);
		});
	});
}
$(document).ready(function() {
	var nres = "",
	filetype = "",
	query = "",
	query_selected = "",
	likequery_selected = "",
	orquery_selected = "",
	whatsnew_selected = "",
	search_uri = "",
	this_selected = "",
	ninuxoo_selected = "",
	other_selected = "";
	$("#advanced_search_btn").mousedown(function(){
		var href = $("#search_input").val();
		$("#advanced_search_btn").attr("href", $("#advanced_search_btn").attr("href") + "&q=" + href);
	});
	$("#searchform input[type=search]").focus();
	
	$(".resultstree").html("");
	// Check if there's research term in query string
	if($._GET("s") != undefined && $._GET("s") == "advanced"){
		<?php require_once("common/js/include/advanced_search.js"); ?>
	} else {
		if($._GET("op") != undefined){
			op = $._GET("op");
		} else {
			op = "query";
		}
		if($._GET("q") != undefined || $._GET("op") == "browse" || $._GET("op") == "whatsnew" || $._GET("op") == "detail"){
			$("#page_content_main").fadeOut(150);
			$("#main_search").fadeOut(150);
			$("#loader").fadeIn(150);
			if($._GET("q") != undefined && $._GET("q").length > 0){
				$("#loader").append("<span>Ricerca in corso...</span>");
				// Removes "+" character from the search term
				var query = decodeURI($._GET("q")).replace(/\+/g, " "),
				get = "q";
				switch($._GET("nas")){
					case "ninuxoo":
						search_uri = "common/include/funcs/_ajax/read_json.php?op=" + op + "&q=" + $._GET("q") + "&nresults=" + $._GET("nresults") + "&ip=" + $._GET("ip") + "&filetype=" + $._GET("filetype");
						break;
					case "other":
						search_uri = $._GET("ip") + "/common/include/classes/local_search.class.php?op=" + op + "&q=" + $._GET("q") + "&nresults=" + $._GET("nresults") + "&ip=" + $._GET("ip") + "&filetype=" + $._GET("filetype");
						break;
					case "this":
					default:
						search_uri = "common/include/classes/local_search.class.php?op=" + op + "&q=" + $._GET("q") + "&nresults=" + $._GET("nresults") + "&ip=" + $._GET("ip") + "&filetype=" + $._GET("filetype");
						break;
				}
			} else if($._GET("op") == "browse") {
				var browse_query = "",
				splitted_query_path = decodeURI($._GET("path")).split("/"),
				get = "path",
				pp = "",
				navigating_dir = "";
				
				if(decodeURI($._GET("path")) == "/" || decodeURI($._GET("path")) == ""){
					navigating_dir = "Directory principale";
				} else {
					navigating_dir = 'directory "' + decodeURI($._GET("path")) + '"';
				}
				for (p = 0; p < splitted_query_path.length; p++){
					if(splitted_query_path[p].length > 0){
						if(p == splitted_query_path.length - 1){
							browse_query += "" + splitted_query_path[p].replace(/\+/g, " ") + "";
						} else {
							pp += splitted_query_path[p] + "/";
							browse_query += '<a href="?op=browse&path=' + pp + '">' + splitted_query_path[p].replace(/\+/g, " ") + '</a>/';
						}
					}
				}
				$("#loader").append("<span>Scansione in corso...</span>");
				search_uri = "common/include/classes/local_search.class.php?op=browse&path=" + $._GET("path");
			} else if($._GET("op") == "whatsnew") {
				$("#loader").append("<span>Acquisizione dei dati in corso...</span>");
				search_uri = "common/include/classes/local_search.class.php?op=whatsnew";
			} else if($._GET("op") == "detail") {
				$("#loader").append("<span>Acquisizione dei dati in corso...</span>");
				search_uri = "common/include/classes/local_search.class.php?op=detail&url=" + encodeURI($._GET("url"));
			}
			$.getJSON(search_uri, function(data) {
				if(data.nresults > 0){
					var more_search_data = "";
					// Sets the browser's title
					if($._GET("q") != undefined && $._GET("q").length > 0){
						$("title").text("NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Risultati della ricerca per \"" + query + "\" - Ninux.org");
					} else if ($._GET("op") == "detail") {
						$("title").text("NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Scheda del " + data.results.type + " \"" + data.results.name + "\" - Ninux.org");
					} else {
						$("title").text("NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Navigazione della " + navigating_dir + " - Ninux.org");
					}
					// Convert the main search id to results id
					$("#page_content_main").attr("id", "page_content").html("");
					// Convert the main header id to results id
					$("#main_header").attr("id", "header");
					// Sets the link on logo
					$("#logo").html("<a href=\"\">" + $("#logo").html() + "</a>");
					// Moves the search input to top left of page
					if($._GET("q") != undefined){
						more_search_data = "&q=" + $._GET("q");
					}
					if($._GET("nas") != undefined){
						more_search_data += "&nas=" + $._GET("nas");
					}
					if($._GET("op") != undefined){
						more_search_data += "&op=" + $._GET("op");
					}
					if($._GET("nresults") != undefined){
						more_search_data += "&nresults=" + $._GET("nresults");
					}
					if($._GET("filetype") != undefined){
						more_search_data += "&filetype=" + $._GET("filetype");
					}
					$("#top_menu").append('<div id="search"><form id="searchform" action="" method="get">' + $("#searchform").html() + '</a></form></div>').find("div:first-child").css({"width": "67%"});
					$("#searchform input[type=search]").val(query);
					//Clear data
					$("#main_search").remove();
					$("#searchlabels").html("");
					$(".resultstree").html("");
					
					if($._GET("q") != undefined && $._GET("q").length > 0){
						$("#page_content").prepend('<h1>Ricerca di &quot;' + query + '&quot;</h1>');
						s = data.nresults + " risultati trovati in " + Math.round(data.searchtime*1000)/1000 + " secondi";
						$("#page_content").append("<h2>" + s + "</h2>");
						$("#page_content").append('<div id="right_menu"><h3>STATISTICHE<span>di ricerca</span></h3></div>');
					} else if ($._GET("op") == "detail") {
						$("#page_content").prepend('<h1>' + data.results.name + '</h1>');
						s = data.nresults + " risultato trovato in " + Math.round(data.searchtime*1000)/1000 + " secondi";
						$("#page_content").append("<h2>" + s + "</h2>");
						$("#page_content").append('<div id="right_menu"><h3>STATISTICHE<span>del ' + data.results.type.toLowerCase() + '</span></h3></div>');
					} else {
						$("#page_content").prepend('<h1>Navigazione della ' + navigating_dir + '</h1>');
						s = data.nresults + " risultati trovati in " + Math.round(data.searchtime*1000)/1000 + " secondi";
						$("#page_content").append("<h2>" + s + "</h2>");
						$("#page_content").append('<div id="right_menu"><h3>STATISTICHE<span>di navigazione</span></h3></div>');
					}
					if ($._GET("op") != "detail"){
						$("#loader").fadeIn(150);
						index_count = 0;
						$.each(data.results, function(index, value) {
							//console.log(index, value);
							//alert(value.resultlabel);
							index_count++;
							if(index == 0) {
								s = '<li class="exactresults">';
							} else {
								s = '<li class="otherresults">';
							}
							s += '<a href="javascript:void(0);">';
							s += value.resultlabel.replace(/\//g, '</a> / <a href="javascript:void(0);">');
							s += "</a><span>";
							s += value.nresults;
							if(value.nresults == 1){
								var results_txt = "risultato";
							} else {
								var results_txt = "risultati";
							}
							s += " " + results_txt + "</span></li>";
							if(index == 0){
								$("#right_menu").append('<ul>' + s + '</ul>');
							} else {
								if(index == 1){
									$("#right_menu").append('<h4>Altri risultati trovati:</h4><ul>' + s + '</ul>');
								} else {
									$("#right_menu ul:last-child").append(s);
								}
							}
							var ul_tree_control = '<ul id="research_term_control_' + index + '" class="treecontrol"><li><a href="javascript:void(0);" title="Racchiudi tutti gli alberi" class="close"></a></li><li><a href="javascript:void(0);" title="Espandi tutti gli alberi" class="open"></a></li><li><a href="javascript:void(0);" title="Inverti modalitÃ : se aperto chiudi, se chiuso apri" class="toggle"></a></li></ul>';
							
							if(index == 0) {
								$("#page_content").append('<div class="search_results"><h1>Termine di ricerca</h1>' + ul_tree_control + '<ul id="treeview_' + index + '" class="exactresults filetree treeview"></ul><div id="otherresults"></div></div>');
								var start_collapsed = false;
								
								$(".filetree a[title]").tooltip();
								$(".treecontrol a[title]").tooltip();
							} else {
								$("#otherresults").append('<h1>Risultati per &quot;' + value.resultlabel + '&quot;</h1>' + ul_tree_control + '<ul id="treeview_' + index + '" class="otherresults filetree treeview"></ul>')
								var start_collapsed = true;
							}
							s = $.ultrie(value.resourcetrie, "", "");
							$(".filetree").append(s);
							
							$(".filetree").treeview({
								control: "#research_term_control_" + index,
								animated: "fast",
								collapsed: true
							});
							$("#research_term_control_" + index + " li a.open").click();
						});
						$("#page_content").removeHighlight().highlight(query).find("#right_menu").removeHighlight();
						$("#loader").fadeOut(300, function(){
							$("#page_content").slideDown(600);
						});
					} else {
						var media_stats, media_stats_data, cast_stats, cast_stats_data, file_stats, file_stats_data, file_data, file_stats_data, poster, caption, titolo_originale, contenuto;
						
						$("#page_content").append('<div class="search_results"><h1 style="text-indent: 0; margin: 0 0 20px 0; border: 0px none;">Scheda del film</h1></div><div id="page_content_content"><div id="wiki_content" style="padding-bottom: 50px;"></div><table cellspacing="10" cellpadding="10" style="width: auto;"><tr><td id="cast_content" valign="top"></td><td id="production_content" valign="top"></td></tr></table>')
						switch(data.results.type){
							case "Film":
									/* Menu laterale */
									media_stats_data = '<li><a href="javascript: void(0);">Anno</a>: <span style="display: none;" id="film_year"></span></li>';
									media_stats_data += '<li><a href="javascript: void(0);">Paese</a>: <span style="display: none;" id="film_country"></span></li>';
									media_stats_data += '<li><a href="javascript: void(0);">Genere</a>: <span style="display: none;" id="film_genre"></span></li>';
									media_stats_data += '<li><a href="javascript: void(0);">Durata</a>: <span style="display: none;" id="film_runtime"></span></li>';
									media_stats_data += '<li><a href="javascript: void(0);">Colore</a>: <span style="display: none;" id="film_color"></span></li>';
									media_stats_data += '<li><a href="javascript: void(0);">Rapporto d\'aspetto</a>: <span style="display: none;" id="film_ratio"></span></li>';
									media_stats_data += '<li><a href="javascript: void(0);">Tipo di audio</a>: <span style="display: none;" id="film_sound"></span></li>';
									
								media_stats = '<ul id="media_stats_data"></ul>';
								file_stats_data = '<li><a href="javascript: void(0);">Dimensione</a>: ' + data.results.stats.file.size + '</li>';
								file_stats_data += '<li><a href="javascript: void(0);">Permessi</a>: ' + data.results.stats.permission.human + ' (' + data.results.stats.permission.octal + ')</li>';
								//media_stats_data += '<li id="loading_data"><span>&rarr; Interrogazione IMDB...</span></li>';
								file_stats = '<h4 class="othersearch">File</h4><ul>' + file_stats_data + '</ul>';
								file_stats_data = '<li><a href="javascript: void(0);">Data di creazione</a>:<br />' + data.results.stats.time.creation + '</li>';
								file_stats_data += '<li><a href="javascript: void(0);">Ultimo accesso</a>:<br />' + data.results.stats.time.access + '</li>';
								file_stats_data += '<li><a href="javascript: void(0);">Ultima modifica</a>:<br />' + data.results.stats.time.modify + '</li>';
								file_data = '<ul>' + file_stats_data + '</ul>';
								$("#right_menu").animate({"width": "300px"}, 450).append(media_stats + cast_stats + file_stats + file_data);
								$.ajaxSetup({scriptCharset: "utf-8", contentType: "application/json; charset=utf-8"});
								$("#loader").append("<br /><span>Interrogo dbpedia...</span>");
								$.getJSON("common/include/funcs/_ajax/get_dbpedia.php", {title: data.results.name, type: "film", year: data.results.year}, function(wiki_data_film){
									$("#media_stats_data").html(media_stats_data);
									for (i = 0; i < wiki_data_film._items_count; i++) {
										if(wiki_data_film[i].film.year != "") {
											$("#film_year").html(wiki_data_film[i].film.year).css("display", "inline");
										}
										if(wiki_data_film[i].film.country != "") {
											$("#film_country").html(wiki_data_film[i].film.country).css("display", "inline");
										}
										if(wiki_data_film[i].film.genre != "") {
											$("#film_genre").html(wiki_data_film[i].film.genre).css("display", "inline");
										}
										if(wiki_data_film[i].film.runtime != "") {
											$("#film_runtime").html(wiki_data_film[i].film.runtime).css("display", "inline");
										}
										if(wiki_data_film[i].film.color != "") {
											$("#film_color").html(wiki_data_film[i].film.color).css("display", "inline");
										}
										if(wiki_data_film[i].film.ratio != "") {
											$("#film_ratio").html(wiki_data_film[i].film.ratio).css("display", "inline");
										}
										if(wiki_data_film[i].film.audio != "") {
											$("#film_sound").html(wiki_data_film[i].film.audio).css("display", "inline");
										}
										break;
									}
								});
								$.getJSON("common/include/funcs/_ajax/get_dbpedia.php", {title: data.results.name, type: "", year: data.results.year}, function(wiki_data){
									for (j = 0; j < wiki_data._items_count; j++) {
										if(wiki_data[j].original_title != "") {
											titolo_originale = "<i>Titolo originale: \"" + wiki_data[j].original_title + "\"</i>";
										} else {
											titolo_originale = "";
										}
										$("#page_content h2").html(titolo_originale);
										$("#page_content h2").append('<div id="paths">');
										$("#paths").append('<div id="smb"><p>Percorso Samba:&nbsp;&nbsp;</p><p><a name="samba_share_path">' + decodeURI("smb://<?php print $NAS_IP; ?>/" + $._GET("url")) + '</a></p></div>');
										$("#paths").append('<div id="file" style="clear: both;"><p>Condivisione Windows:&nbsp;&nbsp;</p><p><a name="samba_share_path">' + decodeURI("file://<?php print $NAS_IP; ?>/" + $._GET("url")) + '</a></p></div>');
										
										if(wiki_data[j].depiction != "" && wiki_data[j].caption != "") {
											$("#wiki_content").append('<div id="poster_box"><a id="poster" class="zoombox" href="' + wiki_data[j].depiction + '" title="' + wiki_data[j].caption + '"><img style="width: 250px;" src="' + wiki_data[j].depiction + '" /></a>' + wiki_data[j].caption + '</div>');
											$("#poster img").load(function(){
												$("#poster_box").fadeIn(300);
											});
										}
										if(wiki_data[j].abstract != "") {
											$("#wiki_content").append(unescape(wiki_data[j].abstract));
										}
										break;
									}
								});
								$.getJSON("common/include/funcs/_ajax/get_dbpedia.php", {title: data.results.name, type: "cast", year: data.results.year}, function(wiki_data_cast){
									cast_stats_data = '<li>Regia: <span style="display: none;" id="film_director"></span></li>';
									cast_stats_data += '<li>Scenografia: <span style="display: none;" id="film_scenographer"></span></li>';
									cast_stats_data += '<li>Sceneggiatura: <span style="display: none;" id="film_scenographer"></span></li>';
									cast_stats_data += '<li>Cast: <ul style="display: none;" id="film_cast"></ul></li>';
									$("#cast_content").append('<h1>Cast</h1><ul id="cast_stats_data">' + cast_stats_data + '</ul>');
									for (k = 0; k < wiki_data_cast._items_count; k++) {
										if(wiki_data_cast[k].cast.director != "") {
											
											$("#film_director").html(wiki_data_cast[k].cast.director).css("display", "inline");
										}
										if(wiki_data_cast[k].cast.scenographer != "") {
											$("#film_scenographer").html(wiki_data_cast[k].cast.scenographer).css("display", "inline");
										}
										if(wiki_data_cast[k].cast.screenwriter != "") {
											$("#film_screenwriter").html(wiki_data_cast[k].cast.screenwriter).css("display", "inline");
										}
										if(wiki_data_cast[k].cast.actors != "") {
											$("#film_cast").html(wiki_data_cast[k].cast.actors).css("display", "inline");
										}
										break;
									}
								});
								$.getJSON("common/include/funcs/_ajax/get_dbpedia.php", {title: data.results.name, type: "production", year: data.results.year}, function(wiki_data_production){
									production_stats_data = '<li>Produttore: <span style="display: none;" id="film_producer"></span></li>';
									production_stats_data += '<li>Produttore esecutivo: <span style="display: none;" id="film_executiveProducer"></span></li>';
									production_stats_data += '<li>Produzione: <span style="display: none;" id="film_company"></span></li>';
									$("#production_content").append('<h1>Produzione</h1><ul id="production_stats_data">' + production_stats_data + '</ul>');
									for (l = 0; l < wiki_data_production._items_count; l++) {
										if(wiki_data_production[l].production.director != "") {
											$("#film_producer").html(wiki_data_production[l].production.producer).css("display", "inline");
										}
										if(wiki_data_production[l].cast.scenographer != "") {
											$("#film_executiveProducer").html(wiki_data_production[l].production.executiveProducer).css("display", "inline");
										}
										if(wiki_data_production[l].cast.screenwriter != "") {
											$("#film_company").html(wiki_data_production[l].production.company).css("display", "inline");
										}
										break;
									}
								});
									/*
									
									*/
									/*
									$.get("common/include/funcs/_ajax/get_imdb.php", {title: data.results.name}, function(datas){
										$("#poster").attr("href", datas.dati["poster"]).attr("title", "Locandina del film").find("img").attr("src", datas.dati["poster"]);
										if(datas.dati["poster"].length == 0){
											if(wiki_data.sinossi["lang"] == "en"){
												$("#poster").attr("href", wiki_data.sinossi["image"]).attr("title", wiki_data.sinossi["caption"]).find("img").attr("src", wiki_data.sinossi["image"]);
											} else {
												$("#poster").attr("href", wiki_data.sinossi["immagine"]).attr("title", wiki_data.sinossi["didascalia"]).find("img").attr("src", wiki_data.sinossi["immagine"]);
											}
											$("#poster img").load(function(){
												$("#poster_box").fadeIn(300);
											});
										}
										/* Menu laterale * /
										// IMDB riporta dati più dettagliati
										if(datas.dati["anno"] != ""){
											$("#film_year").html(datas.dati["anno"]).css("display", "inline");
										}
										if(datas.dati["regia"] != ""){
											$("#film_director").html(datas.dati["regia"]).css("display", "inline");
										}
										if($("#film_genre").html().length == 0){
											// In inglese, meglio se in extremis
											$("#film_genre").html(datas.dati["genere"]).css("display", "inline");
										}
										if($("#film_genre").html().length == 0){
											$("#film_runtime").html(datas.dati["durata"]).css("display", "inline");
										}
										if(datas.dati["colore"] != ""){
											$("#film_color").html(datas.dati["colore"]).css("display", "inline");
										}
										if(datas.dati["risoluzione"] != ""){
											$("#film_ratio").html(datas.dati["risoluzione"]).css("display", "inline");
										}
										if(datas.dati["sonoro"] != ""){
											$("#film_sound").html(datas.dati["sonoro"]).css("display", "inline");
										}
										
										imdb_stats_data = '<li><a href="javascript: void(0);">Voto medio</a>: <span style="display: inline;"><b style="font-size: 21px;">' + datas.dati["rating"] + '</b> / ' + datas.dati["voter-count"] + '</span></li>';
										imdb_stats_data += '<li><a href="javascript: void(0);">Voto massimo</a>: <span style="display: inline; font-size: 1em;">' + datas.dati["max-rating"] + '</span></li>';
										
										$("<ul>" + imdb_stats_data + "</ul>").insertAfter("#imdb");
										$("#loading_data").fadeOut(450, function(){ $(this).remove(); });
									}, "json");
									*/
									/*
									$("#page_content_content").append('<table id="director_table" cellpadding="10" cellspacing="10" class="grid" style="float: right;"></table>');
									$("#page_content_content").append('<table id="details_table" cellpadding="10" cellspacing="10" class="grid"></table>');
									$("#details_table").append('<tr><th><img src="common/media/img/document_video_32_333.png" /></th><td>' + datas.dati.genere + '</td></tr>');
									$("#details_table").append('<tr><th><img src="common/media/img/clock_32_333.png" /></th><td>' + datas.dati.durata + '</td></tr>');
									$("#details_table").append('<tr><th><img src="common/media/img/speaker_louder_32_333.png" /></th><td>' + datas.dati.sonoro + '</td>v</tr>');
									$("#details_table").append('<tr><th><img src="common/media/img/screen_16to9_32_333.png" /></th><td>' + datas.dati.risoluzione + '</td></tr>');
									$("#director_table").append('<tr><th><a href="?op=query&nresults=200&q=' + datas.dati.regia + '" title="Cerca altri film di questo regista"><img src="common/media/img/user_profile_32_333.png" /></a></th><td><a href="?op=query&nresults=200&q=' + datas.dati.regia + '" title="Cerca altri film di questo regista">' + datas.dati.regia + '</a></td></tr>');
									$("#director_table").append('<tr><th><img src="common/media/img/group_full_32_333.png" /></th><td>' + datas.dati.sceneggiatura + '</td></tr>');
									$("#director_table").append('<tr><th></th><td></td></tr>');
									$("#director_table").append('<tr><th><a href="?op=query&nresults=200&q=' + datas.dati.anno + '" title="Cerca altri film di questo anno"><img src="common/media/img/label_32_333.png" /></a></th><td><a href="?op=query&nresults=200&q=' + datas.dati.anno + '" title="Cerca altri film di questo anno">' + datas.dati.data_uscita + '</a></td></tr>');
									
										setTimeout(function(){
											$("#poster_box").fadeIn(300);
										}, 2000);
										
								});*/
								$("#poster_box").fadeIn(300);
								$("#loader").fadeOut(300, function(){
									$("#page_content").fadeIn(600);
								});
								$('a.zoombox').zoombox({
									theme: 'zoombox',
									opacity: 0.8,
									duration: 800,
									animation: true,
									width: 600,
									height: 400,
									gallery: true,
									autoplay: false
								});
								break;
						}
					}
				} else {
					$("#loader").hide();
					$("#page_content_main").fadeIn(150);
					$("#main_search").fadeIn(150);
					$("#content").prepend('<p class="noresults">Nessun risultato trovato con la ricerca per \"' + query + '\"</p>');
					get_stats();
				}
			});
		} else {
			get_stats();
		}
	}
	if($._GET("s") != undefined && $._GET("s") == "advanced"){
		$("select").chosen();
	}
	check_notify();
});
</script>