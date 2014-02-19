<?php
header("Content-type: text/plain");

$dir_name = "../../conf/user/" . sha1($output["username"]) . "/configs";
if(is_dir($dir_name . "/" . str_replace("./", "", $output["dir_name"]))) {
	if(@rmdir($dir_name . "/" . str_replace("./", "", $output["dir_name"]))) {
		$resp["error"] = false;
		$resp["error_msg"] = "";
	} else {
		$resp["error"] = true;
		$resp["error_msg"] = "non &egrave; stato possibile rimuovere la directory";
	}
} else {
	if(@unlink($dir_name . "/" . str_replace("./", "", $output["dir_name"]))) {
		$resp["error"] = false;
		$resp["error_msg"] = "";
	} else {
		$resp["error"] = true;
		$resp["error_msg"] = "non &egrave; stato possibile rimuovere il file";
	}
}

print json_encode($resp);
?>