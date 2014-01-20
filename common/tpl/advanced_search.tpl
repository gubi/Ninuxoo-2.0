<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript">
function ucfirst(string) {
	return string.charAt(0).toUpperCase() + string.slice(1);
}
function advanced_search() {
	var query = ($("#query").val().length > 0) ? ucfirst($("#query").val()) : "",
	op = ($("#op").val().length > 0) ? $("#op").val() : "",
	nresults = ($("#nresults").val().length > 0) ? $("#nresults").val() : "",
	path = ($("#path").val().length > 0) ? $("#path").val() : "",
	ip = ($("#ip").val().length > 0) ? $("#ip").val() : "",
	filetype = ($("#filetype").val().length > 0) ? $("#filetype").val() : "";
	
	$("#query").val(query);
	
	if(query.length > 0) {
		var ref = query,
		q = [];
		
		if (op.length > 0 || nresults.length > 0 || path.length > 0 || filetype.length > 0) {
			ref += "?";
		}
		if(op.length > 0) {
			q.push("op=" + op);
		}
		if(nresults.length > 0) {
			q.push("nresults=" + nresults);
		}
		if(path.length > 0) {
			q.push("path=" + path);
		}
		if(filetype.length > 0) {
			q.push("filetype=" + filetype);
		}
		ref += q.join("&");
		window.location.replace("./Cerca:" + ref);
	}
}
$(document).ready(function() {
	$("select").chosen();
});
</script>
<?php
parse_str($_SERVER["QUERY_STRING"], $out);
print_r($_GET["op"]);
?>
<form method="post" action="" id="advanced_search_frm" class="form-horizontal" role="form" onsubmit="advanced_search(); return false;">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-search"></span>&nbsp;&nbsp;Ricerca <small class="help-block">Aggiungi dei filtri alla ricerca</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="query" class="col-sm-3 control-label">Query della ricerca</label>
				<div class="col-sm-6">
					<input type="search" class="form-control" name="query" id="query" value="<?php print $GLOBALS["search_term"]; ?>" autofocus tabindex="1" />
					<span class="help-block">Nota: la ricerca &egrave; di tipo case-insensitive (insensibile alle maiuscole)</span>
				</div>
			</div>
			<div class="form-group">
				<label for="op" class="col-sm-3 control-label">Tipo di ricerca</label>
				<div class="col-sm-4">
					<select name="op" id="op" tabindex="2">
						<?php
						foreach($search_types as $search_val => $search_text) {
							$default = (isset($_GET["op"]) && trim($_GET["op"]) !== "") ? $_GET["op"] : $GLOBALS["search_type"];
							if($search_val == $default) {
								$selected = " selected";
							} else {
								$selected = "";
							}
							print '<option value="' . $search_val . '"' . $selected . '>' . $search_text . '</option>';
						}
						?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="path" class="col-sm-3 control-label">Directory di ricerca</label>
				<div class="col-sm-5">
					<div class="input-group">
						<span class="input-group-addon"><tt>root/</tt></span>
						<input type="text" class="form-control" style="font-family: monospace;" name="path" id="path" value="<?php print $_GET["path"]; ?>" tabindex="3" />
					</div>
				</div>
			</div>
			<div class="form-group">
				<label for="nresults" class="col-sm-3 control-label">Numero di risultati massimi</label>
				<div class="col-sm-1">
					<input type="number" class="form-control" name="nresults" id="nresults" min="10" max="400" size="1" maxlength="3" value="<?php print (isset($_GET["nresults"]) && trim($_GET["nresults"]) !== "") ? $_GET["nresults"] : $GLOBALS["search_num_results"]; ?>" tabindex="3" />
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="ip" class="col-sm-3 control-label">Ricerca in un host conosciuto (e collegato)</label>
				<div class="col-sm-3">
					<input type="text" class="form-control" name="ip" id="ip" size="12" value="<?php print $_GET["ip"]; ?>" placeholder="Indirizzo IPv4, IPv6 o DNS" tabindex="4" />
				</div>
			</div>
			<div class="form-group">
				<label for="filetype" class="col-sm-3 control-label">Restringi la ricerca per tipo di file</label>
				<div class="col-sm-1">
					<input type="text" class="form-control" name="filetype" id="filetype" maxlength="4" size="13" value="<?php print $_GET["filetype"]; ?>" placeholder="jpg" tabindex="5" />
				</div>
			</div>
		</div>
	</div>
	<button class="btn btn-primary right" id="advanced_search_btn">Cerca&nbsp;&nbsp;&nbsp;<span class="fa fa-arrow-right"></span></button>
</form>