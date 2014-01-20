<?php
header("Content-type: text/plain");

require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
$conf->conf_replace("pass", sha1($output["password"]), "../../conf/user/" . sha1($output["username"]) . "/user.conf");

print json_encode(array("data" => "ok"));
?>