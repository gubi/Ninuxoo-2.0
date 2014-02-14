<?php
header("Content-type: text/plain");

require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
$conf->conf_replace("nas_name", $output["nas_name"], "../../conf/config.ini");
$conf->conf_replace("nas_description", $output["nas_description"], "../../conf/config.ini");
$conf->conf_replace("root_share_dir", $output["root_share_dir"], "../../conf/config.ini");
$conf->conf_replace("http_root", $output["uri_address"], "../../conf/config.ini");
$conf->conf_replace("root_dir", $output["server_root"], "../../conf/config.ini");
$conf->conf_replace("listing_file_dir", $output["api_dir"], "../../conf/config.ini");

$scan_shares = (strlen($output["shared_paths"]) > 0) ? "/" . str_replace(",", "\n/", rawurldecode($output["shared_paths"])) : "";
if($fss = @fopen($output["server_root"] . "common/include/conf/scan_shares", "w")) {
	fwrite($fss, $scan_shares . PHP_EOL);
	fclose($fss);
}

$conf->conf_replace("show_ip", ($output["show_ip"] == "on") ? "true" : "false", "../../conf/general_settings.ini");
$conf->conf_replace("allow_advanced_research", ($output["allow_advanced_research"] == "on") ? "true" : "false", "../../conf/general_settings.ini");
$conf->conf_replace("research_type", $output["research_type"], "../../conf/general_settings.ini");
$conf->conf_replace("research_results", $output["research_results"], "../../conf/general_settings.ini");

print json_encode(array("data" => "ok"));
?>