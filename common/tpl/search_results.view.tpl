<?php
require_once("common/include/classes/rsa.class.php");
require_once("common/include/lib/mime_types.php");

$rsa = new rsa();
$hash = rawurldecode(str_replace("/Scheda:?", "", $_SERVER["REQUEST_URI"]));
$file = trim($rsa->simple_decrypt($hash));
$info = pathinfo($file);
$filename = $info["basename"];

$dhash = str_replace("Scheda", "Scarica", $_SERVER["REQUEST_URI"]);
?>
<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.highlight-4.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_search.js"></script>

<span style="display: none;" id="search_type"><?php print $GLOBALS["search_type"]; ?></span>
<span style="display: none;" id="search_ip"><?php print $GLOBALS["search_ip"]; ?></span>
<span style="display: none;" id="search_filetype"><?php print $GLOBALS["search_filetype"]; ?></span>
<div id="result_content">
	<div class="row">
		<div class="panel panel-default right col-lg-3" id="affix">
			<div class="panel-heading list-group">
				<a class="list-group-item active" href="<?php print $dhash; ?>">
					<h4 class="list-group-item-heading"><span class="fa fa-cloud-download"></span>&nbsp;&nbsp;Scarica</h4>
					<p class="list-group-item-text">File <span id="file_ext"><?php print $info["extension"]; ?></span> (<span id="file_mime"><?php print $mime_type[$info["extension"]]; ?></span>)</p>
				</a>
			</div>
			<div class="panel-heading">
				<big class="lead text-primary">STATISTICHE<small class="help-block">del file</small></big>
			</div>
			<div class="panel-body">
				<dl class="dl-horizontal">
					<dt>Ultima modifica:</dt><dd id="file_last_edit"><?php print gmdate("d/m/Y H:i:s", filemtime($file)); ?></dd>
					<dt>Peso:</dt><dd id="file_weight"><?php print (filesize($file)/1024); ?></dd>
				</dl>
				<br />
				<hr />
				<p class="lead media-heading text-muted">Risultati</p>
				<dl class="dl-horizontal">
					<dt>Termini ricercati:</dt><dd id="nlabels"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
					<dt>Numero di risultati:</dt><dd id="nresults"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
					<dt>Durata della ricerca:</dt><dd id="searchtime"><span class="fa fa-refresh fa-spin text-muted"></span></dd>
				</dl>
			</div>
		</div>
		<div class="panel panel-default col-lg-8">
			<div class="panel-heading">
				<span class="lead text-primary">
					<span class="fa fa-file-o"></span>&nbsp;&nbsp;<span id="search_term"><?php print $filename; ?></span><small class="help-block">Scheda del file</small>
				</span>
			</div>
		</div>
		<div class="panel panel-default col-lg-8">
			<div class="panel-heading">
				<span class="lead text-primary">
					<span class="fa fa-search"></span>&nbsp;&nbsp;Duplicati: <small class="help-block">Percorsi in cui vi &egrave; un file con lo stesso nome</small>
				</span>
			</div>
			<div class="panel-body" id="search_results"></div>
		</div>
	</div>
</div>