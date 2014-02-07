<?php
header("Content-type: text/plain");
require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
$conf->conf_replace("chat_status", $output["status"], "../../conf/user/" . sha1($output["username"]) . "/user.conf");
$res["status"] = $output["status"];

print json_encode($res);
?>
