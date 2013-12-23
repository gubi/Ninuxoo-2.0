<?php
header("Content-type: text/plain");

require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
$conf->conf_replace("show_ip", ($output["show_ip"] == "on") ? "true" : "false", "../../conf/general_settings.ini");
$conf->conf_replace("allow_advanced_research", ($output["allow_advanced_research"] == "on") ? "true" : "false", "../../conf/general_settings.ini");
$conf->conf_replace("research_type", $output["research_type"], "../../conf/general_settings.ini");
$conf->conf_replace("research_results", $output["research_results"], "../../conf/general_settings.ini");

print json_encode(array("data" => "ok"));
?>