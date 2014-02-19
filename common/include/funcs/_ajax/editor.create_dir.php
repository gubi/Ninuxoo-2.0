<?php
header("Content-type: text/plain");

$path = str_replace("common/include/", "../../", $output["user_conf_dir"]);
if(strlen(trim($output["dir_name"])) > 0) {
	if(!file_exists(trim($path) . "/" . trim($output["dir_name"]))) {
		if(@mkdir(trim($path) . "/" . trim($output["dir_name"]))) {
			if(@chmod(trim($path) . "/" . trim($output["dir_name"]), 0777)) {
				$resp["error"] = false;
				$resp["error_msg"] = "";
				$resp["icon"] = "success";
			} else {
				$resp["error"] = true;
				$resp["error_msg"] = "non &egrave; stato possibile impostare i permessi della directory";
				$resp["icon"] = "warning";
			}
		} else {
			$resp["error"] = true;
			$resp["error_msg"] = "non &egrave; stato possibile creare la directory";
			$resp["icon"] = "error";
		}
	} else {
		$resp["error"] = true;
		$resp["error_msg"] = "la directory esiste gi&agrave;";
		$resp["icon"] = "error";
	}
} else {
	$resp["error"] = true;
	$resp["error_msg"] = "la directory deve avere un nome";
	$resp["icon"] = "error";
}

print json_encode($resp);
?>