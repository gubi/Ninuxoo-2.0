<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
	$panel_width = ((int)str_replace("px", "", trim($output["size"])) + 2);
$config->set("Chat", "panel_width", $panel_width);

$config->save();
$saved_config = parse_ini_string($config, 1);
if($saved_config["Chat"]["panel_width"] == $panel_width) {
	$resp["data"] = "ok";
} else {
	$resp["data"] = "no";
}
print json_encode($resp);
?>
