<?php
header("Content-type: text/plain;");

if(isset($_GET["token"]) && trim($_GET["token"]) !== "") {
	foreach(glob($trusted_dir . "*.pem") as $filename) {
		$tokens[] = str_replace(array($trusted_dir, ".pem"), "", $filename);
	}
	if(in_array($_GET["token"], $tokens)) {
		if(isset($_GET["search"]) && trim($_GET["search"]) !== "") {
			$search = shell_exec('grep -i "' . urldecode($_GET["search"]) . '" listing');
			$results = array_filter(explode("\n", $search));
			
			if(count($results) > 0) {
				print_r($results);
			} else {
				"No results";
			}
		} else {
			print "Nothing to do.";
		}
	}
} else {
	require_once("../common/include/classes/link_nas.class.php");
	$link_nas = new link_nas();
	
	if(isset($_GET["action"]) && trim($_GET["action"]) !== "") {
		switch($_GET["action"]) {
			case "make_friend":
				if(isset($_GET["ip"]) && trim($_GET["ip"]) !== "") {
					print $link_nas->start_request($_GET["ip"]);
				}
				break;
			case "friend_request":
				if(isset($_GET["request"]) && trim($_GET["request"]) !== "") {
					print $link_nas->first_response($_GET["request"]);
				}
				break;
			case "confirm_friend":
				if(isset($_GET["request"]) && trim($_GET["request"]) !== "") {
					print $link_nas->second_response($_GET["request"]);
				}
				break;
			case "reconfirm_friend":
				if(isset($_GET["request"]) && trim($_GET["request"]) !== "") {
					print $link_nas->third_response($_GET["request"]);
				}
				break;
			/*
			default:
				require_once("../common/include/classes/logging.class.php");
				
				$log = new Logging();
				$log->file("../common/include/log/ninuxoo.log");
				$log->write("notice", "Rejected request from " . $_SERVER["REMOTE_ADDR"] . " without token");
				$log->close();
				print "You must to provide token!";
				break;
			*/
		}
	}
}
?>