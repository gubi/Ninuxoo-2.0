<?php
header("Content-type: text/plain");

$seen_files_path = "../../conf/user/" . sha1($output["user"]) . "/seen_files.list";
if(file_exists($seen_files_path)) {
	$seen_file = parse_ini_file($seen_files_path, true);
	if(array_key_exists(sha1($output["url"]), $seen_file["Files"])) {
		$status = $seen_file["Files"][sha1($output["url"])];
	} else {
		$status = "unseen";
	}
} else {
	$status = "unseen";
}
print $status;
?>