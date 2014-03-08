<?php
header("Content-type: text/plain");

$seen_files_path = "../../conf/user/" . sha1($output["user"]) . "/seen_files.list";
if(!file_exists($seen_files)) {
	$seen_file = "[Files]\n";
	$seen_file .= sha1($output["url"]) . ' = "' . $output["status"] . '"' . "\n";
	if($sf = fopen($seen_files_path, "w")) {
		fwrite($sf, $seen_file);
		fclose($sf);
		@chmod($seen_files_path, 0777);
		
		print $output["status"];
	} else {
		print "no";
	}
} else {
	require_once("Config/Lite.php");
	
	$seen_file = new Config_Lite();
	$seen_file->read("../../conf/user/" . sha1($output["user"]) . "/seen_files.list");
	$config->set("Files", sha1($output["url"]), $output["status"]);
	if($config->save()) {
		print $output["status"];
	} else {
		print "no";
	}
}
?>