
<link href="common/js/chosen/chosen-bootstrap.css" rel="stylesheet" />
<script type="text/javascript" src="common/js/chosen/chosen.jquery.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$("select").chosen();
});
</script>
<form method="get" action="" id="advanced_search_frm" class="form-horizontal" role="form" onsubmit="return false;">
	<div class="panel panel-default">
		<div class="panel-heading"><span class="lead text-primary"><span class="fa fa-search"></span>&nbsp;&nbsp;Ricerca <small class="help-block">Aggiungi dei filtri alla ricerca</small></span></div>
		<div class="panel-body">
			<div class="form-group">
				<label for="q" class="col-sm-2 control-label">Query della ricerca</label>
				<div class="col-sm-6">
					<input type="search" class="form-control" name="q" id="q" value="<?php print (strpos($_GET["q"], "Cerca:") !== false) ? urldecode(str_replace("Cerca:", "", $_GET["q"])) : ""; ?>" autofocus tabindex="1" />
				</div>
			</div>
			<div class="form-group">
				<label for="op" class="col-sm-2 control-label">Tipo di ricerca</label>
				<div class="col-sm-4">
					<select name="op" id="op" tabindex="2">
						<option value="query" selected>Tutti i risultati possibili</option>
						<option value="exactquery">Testo esatto</option>
						<option value="orquery">Singola parola</option>
						<option value="likequery">Somiglianza nei termini</option>
						<option value="whatsnew">Nell'ultima scansione del crawler</option>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label for="nresults" class="col-sm-2 control-label">Numero di risultati</label>
				<div class="col-sm-1">
					<input type="number" class="form-control" name="nresults" id="nresults" min="10" max="400" size="1" maxlength="3" value="" tabindex="3" />
				</div>
			</div>
		</div>
		<div class="panel-body">
			<div class="form-group">
				<label for="ip" class="col-sm-2 control-label">Ricerca in un indirizzo IP conosciuto (e collegato)</label>
				<div class="col-sm-2">
					<input type="text" class="form-control" name="ip" id="ip" size="12" value="" placeholder="Indirizzo IP" tabindex="4" />
				</div>
			</div>
			<div class="form-group">
				<label for="type" class="col-sm-2 control-label">Restringi la ricerca per tipo di file</label>
				<div class="col-sm-1">
					<input type="text" class="form-control" name="filetype" id="type" maxlength="4" size="13" value="" placeholder="jpg" tabindex="5" />
				</div>
			</div>
		</div>
	</div>
	<button class="btn btn-primary right" id="advanced_search_btn">Cerca&nbsp;&nbsp;&nbsp;<span class="fa fa-arrow-right"></span></button>
</form>