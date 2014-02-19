<?php
header("Content-type: text/plain");

$dir_name = "../../conf/user/" . sha1($output["username"]) . "/configs";
if(is_dir($dir_name . "/" . str_replace("./", "", $output["dir_name"]))) {
	if(@rename($dir_name . "/" . str_replace("./", "", $output["dir_name"]), $dir_name . "/" . $output["new_name"])) {
		$resp["error"] = false;
		$resp["error_msg"] = "";
	} else {
		$resp["error"] = true;
		$resp["error_msg"] = "non &egrave; stato possibile rinominare la directory";
	}
} else {
	$resp["error"] = true;
	$resp["error_msg"] = "non &egrave; una directory";
}

print json_encode($resp);
?>