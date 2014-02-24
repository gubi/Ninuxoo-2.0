<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
	$chat_window = trim($output["panel_window"]);
$config->set("Chat", "chat_window", $chat_window);

$config->save();
$saved_config = parse_ini_string($config, 1);
if($saved_config["Chat"]["chat_window"] == $chat_window) {
	$resp["data"] = "ok";
} else {
	$resp["data"] = "no";
}
print json_encode($resp);
?>
