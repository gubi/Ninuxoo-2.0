<?php
header("Content-type: text/plain");
require_once("../../lib/mime_types.php");
require_once("../personal_configs.explode_tree.php");

if(trim($_GET["debug"]) == "true") {
	$output = $_GET;
}
$dir_name = "../../conf/user/" . sha1($output["username"]) . "/configs";
chdir($dir_name);
if(exec('find ./ -mindepth 1 -maxdepth 2 \( ! -iname ".*" \) | sort', $scan)){
	foreach($scan as $file) {
		if(is_dir($file)) {
			$dirs[] = $file;
		} else {
			$files[] = $file;
		}
	}
	natcasesort($dirs);
	natcasesort($files);
	$all = array_merge($dirs, $files);
	$key_files = array_combine(array_values($all), array_values($all));
	$tree = explodeTree($key_files, "/");
	plotTree($tree, 0, true, $dir_name, $mime_type);
}
?>