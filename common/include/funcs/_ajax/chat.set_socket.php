<?php
header("Content-type: text/plain;");
require_once("../../classes/chat.class.php");

if($_GET["debug"] == "true") {
	$output = $_GET;
	print $output["id"] . "\n";
}
$chat = new chat();
$irc = shell_exec("avahi-browse _irc._tcp -prt");
preg_match_all("/\=\;(.*?)\n/is", $irc, $out);

if(file_exists("../../conf/user/" . sha1($output["username"]))) {
	if($uc = fopen("../../conf/user/" . sha1($output["username"]) . "/history/chat/" . $output["id"], "w")) {
		fwrite($uc, "");
		fclose($uc);
		
		print "ok";
	} else {
		print "na";
	}
} else {
	print "user config do not exists";
}
?>
