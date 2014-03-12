<?php
header("Content-type: text/plain; charset=utf-8");

$config = parse_ini_file("../../conf/config.ini", true);
$general_settings = parse_ini_file("../../conf/general_settings.ini", true);
if(file_exists(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . "/.ninuxoo_cache/" . base64_encode($_SERVER["QUERY_STRING"]) . ".json")) {
	$json = file_get_contents(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . "/.ninuxoo_cache/" . base64_encode($_SERVER["QUERY_STRING"]) . ".json");
	if($_GET["debug"] == "true") {
		print_r(json_decode($json));
	} else {
		print $json;
	}
	exit();
} else {
	require_once("../../classes/get_semantic_data.class.php");

	$semantic_data = new semantic_data();
	$semantic_data->output("html");
	$semantic_data->format("array");
	if(isset($_GET["output"])) {
		$semantic_data->output($_GET["output"]);
	}
	if($_GET["debug"] == "true") {
		$semantic_data->debug(true);
	}
	switch(strtolower($_GET["type"])) {
		case "album":
			$semantic_data->audio(rawurldecode($_GET["title"]));
			break;
		case "book":
			$semantic_data->book(rawurldecode($_GET["title"]));
			break;
		case "film":
			$semantic_data->film(rawurldecode($_GET["title"]));
			break;
		case "person":
			$semantic_data->person(rawurldecode($_GET["artist"]));
			break;
		case "thing":
		case "work":
			$semantic_data->thing(rawurldecode($_GET["title"]));
			break;
	}
	$result = $semantic_data->export(false);
	unset($result[0]["time"]);
	$res = json_encode($result);
	
	if(trim($res) && $res !== "null" && $res !== "no results") {
		if($general_settings["caching"]["allow_caching"] == "true" && $general_settings["caching"]["save_semantic_data"] == "true") {
			file_put_contents(str_replace("//", "/", $config["NAS"]["root_share_dir"] . "/") . "/.ninuxoo_cache/" . base64_encode($_SERVER["QUERY_STRING"]) . ".json", $res . PHP_EOL);
		}
		print $res;
	} else {
		print json_encode(array("data" => null));
	}
}
?>