<?php
header("Content-type: text/plain");
require_once("../../lib/markdown.php");

function compareByName($a, $b) {
	return strcmp($a[0], $b[1]);
}
$avh = shell_exec("avahi-browse _dns-sd._tcp -prt");
preg_match_all("/\=\;(.*?)\n/is", $avh, $out);
foreach($out[1] as $kp => $parsed) {
	$o = explode(";", $parsed);
	$group[$o[2]]["message"] = str_replace(array("<p>", "</p>", '<a href="http://'), array("", "", '<a target="_blank" href="http://'), Markdown(trim(str_replace("{~}", ";", $o[8]), '"')));
	$group[$o[2]]["message_raw"] = trim(str_replace("{~}", ";", $o[8]), '"');
	$group[$o[2]]["ip"][] = $o[6];
	$group[$o[2]]["own"] = ($o[6] == $_SERVER["SERVER_ADDR"]) ? true : false;
}
if(is_array($group)) {
	$pid = shell_exec('ps -au www-data | grep "avahi-publish" | awk {\'print $1\'}');
	
	foreach($group as $sender => $data) {
		$is_own[] = $data["own"];
		uksort($data["ip"], 'compareByName');
		$group[$sender]["ip"] = $data["ip"];
		$mdata["messages"]["pid"] = trim($pid);
		$mdata["messages"]["broadcast"] = $group;
	}
	$mdata["messages"]["count"] = (in_array("1", $is_own) && count($group) > 1) ? count($group) - 1 : count($group);
	if($_GET["debug"] == "true") {
		print_r($mdata);
	}
	print json_encode($mdata);
} else {
	$mdata["messages"]["count"] = 0;
	print json_encode($mdata);
}
?>