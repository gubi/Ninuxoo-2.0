<?php
header("Content-type: text/plain");
require_once("../../classes/local_search.class.php");

$output["path"] = (trim($output["path"]) == "-") ? "" : trim($output["path"]);
$output["filetype"] = (trim($output["filetype"]) == "-") ? "" : trim($output["filetype"]);
$local_search = new local_search();
$local_search->set_params(array(
		"op" => str_replace(" ", "\ ", escapeshellcmd(urldecode(trim($output["op"])))),
		"path" => str_replace(" ", "\ ", escapeshellcmd(urldecode($output["path"]))),
		"q" => str_replace(" ", "\ ", urldecode(trim($output["q"]))),
		"nresults" => trim($output["nresults"]),
		"filetype" => $output["filetype"]
));
print $local_search->get();
?>