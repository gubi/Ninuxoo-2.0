<?php
header("Content-type: text/plain; charset=utf-8");
include("../../lib/IMDb-Scraper-master/imdb_scraper.php");
require_once("json_service.php");

$title = preg_replace("/ \[.*?\]/", "", trim(urldecode($_GET["title"])));
$place = trim(urldecode($_GET["place"]));
$output = array("dati" => IMDbScraper::get($title, $place));

if($_GET["debug"] == "true"){
	print_r($output);
}
print json_encode($output);
?>