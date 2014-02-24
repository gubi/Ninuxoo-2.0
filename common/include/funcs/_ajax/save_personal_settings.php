<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["user_username"]) . "/user.conf");

$config->set("User", "name", $output["name"]);
$config->set("Notification", "new_files", ($output["new_files"] == "on") ? "true" : "false");
$config->set("Notification", "new_chat_messages", ($output["new_chat_messages"] == "on") ? "true" : "false");
$config->set("Chat", "nick", $output["nick"]);
$config->set("Chat", "personal_message", $output["personal_message"]);
$config->set("Chat", "chat_status", $output["chat_status"]);
$config->set("Chat", "show_ip", ($output["show_ip"] == "on") ? "true" : "false");
$config->set("Chat", "refresh_interval", $output["refresh_interval"]);

if($config->save()) {
	$resp["data"] = "ok";
} else {
	$resp["data"] = "no";
}
print json_encode($resp);
?>