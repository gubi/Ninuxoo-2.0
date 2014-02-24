<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
	$panel_status = trim($output["status"]);
$config->set("Chat", "panel_status", $panel_status);

$config->save();
$saved_config = parse_ini_string($config, 1);
if($saved_config["Chat"]["panel_status"] == $panel_status) {
	$resp["data"] = "ok";
} else {
	$resp["data"] = "no";
}
print json_encode($resp);
?>
