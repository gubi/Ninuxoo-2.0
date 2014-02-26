<?php
header("Content-type: text/plain");
function clean($path_str) {
	return escapeshellcmd($path_str);
}
$root_path = "../../conf/user/" . sha1($output["user_username"]) . "/configs";
$output["script_dir"] = str_replace("common/include/", "../../", $output["script_dir"]);

if($output["save_script_dir"] == "root") {
	$move_path = $root_path;
} else {
	$move_path = $root_path . "/" . $output["save_script_dir"];
}
if(trim($output["config_name"]) == "") {
	$output["config_name"] = $output["original_name"];
}
if($fs = fopen(str_replace("//", "/", $output["script_dir"] . "/" . $output["original_name"]), "w")) {
	fwrite($fs, $output["script"]);
	fclose($fs);
	if($output["save_script_dir"] !== str_replace($root_path . "/", "", $output["script_dir"]) || trim($output["original_name"]) !== trim($output["config_name"])) {
		rename(str_replace("//", "/", $output["script_dir"] . "/" . $output["original_name"]), str_replace("//", "/", $move_path . "/" . trim($output["config_name"])));
	}
	$data = "ok";
} else {
	$data = "error::Non si gode dei permessi sufficienti per salvare il file.";
}

print json_encode(array("data" => $data));
?>