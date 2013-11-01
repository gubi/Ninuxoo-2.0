<?php
if(!file_exists("config.ini")){
	ob_start();
	require("scan.php");
	$data = ob_get_clean();
	ob_end_clean();
	header("Content-type: text/html");
}
$config = parse_ini_file("config.ini", 1);

$NAS_absolute_uri = preg_replace("{/$}", "", $config["NAS"]["http_root"]);
?>
<!DOCTYPE html>
<html>
<head>
	<title>NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Ninux.org</title>
	
	<base href="./" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Ninux.org Community - the Ninux Software Team" />
	<meta name="description" content="Ninux.org search engine" />
	
	<link rel="shortcut icon" href="<?php print $NAS_absolute_uri; ?>/common/media/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>/common/css/main.css" type="text/css" media="screen" />
	<link rel="search" type="application/opensearchdescription+xml" title="Ninuxoo NAS LittleGym" href="osd.xml" />
	
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery-1.7.2.min.js"></script>
	<!-- Treeview -->
	<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.js"></script>
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.treeview/jquery.treeview.async.js"></script>
	<!-- Zoombox
	<link href="<?php print $NAS_absolute_uri; ?>/common/js/zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/zoombox/zoombox.js"></script>
	<!-- qTip -->
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/qtip-integration.js"></script>
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/jquery.highlight-4.js"></script>
	<!--Chosen-->
	<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>/common/js/chosen/chosen.css" />
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>/common/js/chosen/chosen.jquery.js"></script>
	<script type="text/javascript">
	$.ultrie = function(resourcetrie, resuri, realuri) {
		var res = "",
		path_link = "",
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
			res1 += "/cgi-bin/browse_share.cgi?url=" + decodeURI(realuri) + "/";
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
						if(p == 2){
							link += splitted_uri[p] + "/";
							text = splitted_path[p];
							ip = splitted_path[2];
						} else {
							link += splitted_uri[p] + "/";
							text = splitted_path[p];
							ip = splitted_path[2];
						}
						if(p == splitted_path.length - 1){
							path_link += "&nbsp;" + text + "&nbsp;/";
						} else {
							path_link += '&nbsp;<a href="/cgi-bin/browse_share.cgi?url=smb://' + link + '">' + text + '</a>&nbsp;/';
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
					res1 += '<li class="result last" id="' + resource.uri + '"><a href="http://ninuxoo.ninux.org/cgi-bin/browse_share.cgi?url=';
				} else {
					res1 += '<li class="result" id="' + resource.uri + '"><span class="file"><a href="http://ninuxoo.ninux.org/cgi-bin/browse_share.cgi?url=';
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
			res = '<li class="label folder collapsable">' + res1 + '</li>\n';
		} else {
			res = res1;
		}
		return res;

	};
	$.extend({getUrlVars: function(){ var vars = [], hash; var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&'); for(var i = 0; i < hashes.length; i++) { hash = hashes[i].split('='); vars.push(hash[0]); vars[hash[0]] = hash[1]; } return vars; }, _GET: function(name){ return $.getUrlVars()[name]; }});
	
	
	function get_stats(){
		$.getJSON("local_search.php", {op: "resourcestats"}, function(resource) {
			$("#resstats").html();
			$("#resstats").fadeOut(300, function(){
				$(this).html(resource.result + " files indicizzati in questo Server (<?php print $config["NAS"]["name"]; ?>)").fadeIn(300);
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
		
		$("#searchform input[type=search]").focus();
		
		$(".resultstree").html("");
		// Check if there's research term in query string
		if($._GET("s") != undefined && $._GET("s") == "advanced"){
			if($._GET("filetype") != undefined){
				query = $._GET("q");
			} else {
				query = "";
			}
			if($._GET("nresults") != undefined){
				nres = $._GET("nresults");
			} else {
				nres = 200;
			}
			if($._GET("filetype") != undefined){
				filetype = $._GET("filetype");
			} else {
				filetype = "";
			}
			if($._GET("nas") != undefined){
				switch($._GET("nas")){
					case "ninuxoo":
						this_selected = "";
						ninuxoo_selected = "selected";
						other_selected = "";
						break;
					case "other":
						this_selected = "";
						ninuxoo_selected = "";
						other_selected = "selected";
						break;
					case "this":
					default:
						this_selected = "selected";
						ninuxoo_selected = "";
						other_selected = "";
						break;
				}
			}
			if($._GET("op") != undefined){
				switch($._GET("op")){
					case "exactquery":
						query_selected = "";
						exactquery_selected = "selected";
						whatsnew_selected = "";
						break;
					case "whatsnew":
						query_selected = "";
						exactquery_selected = "";
						whatsnew_selected = "selected";
						break;
					case "query":
					default:
						query_selected = "selected";
						exactquery_selected = "";
						whatsnew_selected = "";
						break;
				}
			}
			$("#main_search").remove();
			// Sets the browser's title
			$("title").text("NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Ricerca avanzata - Ninux.org");
			// Convert the main header id to results id
			$("#main_header").attr("id", "header");
			// Add page title & subtitle
			$("#page_content_main").attr("id", "page_content").html("<h1>Ricerca avanzata</h1><h2>Aggiungi dei filtri alla ricerca</h2>");
			// Create container & form
			$("#page_content").append('<div id="advanced_search"></div>');
			$("#advanced_search").html('<form id="searchform" class="frm" action="" method="get"><fieldset><legend>Parametri di ricerca</legend></fieldset></form>');
			$("#searchform fieldset").append('<table cellpadding="0" cellspacing="0"></table>');
			// Append each row to form table
			$("#searchform table").append('<tr><td><label for="q">Query della ricerca</label></td><td><input type="search" name="q" id="q" value="' + query + '" /></td></tr></tr>');
			$("#searchform table").append('<tr><td><label for="nas">Cerca in</label></td><td><select name="nas" id="nas"></select></td></tr><tr><td colspan="2">&nbsp;</td></tr>');
			$("#nas").append('<option value="this" ' + this_selected + '>Questo Server (<?php print $config["NAS"]["name"]; ?>)</option>');
			$("#nas").append('<option value="ninuxoo" ' + ninuxoo_selected + '>NinuXoo</option>');
			$("#nas").append('<option value="other" ' + other_selected + '>Altro Server</option>');
			$("#nas").change(function(){
				switch($(this).val()){
					case "ninuxoo":
						$("#ip").removeAttr("disabled");
						break;
					case "other":
						$("#ip").removeAttr("disabled");
						$("#searchform button").attr("disabled", "disabled");
						setTimeout(function(){$("#ip").focus()}, 1);
						break;
					default:
						$("#ip").attr("disabled", "disabled");
						break;
				}
			});
			$("#searchform table").append('<tr><td><label for="op">Tipo di ricerca</label></td><td><select name="op" id="op"></select></td></tr>');
			$("#op").append('<option value="query" ' + query_selected + '>Tutti i risultati possibili</option>');
			$("#op").append('<option value="exactquery" ' + likequery_selected + '>per parola esatta</option>');
			$("#op").append('<option value="whatsnew" ' + whatsnew_selected + '>dall\'ultima scansione del crawler</option>');
			$("#searchform table").append('<tr><td><label for="nresults">Numero di risultati</label></td><td><input type="number" name="nresults" id="nresults" min="10" max="400" size="1" maxlength="3" value="' + nres + '" /></td></tr><tr><td colspan="2">&nbsp;</td></tr>');
			$("#searchform table").append('<tr><td><label for="ip">Restringi la ricerca all\'interno di un indirizzo IP</label></td><td><input type="text" name="ip" id="ip" maxlength="15" size="12" value="" placeholder="Indirizzo IP" disabled="disabled" /></td></tr>');
			$("#ip").blur(function(){
				$("#loader").fadeIn(300);
				if($("#nas").val() == "other"){
					var val = $(this).val();
					if(val.length > 0){
						$.get("common/include/funcs/_ajax/check_uri.php", {uri: $(this).val()}, function(resp){
							if(resp == "valid"){
								$("#loader").fadeOut(300, function(){
									$("#searchform button").removeAttr("disabled");
								});
							} else {
								$("#loader").fadeOut(300, function(){
									alert("L'indirizzo IP inserito non è raggiungibile\no non è compatibile con la versione corrente di NinuXoo.");
									$("#searchform button").attr("disabled", "disabled");
									setTimeout(function(){$("#ip").select()}, 1);
								});
							}
						});
					} else {
						alert("Inserire un indirizzo IP valido.");
						setTimeout(function(){$("#ip").select()}, 1);
					}
				}
			});
			$("#searchform table").append('<tr><td><label for="type">Restringi la ricerca per tipo di file</label></td><td><input type="text" name="filetype" id="type" size="13" value="' + filetype + '" placeholder="Estensione del file" /></td></tr>');
			// Append button
			$("#searchform").append("<button>Cerca</button>");
			$("#q").focus();
		} else {
			if($._GET("op") != undefined){
				op = $._GET("op");
			} else {
				op = "query";
			}
			if($._GET("q") != undefined){
				if($._GET("q").length > 0){
					$("#page_content_main").fadeOut(150);
					$("#loader").fadeIn(150);
					$("#main_search").fadeOut(150);
					// Removes "+" character from the search term
					var query = decodeURI($._GET("q")).replace(/\+/g, " ");
					switch($._GET("nas")){
						case "ninuxoo":
							search_uri = "read_json.php";
							break;
						case "other":
							search_uri = $._GET("ip") + "/local_search.php";
							break;
						case "this":
						default:
							search_uri = "local_search.php";
							break;
					}		
					$.getJSON(search_uri, {op: op, q: $._GET("q"), nresults: $._GET("nresults"), ip: $._GET("ip"), filetype: $._GET("filetype")}, function(data) {
						if(data.nresults > 0){
							var more_search_data = "";
							// Sets the browser's title
							$("title").text("NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Risultati della ricerca per \"" + query + "\" - Ninux.org");
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
							$("#top_menu tr:first").append('<td rowspan="2"><div id="search"><table cellspacing="0" cellpadding="0"><tr><td><form id="searchform" action="" method="get">' + $("#searchform").html() + '</form></td><td><a href="?s=advanced' + more_search_data + '" title="Ricerca avanzata"></a></td></tr></table></div></td>');
							$("#searchform input[type=search]").val(query);
							//Clear data
							$("#main_search").remove();
							$("#searchlabels").html("");
							$(".resultstree").html("");
							
							$("#page_content").prepend('<h1>Ricerca di &quot;' + query + '&quot;</h1>');
							s = data.nresults + " risultati trovati in " + Math.round(data.searchtime*1000)/1000 + " secondi";
							$("#page_content").append("<h2>" + s + "</h2>");
							$("#page_content").append('<div id="right_menu"><h3>STATISTICHE<span>di ricerca</span></h3></div>');
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
								s += '<a href="#res' + index + '">';
								s += value.resultlabel;
								s += "</a><span>";
								s += value.nresults;
								s += " risultati</span></li>";
								if(index == 0){
									$("#right_menu").append('<ul>' + s + '</ul>');
								} else {
									if(index == 1){
										$("#right_menu").append('<h4>Altri risultati trovati:</h4><ul>' + s + '</ul>');
									} else {
										$("#right_menu ul:last-child").append(s);
									}
								}
								var ul_tree_control = '<ul id="research_term_control_' + index + '" class="treecontrol"><li><a href="javascript:void(0);" title="Espandi tutti gli alberi" class="open"></a></li><li><a href="javascript:void(0);" title="Racchiudi tutti gli alberi" class="close"></a></li><li><a href="javascript:void(0);" title="Inverti modalità: se aperto chiudi, se chiuso apri" class="toggle"></a></li></ul>';
								
								if(index == 0) {
									$("#page_content").append('<div class="search_results"><h1>Termine di ricerca</h1>' + ul_tree_control + '<ul id="treeview_' + index + '" class="exactresults filetree treeview"></ul><div id="otherresults"></div></div>');
									var start_collapsed = false;
									
									$(".filetree a[title]").qtip({style: {border: {width: 2, radius: 3}, color: "white", name: "dark", textAlign: "center", tip: true}, position: {corner: {target: "topMiddle", tooltip: "bottomMiddle"}}});
									$(".treecontrol a[title]").qtip({style: {border: {width: 2, radius: 3}, color: "white", name: "dark", textAlign: "center", tip: true}, position: {corner: {target: "topRight", tooltip: "bottomLeft"}}});
								} else {
									$("#otherresults").append('<h1>Risultati per &quot;' + value.resultlabel + '&quot;</h1>' + ul_tree_control + '<ul id="treeview_' + index + '" class="otherresults filetree treeview"></ul>')
									var start_collapsed = true;
								}
								s = $.ultrie(value.resourcetrie, "", "");
								$(".filetree").append(s);
								
								$("#treeview_" + index).treeview({
									control: "#research_term_control_" + index,
									animated: "fast",
									collapsed: start_collapsed
								});
							});
							$("#page_content").removeHighlight().highlight(query).find("#right_menu").removeHighlight();
							$("#loader").fadeOut(300, function(){
								$("#page_content").slideDown(600);
							});
							/*
							$("a.zoombox").zoombox({
								theme: 'simple',
								opacity: 0.9,
								duration: 300,
								animation: true,
								gallery: true,
								autoplay: false
							});
							*/
						} else {
							$("#loader").hide();
							$("#page_content_main").fadeIn(150);
							$("#main_search").fadeIn(150);
							$("#content").prepend('<p class="noresults">Nessun risultato trovato con la ricerca per \"' + query + '\"</p>');
							get_stats();
						}
					});
				}
			} else {
				$("#whatsnew").click(function(e) {
					e.preventDefault();
					$.getJSON("local_search.php", {op: "whatsnew"}, function(data) {
						$("#page_content_main").html(data.nresults);
					});
					return false;
				});
				get_stats();
			}
		}
		$("select").chosen();
	});
	</script>
</head>
<body>
	<div id="top_menu">
		<table>
			<tr>
				<td>
					<ul>
						<li><a href="javascript:void(0);" id="whatsnew" title="Nuovi files indicizzati">Novit&agrave;</a></li>
						<li><a href="http://10.168.177.178:8888/" title="Ascolta la musica in rete">Juke Box</a></li>
						<li><a href="http://ninuxoo.ninux.org/cgi-bin/proxy_wiki.cgi?url=Elenco_Telefonico_rete_VoIP_di_ninux.org" title="Elenco telefonico"><acronym title="Voice over IP">VoIP</acronym></a></li>
						<li><a href="<?php print $NAS_absolute_uri; ?>/Meteo" title="Visualizza dati meteo in tempo reale">Meteo</a></li>
						<li class="separator">&nbsp;</li>
						<li><a href="http://blog.ninux.org/" title="Vai al nostro Blog">Blog</a></li>
						<li><a href="http://wiki.ninux.org/" title="Vai al nostro Wiki">Wiki</a></li>
					</ul>
				</td>
				<td>
					<ul id="second_menu">
						<li><a href="http://10.162.0.85/" title="Controlla la posta">Posta</a></li>
					</ul>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<div id="main_container">
		<div id="main_header">
			<table>
				<tr>
					<td>
						<a href="">
							<img src="<?php print $NAS_absolute_uri; ?>/common/media/img/logo.png" alt="Logo Ninuxoo" />
						</a>
						<h1><?php print $config["NAS"]["name"]; ?></h1>
					</td>
				</tr>
			</table>
		</div>
		<div id="container">
			<div id="content">
				<div id="loader"></div>
				<table id="main_search" cellspacing="0" cellpadding="0">
					<tr>
						<td>
							<form id="searchform" action="" method="get">
								<input type="hidden" name="op" value="query" style="display: none;">
								<input type="hidden" name="nresults" value="200" style="display: none;">
								<input type="search" name="q" value="" placeholder="Cerca in Ninuxoo" >
								<input type="submit" value="" style="display: none;">
							</form>
						</td>
						<td>
							<a href="<?php print $NAS_absolute_uri; ?>/?s=advanced<?php if(isset($_GET["q"]) && trim($_GET["q"]) !== ""){ print "&q=" . $_GET["q"]; } ?>" title="Ricerca avanzata"></a>
						</td>
					</tr>
				</table>
				<div id="page_content_main">
					<div id="resstats" class="resstats"><span style="color: #aaa; font-style: italic;">Calcolo statistiche...</span></div>
				</div>
			</div>
			<!-- end outputhead -->
			<!-- outputtail -->
			<div id="footer">
				Powered by Ninux Community ~ the Ninux Software &amp; Communication Team :: used icons made by <a href="http://www.picol.org/" target="_blank" title="PIctorial COmmunication Language - Richiede inoltro a Internet">Picol project</a>
			</div>
		</div>
	</div>
</body>
</html>
<!-- end outputtail -->
