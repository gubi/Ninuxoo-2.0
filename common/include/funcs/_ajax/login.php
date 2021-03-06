<?php
$setting = parse_ini_file("../../conf/general_settings.ini", true);
if(file_exists("../../conf/user/" . sha1($output["username"]))) {
	$user_conf = parse_ini_file("../../conf/user/" . sha1($output["username"]) . "/user.conf", true);
	if($output["username"] == $user_conf["User"]["email"] && sha1($output["password"]) == $user_conf["User"]["pass"]) {
		$config = parse_ini_file("../../conf/config.ini", true);
		
		require_once("../../funcs/_blowfish.php");
		if(trim($user_conf["User"]["name"]) == "" || trim($user_conf["User"]["key"]) == "") {
			require_once("Config/Lite.php");
			require_once("../../lib/simplehtmldom_1_5/simple_html_dom.php");
			
			$mit = "http://pgp.mit.edu:11371";
			
			$html = new simple_html_dom();
			$html->load_file($mit . "/pks/lookup?search=" . $output["username"]);
			$ret = $html->find("a");
			$data = array();
			foreach($ret as $a) {
				if(strpos($a->href, "lookup") !== false) {
					$data[] = $a->plaintext;
				}
			}
			
			$uconf = new Config_Lite();
			$uconf->read("../../conf/user/" . sha1($output["username"]) . "/user.conf");
			
			$config->set("User", "name", trim(preg_replace("/&lt;(.*?)&gt;/i", "", $data[1])));
			$config->set("User", "username", $output["username"]);
			$config->set("User", "key", $data[0]);
			$config->save();
		} else {
			require_once("../../classes/chat.class.php");
			$data = array();
			$data[0] = trim($user_conf["User"]["key"]);
			$data[1] = trim($user_conf["User"]["name"]);
		}
		foreach($data as $k => $v) {
			$user_data["error"] = false;
			
			$user_data["key"] = $data[0];
			$user_data["name"] = trim(preg_replace("/&lt;(.*?)&gt;/i", "", $data[1]));
			
			$name = PMA_blowfish_encrypt($user_data["name"] . "~" . $output["username"] . "~" . $user_data["key"], "ninuxoo_cookie");
			$setted_time = (int)$setting["login"]["session_length"];
			
			setcookie("n", $name, time() + $setted_time, "/");
			$chat = new chat();
				if(strlen($user_conf["Chat"]["nick"]) > 0) {
					$nick = $user_conf["Chat"]["nick"];
				} else {
					$name = explode(" ", $user_conf["User"]["name"]);
					$nick = trim($name[0]);
				}
			$chat->params($nick, $user_conf["User"]["email"], (strlen($user_conf["Chat"]["personal_message"]) > 0) ? $user_conf["Chat"]["personal_message"] : "Hello!");
			$chat->set_status($user_conf["Chat"]["chat_status"]);
		}
		print json_encode($user_data);
	} else {
		// Fare il check su altri NAS prima
		print json_encode(array("error" => true, "message" => "Username o password errati"));
	}
} else {
	// Fare il check su altri NAS prima
	print json_encode(array("error" => true, "message" => "Username o password errati"));
}
?>