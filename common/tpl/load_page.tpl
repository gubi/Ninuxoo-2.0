<?php
if(file_exists("common/md/pages/" . $_GET["s"] . ".md")) {
	$page = file_get_contents("common/md/pages/" . $_GET["s"] . ".md");
	print '<div>' . str_replace('href="http', 'target="_blank" href="http', Markdown($page)) . '</div>';
} else {
	require_once("common/tpl/404.tpl");
}
?>