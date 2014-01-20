<?php
header("Content-type: text/plain");
include("../browser.php");

/*
foreach($_GET as $k => $v){
	$query[] = $k . "=" . $v;
}
*/
if(isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	$uri = str_replace(" ", "%20", urldecode($_GET["uri"]));
	if(strpos($uri, "?") !== false) {
		$info = explode("?", $uri);
		
		$curl_cmd = 'curl -L -G -d "' . $info[1] . '" ' . $info[0];
	} else {
		$curl_cmd = 'curl ' . $uri;
	}
	if(isset($_GET["debug"])) {
		print $uri . "\n\n";
		print $curl_cmd . "\n\n";
	}
	$contents = shell_exec($curl_cmd);
	if(strlen($contents) == 0){
		$contents = browse($uri);
	}
	if(strlen($contents) > 0){
		print $contents;
	} else {
		print '{"error": "no file"}';
	}
} else {
	$uri = "http://ninuxoo.ninux.org/cgi-bin/json.cgi?";
	print @file_get_contents($uri . implode("&", $query));
}
?>
