<?php
require_once("../../lib/mime_types.php");

$info = pathinfo($output["config_name"]);
$filename = $info["basename"];

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private", false);
header("Content-Disposition: attachment; filename=\"" . $filename . "\"");
header("Content-Type: " . $mime_type[$info["extension"]]);
header("Content-Transfer-Encoding: binary");
header("Connection: close");
print stripslashes($output["script"]);
?>