<?php
header("Content-type: text/plain");

$path = str_replace("common/", "../../../", $output["script_dir"]);
if(unlink(str_replace("//", "/", $path . "/" . trim(rawurlencode($output["page_name"]))) . ".md")) {
	$data = "ok";
} else {
	$data = "error::Non si gode dei permessi sufficienti per cancellare il file.";
}

print json_encode(array("data" => $data));
?>