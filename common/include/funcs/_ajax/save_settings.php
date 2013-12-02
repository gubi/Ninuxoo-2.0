<?php
header("Content-type: text/plain");

require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
$conf->conf_replace("use_editor_always", ($output["allow_editor_always"] == "on") ? "true" : "false", "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
$conf->conf_replace("allow_user_registration", ($output["allow_user_registration"] == "on") ? "true" : "false", "../../conf/general_settings.ini");
$conf->conf_replace("session_length", $output["session_length"], "../../conf/general_settings.ini");

print json_encode(array("data" => "ok"));
?>