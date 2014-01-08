<?php
header("Content-type: text/plain");
require_once("../../classes/local_search.class.php");

$conf = parse_ini_file("../../conf/config.ini", true);
$local_search = new local_search();
$local_search->set_params(array(
		/*"config_ini_file" => "config.ini",*/
		"op" => str_replace(" ", "\ ", escapeshellcmd(urldecode($output["op"]))),
		"path" => str_replace(" ", "\ ", escapeshellcmd(urldecode($output["path"]))),
		"q" => str_replace(" ", "\ ", urldecode($output["q"])),
		"nresults" => str_replace(" ", "\ ", escapeshellcmd(urldecode($output["nresults"]))),
		"filetype" => str_replace(" ", "\ ", escapeshellcmd(urldecode((($output["filetype"] == "-") ? "" : $output["filetype"])))),
		"ip" => str_replace(" ", "\ ", escapeshellcmd(urldecode((($output["ip"] == "-") ? "" : $output["ip"])))),
		"uri" => urldecode($output["url"])
));
if($output["debug"] == "true"){
	print_r($local_search->get($output["op"], 1));
	print "\n\n";
	print str_replace(" ", "\ ", urldecode($output["path"]));
}
print $local_search->get($output["op"]);
?>