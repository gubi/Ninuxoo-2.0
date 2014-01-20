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
$("#searchform table").append('<tr><td><label for="q">Query della ricerca</label></td><td><input type="search" name="q" id="q" value="' + decodeURI($._GET("q")).replace(/\+/g, " ") + '" /></td></tr></tr>');
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