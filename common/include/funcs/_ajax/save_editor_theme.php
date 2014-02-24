<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["user_username"]) . "/user.conf");
$config->set("User", "editor_theme", $output["code_theme"]);

$config->save();
$saved_config = parse_ini_string($config, 1);
if($saved_config["User"]["editor_theme"] == $output["code_theme"]) {
	$resp["data"] = "ok";
} else {
	$resp["data"] = "no";
}
print json_encode($resp);
?>