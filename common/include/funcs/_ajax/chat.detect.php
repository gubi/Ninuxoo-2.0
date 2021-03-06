<?php
header("Content-type: text/plain;");
require_once("../get_gravatar.php");

$irc = shell_exec("avahi-browse _irc._tcp -prt");
preg_match_all("/\=\;(.*?)\n/is", $irc, $out);
foreach($out[1] as $kp => $parsed) {
	$o = explode(";", $parsed);
	$oo = explode(":", trim($o[8], '"'));
	if(md5($oo[0] . $oo[1]) !== $output["current"]) {
		$peoples[$o[2]]["name"] = $o[2];
		$peoples[$o[2]]["img"] = get_gravatar($oo[0], 48, "identicon", "x");
		$peoples[$o[2]]["id"] = md5($o[2] . $oo[1]);
		$peoples[$o[2]]["status"] = $oo[2];
		$peoples[$o[2]]["personal_message"] = stripslashes($oo[1]);
	}
}
if(is_array($peoples)) {
	ksort($peoples);
	$mdata["peoples"] = $peoples;
	$mdata["count"] = count($peoples);
	if($_GET["debug"] == "true") {
		print_r($mdata);
	}
	print json_encode($mdata);
} else {
	$mdata["count"] = 0;
	print json_encode($mdata);
}
?>