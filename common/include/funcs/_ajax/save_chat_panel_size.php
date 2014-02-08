<?php
header("Content-type: text/plain");
require_once("../../classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
$conf->conf_replace("panel_width", str_replace("px", "", trim($output["size"])), "../../conf/user/" . sha1($output["user_username"]) . "/user.conf");

print json_encode(array("data" => "ok"));
?>
