<?php
header("Content-type: text/plain");
require_once("../../classes/bg_process.class.php");

$user_config = @parse_ini_file("../../conf/user/" . sha1($output["user_name"]) . "/user.conf", true);
if($user_config["Chat"]["show_ip"] == "false") {
	$noip = "noip:";
} else {
	$noip = "";
}
$kill = shell_exec('ps aux | grep -i "' . $output["host"] . '" | awk {\'print $2\'} | xargs kill -9');
$cmd = 'avahi-publish -s "' . $output["host"] . '" _dns-sd._tcp 64689 "' . str_replace(";", "{~}", str_replace("noip:noip:", "noip:", $noip . base64_decode($output["message"]))) . '"';
$process = new bg_process($cmd);
$process->run();
print $process->getPid();
?>