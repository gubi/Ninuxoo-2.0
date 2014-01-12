<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.highlight-4.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_search.js"></script>
<script type="text/javascript" src="common/js/include/check_cookie.js"></script>
<?php
parse_str($_SERVER["QUERY_STRING"], $out);
unset($out["s"]);
?>
<span style="display: none;" id="result_type">Search</span>
<span style="display: none;" id="search_type"><?php print (isset($out["op"]) && strlen($out["op"]) > 0) ? $out["op"] : $GLOBALS["search_type"]; ?></span>
<div id="search_loader" class="center-block">
	<div class="col-lg-1"></div>
	<div class="col-lg-10">
		<div class="progress progress-striped active">
			<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%"></div>
		</div>
	</div>
	<div class="col-lg-1"></div>
	<span class="help-block centered col-lg-12">Ottimizzo la ricerca...</span>
</div>
<div id="search_content">
	<div class="row">
		<div class="panel right col-lg-3" id="affix">
			<div class="panel">
				<big class="lead text-primary"><span class="fa fa-tasks"></span>&nbsp;&nbsp;STATISTICHE</big>
			</div>
			<div class="panel">
				<p class="lead media-heading text-muted"><span class="fa fa-bar-chart-o"></span>&nbsp;&nbsp;Risultati di ricerca</p>
				<dl class="dl-horizontal">
					<dt>Termini ricercati:</dt><dd id="nlabels"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
					<dt>Numero di risultati:</dt><dd id="nresults"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
					<dt>Durata della ricerca:</dt><dd id="searchtime"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
				</dl>
				<br />
				<p class="lead media-heading text-muted"><span class="fa fa-wrench"></span>&nbsp;&nbsp;Parametri di ricerca</p>
				<dl class="dl-horizontal">
					<dt>Tipo di ricerca:</dt><dd><?php print $search_types[$GLOBALS["search_type"]]; ?></dd>
					<dt>Massimi risultati:</dt><dd id="search_num_results"><?php print (isset($out["nresults"]) && strlen($out["nresults"]) > 0) ? $out["nresults"] : $GLOBALS["search_num_results"]; ?></dd>
					<dt>Directory di ricerca:</dt><dd id="search_path"><?php print (isset($out["path"]) && strlen($out["path"]) > 0) ? $out["path"] : ((strlen($GLOBALS["search_path"]) > 0) ? $GLOBALS["search_path"] : "-"); ?></span>
					<dt>Tipo di file:</dt><dd id="search_filetype"><?php print (isset($out["filetype"]) && strlen($out["filetype"]) > 0) ? $out["filetype"] : ((strlen($GLOBALS["search_filetype"]) > 0) ? $GLOBALS["search_filetype"] : "-"); ?></dd>
					<dt>IP di destinazione:</dt><dd id="search_ip"><?php print (isset($out["search_ip"]) && strlen($out["search_ip"]) > 0) ? $out["search_ip"] : ((strlen($GLOBALS["search_ip"]) > 0) ? $GLOBALS["search_ip"] : "-"); ?></dd>
				</dl>
				<hr />
				<a class="btn btn-default right" href="./Ricerca_avanzata/Cerca:<?php print $search_term . ((count($out) > 0) ? "?" . http_build_query($out) : ""); ?>">Raffina la ricerca&nbsp;&nbsp;<span class="fa fa-edit"></span></a>
			</div>
		</div>
		<div class="panel col-lg-8">
			<div class="panel">
				<span class="lead text-primary">
					<span class="fa fa-search"></span>&nbsp;&nbsp;Ricerca per &quot;<span id="search_term"><?php print $GLOBALS["search_term"]; ?></span>&quot;<small class="help-block">Risultati della ricerca</small>
				</span>
			</div>
			<div class="panel" id="search_results"></div>
		</div>
	</div>
</div>