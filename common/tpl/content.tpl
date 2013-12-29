<div id="main_search">
	<form id="searchform" action="" method="get">
		<div class="input-group">
			<input type="search" id="search_input" class="form-control input-lg" name="q" value="<?php print (isset($_GET["q"]) ? $_GET["q"] : ""); ?>" placeholder="Cerca in Ninuxoo" >
			<input type="hidden" name="op" value="query" style="display: none;">
			<input type="hidden" name="nresults" value="200" style="display: none;">
			<input type="submit" value="" style="display: none;">
			<a class="input-group-addon btn btn-default" href="./Ricerca_avanzata<?php if(isset($_GET["q"]) && trim($_GET["q"]) !== ""){ print "/Cerca:" . $_GET["q"]; } ?>" title="Ricerca avanzata"><span class="glyphicon glyphicon-cog"></span></a>
		</div>
	</form>
</div>
<div id="page_content_main">
	<div id="resstats" class="resstats"><span style="color: #aaa; font-style: italic;">Calcolo statistiche...</span></div>
</div>