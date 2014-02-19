<?php
header("Content-type: text/plain");

$root_path = "../../conf/user/" . sha1($output["user_username"]) . "/configs";
$output["script_dir"] = str_replace("common/include/", "../../", $output["script_dir"]);

if($output["save_script_dir"] == "root" && $output["save_script_dir"] == $root_path) {
	$move_path = $output["v"];
} else {
	$move_path = $root_path . "/" . str_replace("root", "", $output["save_script_dir"]);
}

if(strlen(trim($output["original_name"])) > 0 && $output["config_name"] !== $output["original_name"]) {
	rename(str_replace("//", "/", $output["script_dir"] . "/" . $output["original_name"]), str_replace("//", "/", $move_path . "/" . $output["config_name"]));
}
if($fs = fopen(str_replace("//", "/", $root_path . "/" . $output["config_name"]), "w")) {
	fwrite($fs, $output["script"] . PHP_EOL);
	fclose($fs);
	if($root_path != $move_path) {
		rename(str_replace("//", "/", $output["script_dir"] . "/" . $output["config_name"]), str_replace("//", "/", $move_path . "/" . $output["config_name"]));
	}
	$data = "ok";
} else {
	$data = "error::Non si gode dei permessi sufficienti per salvare il file.";
}

print json_encode(array("data" => $data));
?>