<div id="loader"></div>
<div id="main_search">
	<form id="searchform" action="" method="get">
		<input type="hidden" name="op" value="query" style="display: none;">
		<input type="hidden" name="nresults" value="200" style="display: none;">
		<input type="search" id="search_input" name="q" value="<?php print (isset($_GET["q"]) ? $_GET["q"] : ""); ?>" placeholder="Cerca in Ninuxoo" >
		<input type="submit" value="" style="display: none;">
		<a href="<?php print $NAS_absolute_uri; ?>/?s=advanced<?php if(isset($_GET["q"]) && trim($_GET["q"]) !== ""){ print "&q=" . $_GET["q"]; } ?>" title="Ricerca avanzata" id="advanced_search_btn"></a>
	</form>
</div>
<div id="page_content_main">
	<div id="resstats" class="resstats"><span style="color: #aaa; font-style: italic;">Calcolo statistiche...</span></div>
</div>