<?php
header("Content-type: text/plain");
include("../browser.php");

foreach($_GET as $k => $v){
	$query[] = $k . "=" . $v;
}
if(isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	$uri = str_replace(" ", "%20", urldecode($_GET["uri"]));
	
	$contents = shell_exec("curl " . $uri);
	//$contents = browse($uri);
	if(strlen($contents) > 0){
		print "jqueryCallback(" . $contents . ")";
	} else {
		print 'jqueryCallback({"error": "no file"})';
	}
} else {
	$uri = "http://ninuxoo.ninux.org/cgi-bin/json.cgi?";
	print "jqueryCallback(" . @file_get_contents($uri . implode("&", $query)) . ")";
}
?>
