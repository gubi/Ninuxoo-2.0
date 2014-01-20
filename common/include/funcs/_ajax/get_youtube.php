<?php
header("Content-type: text/plain; charset=utf-8");
include("../../lib/youtube.php");
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

$title = trim(urldecode($_GET["title"]));
$search = new Search();
$search->setQuery($title);
$videos = YoutubeDataApi::search($search);

if($_GET["debug"] == "true"){
	print_r($videos);
}
print json_encode($videos);
?>