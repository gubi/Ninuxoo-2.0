<?php
$query = explode(":", $_GET["s"]);

switch(strtolower($query[0])) {
	case "esplora":
		require_once("common/tpl/search_results.explore.tpl");
		break;
	case "scheda":
		require_once("common/tpl/search_results.view.tpl");
		break;
	case "cerca":
	default:
		require_once("common/tpl/search_results.search.tpl");
		break;
}
?>
