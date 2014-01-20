<?php
header("Content-type: text/plain");

if(isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	require_once("../../lib/PEAR/Net/Ping.php");
	$ping = Net_Ping::factory();
	if (PEAR::isError($ping)) {
		echo $ping->getMessage();
	} else {
		$ping->setArgs(array('count' => 2));
		// Ping IP
		$ping_res = (array)$ping->ping($_GET["uri"]);
		// If packet loss is 0
		if($ping_res["_loss"] == 0){
			// Check local_search script
			$header = file_get_contents("http://" . $_GET["uri"] . "/local_search.php?check=true");
			if($header == "yes I am!"){
				print "valid";
			} else {
				print "invalid";
			}
		} else {
			print "invalid";
		}
	}
} else {
	print "null";
}
?>