<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["username"]) . "/user.conf");
$config->set("Chat", "panel_status", $output["status"]);

$config->save();
$saved_config = parse_ini_string($config, 1);
if($saved_config["Chat"]["panel_status"] == $output["status"]) {
	$resp["status"] = $output["status"];
} else {
	$resp["status"] = "no";
}
print json_encode($resp);
?>
