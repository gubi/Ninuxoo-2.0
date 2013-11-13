<?php
header("Content-type: text/plain");
require_once("json_service.php");

$contents = shell_exec("curl http://map.ninux.org/nodes.json");
if(strlen($contents) > 0){
	$nodes = json_decode($contents, 1);
	if(isset($_GET["debug"]) && trim($_GET["debug"]) == "true") {
		print_r($nodes);
	}
	foreach($nodes as $node_type => $node_list) {
		if($node_type !== "links" && $node_type !== "potential") {
			foreach($node_list as $node_id => $node) {
				$json_output[$node["slug"]] = array("name" => $node["name"], "type" => $node_type, "lat" => $node["lat"], "lng" => $node["lng"]);
			}
		}
	}
	ksort($json_output);
	print json_encode($json_output);
} else {
	print 'jqueryCallback({"error": "no file"})';
}
?>