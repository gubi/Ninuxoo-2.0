<?php
header("Content-type: text/plain; charset=utf-8");
include("../../lib/IMDb-Scraper-master/imdb_scraper.php");
include("../../lib/JSON.php");
if(!function_exists("json_encode")) {
	function json_encode($data) {
		$json = new Services_JSON();
		return $json->encode($data);
	}
}
if(!function_exists("json_decode")) {
	function json_decode($data) {
		$json = new Services_JSON();
		return $json->decode($data);
	}
}

$title = preg_replace("/ \[.*?\]/", "", trim(urldecode($_GET["title"])));
$place = trim(urldecode($_GET["place"]));
$output = array("dati" => IMDbScraper::get($title, $place));

if($_GET["debug"] == "true"){
	print_r($output);
}
print json_encode($output);
?>