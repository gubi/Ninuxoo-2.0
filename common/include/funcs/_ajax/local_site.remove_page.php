<?php
header("Content-type: text/plain");

$path = str_replace("common/", "../../../", $output["script_dir"]);
if(strlen(trim($output["original_name"])) > 0 && $output["page_name"] !== $output["original_name"]) {
	rename(str_replace("//", "/", $path . "/" . $output["original_name"] . ".md"), str_replace("//", "/", $path . "/" . $output["page_name"] . ".md"));
}
if(unlink(str_replace("//", "/", $path . "/" . $output["page_name"] . ".md"))) {
	$data = "ok";
} else {
	$data = "error::Non si gode dei permessi sufficienti per cancellare il file.";
}

print json_encode(array("data" => $data));
?>