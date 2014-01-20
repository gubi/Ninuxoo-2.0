<?php
header("Content-type: text/plain");

$path = str_replace("common/", "../../../", $output["script_dir"]);
if(strlen(trim(rawurlencode(base64_encode($output["original_name"])))) == 0) {
	$action = "new";
} else {
	$action = "edit";
	if(trim(rawurlencode(base64_encode($output["page_name"]))) !== trim(rawurlencode(base64_encode($output["original_name"])))) {
		rename(str_replace("//", "/", $path . "/" . trim(rawurlencode(base64_encode($output["original_name"])))) . ".md", str_replace("//", "/", $path . "/" . trim(rawurlencode(base64_encode($output["page_name"])))) . ".md");
	}
}
if($fs = fopen(str_replace("//", "/", $path . "/" . trim(rawurlencode(base64_encode($output["page_name"])))) . ".md", "w")) {
	fwrite($fs, $output["page_content"]);
	fclose($fs);
	$data = $action;
} else {
	$data = "error::Non si gode dei permessi sufficienti per salvare il file.";
}

print json_encode(array("data" => $data));
?>