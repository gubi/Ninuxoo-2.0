<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$config = new Config_Lite();
$config->read("../../conf/user/" . sha1($output["username"]) . "/user.conf");
$config->set("User", "pass", sha1($output["password"]));

$config->save();
$saved_config = parse_ini_string($config, 1);
if($saved_config["User"]["pass"] == sha1($output["password"])) {
	$resp["data"] = "ok";
} else {
	$resp["data"] = "no";
}
print json_encode($resp);
?>