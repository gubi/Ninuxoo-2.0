<?php
header("Content-type: text/plain");

$path = str_replace("common/include/", "../../", $output["script_dir"]);
if(strlen(trim($output["original_name"])) > 0 && $output["config_name"] !== $output["original_name"]) {
	rename(str_replace("//", "/", $path . "/" . $output["original_name"]), str_replace("//", "/", $path . "/" . $output["config_name"]));
}
if($fs = fopen(str_replace("//", "/", $path . "/" . $output["config_name"]), "w")) {
	fwrite($fs, $output["script"] . PHP_EOL);
	fclose($fs);
	$data = "ok";
} else {
	$data = "error::Non si gode dei permessi sufficienti per salvare il file.";
}

print json_encode(array("data" => $data));
?>