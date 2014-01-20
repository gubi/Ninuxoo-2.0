<?php
header("Content-type: text/plain");

require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();

$conf->conf_replace("name", $output["name"], "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
$conf->conf_replace("new_files", ($output["new_files"] == "on") ? "true" : "false", "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
$conf->conf_replace("new_chat_messages", ($output["new_chat_messages"] == "on") ? "true" : "false", "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
$conf->conf_replace("show_ip", ($output["show_ip"] == "on") ? "true" : "false", "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
$conf->conf_replace("refresh_interval", $output["refresh_interval"], "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");

print json_encode(array("data" => "ok"));
?>