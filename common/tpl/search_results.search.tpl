<link rel="stylesheet" href="common/js/jquery.treeview/jquery.treeview.css" type="text/css" media="screen" />
<script type="text/javascript" src="common/js/jquery.treeview/jquery.treeview.js"></script>
<script type="text/javascript" src="common/js/jquery.highlight-4.js"></script>
<script type="text/javascript" src="common/js/jCryption/jquery.jcryption.3.0.js"></script>
<script type="text/javascript" src="common/js/include/common.js"></script>
<script type="text/javascript" src="common/js/include/local_search.js"></script>
<div id="search_loader" class="center-block">
	<div class="progress progress-striped active">
		<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
			<span class="sr-only">Ottimizzo la ricerca...</span>
		</div>
	</div>
	<span class="help-block centered">Ottimizzo la ricerca...</span>
</div>
<div id="search_content">
	<div class="row">
		<div class="panel panel-default right col-lg-3" id="affix">
			<div class="panel-heading">
				<big class="lead text-primary">STATISTICHE<small class="help-block">di ricerca</small></big>
			</div>
			<div class="panel-body">
				<p class="lead media-heading text-muted">Parametri di ricerca</p>
				<dl class="dl-horizontal">
					<dt>Tipo di ricerca:</dt><dd id="search_type"><?php print $GLOBALS["search_type"]; ?></dd>
					<dt>Risultati per pagina:</dt><dd id="search_num_results"><?php print $GLOBALS["search_num_results"]; ?></dd>
					<dt <?php print ($GLOBALS["search_ip"] == "-") ? 'class="text-muted"' : ""; ?>>IP di destinazione:</dt><dd id="search_ip"><?php print $GLOBALS["search_ip"]; ?></dd>
					<dt <?php print ($GLOBALS["search_filetype"] == "-") ? 'class="text-muted"' : ""; ?>>Tipo di file:</dt><dd id="search_filetype"><?php print $GLOBALS["search_filetype"]; ?></dd>
				</dl>
				<a class="btn right" href="./Ricerca_avanzata/Cerca:<?php print $search_term; ?>"><span class="fa fa-filter"></span>&nbsp;&nbsp;Correggi</a>
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
					<span class="fa fa-search"></span>&nbsp;&nbsp;Ricerca di &quot;<span id="search_term"><?php print $GLOBALS["search_term"]; ?></span>&quot;<small class="help-block">Risultati della ricerca</small>
				</span>
			</div>
			<div class="panel-body" id="search_results"></div>
		</div>
	</div>
</div>