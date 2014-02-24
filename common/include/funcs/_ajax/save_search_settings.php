<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/config.ini");
$config->set("NAS", "nas_name", $output["nas_name"]);
$config->set("NAS", "nas_description", $output["nas_description"]);
$config->set("NAS", "root_share_dir", $output["root_share_dir"]);
$config->set("NAS", "http_root", $output["uri_address"]);
$config->set("NAS", "root_dir", $output["server_root"]);
$config->set("NAS", "listing_file_dir", $output["api_dir"]);
$config->save();

$scan_shares = (strlen($output["shared_paths"]) > 0) ? "/" . str_replace(",", "\n/", rawurldecode($output["shared_paths"])) : "";
if($fss = @fopen($output["server_root"] . "common/include/conf/scan_shares", "w")) {
	fwrite($fss, $scan_shares . PHP_EOL);
	fclose($fss);
}

$general_settings = new Config_Lite();
$general_settings->read("../../conf/general_settings.ini");
$general_settings->set("searches", "show_ip", ($output["show_ip"] == "on") ? "true" : "false");
$general_settings->set("searches", "allow_advanced_research", ($output["allow_advanced_research"] == "on") ? "true" : "false");
$general_settings->set("searches", "research_type", $output["research_type"]);
$general_settings->set("searches", "research_results", $output["research_results"]);
$general_settings->save();

print json_encode(array("data" => "ok"));
?>