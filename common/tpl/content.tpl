<div id="content">
	<div id="loader"></div>
	<table id="main_search" cellspacing="0" cellpadding="0">
		<tr>
			<td>
				<form id="searchform" action="" method="get">
					<input type="hidden" name="op" value="query" style="display: none;">
					<input type="hidden" name="nresults" value="200" style="display: none;">
					<input type="search" id="search_input" name="q" value="<?php print $_GET["q"]; ?>" placeholder="Cerca in Ninuxoo" >
					<input type="submit" value="" style="display: none;">
				</form>
			</td>
			<td>
				<a href="<?php print $NAS_absolute_uri; ?>/?s=advanced<?php if(isset($_GET["q"]) && trim($_GET["q"]) !== ""){ print "&q=" . $_GET["q"]; } ?>" title="Ricerca avanzata" id="advanced_search_btn"></a>
			</td>
		</tr>
	</table>
	<div id="page_content_main">
		<div id="resstats" class="resstats"><span style="color: #aaa; font-style: italic;">Calcolo statistiche...</span></div>
	</div>
</div>