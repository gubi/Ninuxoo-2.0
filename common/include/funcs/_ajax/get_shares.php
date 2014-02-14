<?php
header("Content-type: text/plain");

$output["file"] = rawurldecode($output["file"]);
if(strlen($output["file"]) == 0) {
	$config = parse_ini_file("../../conf/config.ini", true);
	$output["file"] = $config["NAS"]["root_share_dir"];
}
if(isset($output["file"]) && trim($output["file"]) !== ""){
	$file_exists = true;
}
if(!$file_exists) {
	print json_encode(array("alert" => 'La directory "' . $output["file"] . '" non sembra esistere...'));
} else {
	$ls = shell_exec("ls -d1 " .  str_replace("//", "/", $output["file"] . "/*/"));
	$dirs = explode("\n", $ls);
	if(count($dirs) > 1) {
		$parsed["error"] = false;
		foreach(array_filter($dirs) as $kf => $dir) {
			$info = pathinfo($dir);
			$parsed["shares"][] = $info["basename"];
		}
		print json_encode($parsed);
	} else {
		print json_encode(array("alert" => 'La directory "' . $output["file"] . '" non sembra esistere...'));
	}
}
?>