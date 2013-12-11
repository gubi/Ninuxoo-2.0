<?php
header("Content-type: text/plain");
require_once("../../classes/mdns.class.php");

if($output["check"] == "true") {
	$mdns = new mdns();
	$ndata = $mdns->scan(true);
	$c = -1;
	if (is_array($ndata)) {
		foreach($ndata as $hostname => $ip) {
			$c++;
			switch($ip["status"]) {
				case "trusted":
					$img = '<img src="common/media/img/mainframe_accept_32_333.png" />';
					$status_t = "trusted selected";
					$status_u = "untrusted";
					break;
				case "untrusted":
					$img = '<img src="common/media/img/mainframe_cancel_32_333.png" />';
					$status_t = "trusted";
					$status_u = "untrusted selected";
					break;
				case "unknown":
				default:
					$img = '<img src="common/media/img/mainframe_run_32_333.png" />';
					$status_t = "trusted";
					$status_u = "untrusted";
					break;
			}
			sort($ip["reachability"]);
			$neighbor["finded"] = $c+1;
			$neighbor[$c]["img"] = $img;
			$neighbor[$c]["hostname"] = $hostname;
			$neighbor[$c]["token"] = $ip["token"];
			$neighbor[$c]["owner"]["email"] = $ip["owner"]["email"];
			$neighbor[$c]["owner"]["key"] = '0x' . $ip["owner"]["key"];
			$neighbor[$c]["geo_zone"] = trim($ip["geo_zone"], '"');
			$neighbor[$c]["reachability"] = implode(", ", $ip["reachability"]);
			$neighbor[$c]["status_t"] = $status_t;
			$neighbor[$c]["status_u"] = $status_u;
		}
	} else {
		$neighbor["finded"] = 0;
	}
}
print json_encode(array("data" => $neighbor));
?>

