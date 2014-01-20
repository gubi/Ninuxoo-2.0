<?php
header("Content-type: text/plain;");
require_once("../../lib/markdown.php");

$magic_words = array("noip");

$avh = shell_exec("avahi-browse _dns-sd._tcp -prt");
preg_match_all("/\=\;(.*?)\n/is", $avh, $out);
foreach($out[1] as $kp => $parsed) {
	$o = explode(";", $parsed);
	$message_raw = htmlentities(utf8_decode(trim(str_replace("{~}", ";", $o[8]), '"')));
	if(strpos($message_raw, ":") !== false) {
		$ex = explode(":", $message_raw);
		if(in_array(strtolower($ex[0]), $magic_words)) {
			$message_raw = str_replace($ex[0] . ":", "", $message_raw);
		}
		if(strtolower($ex[0]) == "noip") {
			$group[$o[2]]["ip_hidden"] = true;
			$group[$o[2]]["ip"][] = $o[1] . ": " . preg_replace('/(\d|\w)/s', "*", $o[6]);
		} else {
			$group[$o[2]]["ip_hidden"] = false;
			$group[$o[2]]["ip"][] = $o[1] . ": " . $o[6];
		}
	} else {
		$group[$o[2]]["ip_hidden"] = false;
		$group[$o[2]]["ip"][] = $o[1] . ": " . $o[6];
	}
	// ID: The sum md5 of Hostname and message, in numeric 4 digits
	$group[$o[2]]["id"] = substr(abs(crc32(md5($o[5] . $message_raw))), 0, 4);
	$group[$o[2]]["message"] = str_replace(array("<p>", "</p>", '<a href="http://'), array("", "", '<a target="_blank" href="http://'), Markdown($message_raw));
	$group[$o[2]]["message_raw"] = $message_raw;
	$group[$o[2]]["own"] = ($o[6] == $_SERVER["SERVER_ADDR"]) ? true : false;
}
if(is_array($group)) {
	$pid = shell_exec('ps -au www-data | grep "avahi-publish" | awk {\'print $1\'}');
	
	foreach($group as $sender => $data) {
		$is_own[] = $data["own"];
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