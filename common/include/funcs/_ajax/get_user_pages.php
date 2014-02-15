<?php
header("Content-type: text/plain");
require_once("../get_gravatar.php");

if(isset($_GET["user"])) {
	$output["user"] = $_GET["user"];
}
if(!isset($output["user"]) || trim($output["user"]) == "") {
	foreach(glob("../../conf/user/*/user.conf") as $filename) {
		$user_conf = parse_ini_file($filename, true);
		$user_id = sha1($user_conf["User"]["email"]);
		$users[$user_id]["id"] = $user_id;
		$users[$user_id]["nick"] = $user_conf["Chat"]["nick"];
		$users[$user_id]["personal_message"] = $user_conf["Chat"]["personal_message"];
		$users[$user_id]["gravatar"] = get_gravatar($user_conf["User"]["email"], 32, "identicon", "x");
		$users[$user_id]["name"] = $user_conf["User"]["name"];
		$users[$user_id]["key"] = $user_conf["User"]["key"];
		$users[$user_id]["email"] = $user_conf["User"]["email"];
	}
	$resp["count"] = count($users);
	$resp["data"] = $output;
	$resp["users"] = $users;
} else {
	foreach(glob("../../../md/pages/" . addslashes(trim($output["user"])) . "/*.md") as $filename) {
		$info = pathinfo($filename);
		$files[$info["filename"]]["id"] = $info["filename"];
		$files[$info["filename"]]["name"] = base64_decode(rawurldecode($info["filename"]));
	}
	$resp["count"] = count($files);
	$resp["pages"] = $files;
}
if(isset($_GET["debug"]) == "true") {
	print_r($resp);
} else {
	print json_encode($resp);
}
?>