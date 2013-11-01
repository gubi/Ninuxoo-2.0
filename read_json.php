<?php
header("Content-type: text/plain");

foreach($_GET as $k => $v){
	$query[] = $k . "=" . $v;
}
if(isset($_GET["uri"]) && trim($_GET["uri"]) !== ""){
	$uri = str_replace(" ", "%20", urldecode($_GET["uri"]));
	
	if(@file_get_contents($uri)){
		print @file_get_contents($uri);
	} else {
		print "no file";
	}
} else {
	$uri = "http://ninuxoo.ninux.org/cgi-bin/json.cgi?";
	print @file_get_contents($uri . implode("&", $query));
}
?>
