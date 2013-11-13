<?php
header("Content-type: text/plain");
require_once("json_service.php");
require_once(str_replace("//", "/", dirname(__FILE__) . "/") . "../../classes/manage_conf_file.class.php");


$file_exists = false;
if(isset($_GET["file"]) && trim($_GET["file"]) !== ""){
	$config["file"][] = str_replace("/smb.conf", "", ((substr($_GET["file"], -1) == "/") ? substr($_GET["file"], 0, -1) : $_GET["file"])) . "/smb.conf";
	$file_exists = file_exists($config["file"][0]);
}
if(!$file_exists) {
	$cfile = trim(shell_exec('find / -type f -name "smb.conf" -print 2>/dev/null'));
	$config["file"] = explode("\n", $cfile);
}

if(count($config["file"]) > 0) {
	foreach($config["file"] as $file) {
		$conf = new manage_conf_file();
		$samba_conf = $conf->parse($file, false);
		
		$parsed["file"]["valid_smb_conf"] = false;
		foreach($samba_conf as $section => $data) {
			if(strstr($section, "printer") === false) {
				foreach($data as $k => $v) {
					if($k == "path") {
						if(strpos($v, "%") === false && strpos($v, "printer") === false) {
							$parsed["file"]["valid_smb_conf"] = true;
							$parsed["file"]["file"] = $file;
							$parsed["file"]["path"] = str_replace("smb.conf", "", $file);
							
							$vinfo = pathinfo($v);
							$parsed["file"]["smb_shares"][] = "/" . $vinfo["basename"];
							
							$parsed["file"]["smb.conf"][$section] = $data;
						}
					}
				}
			}
		}
	}
	if (isset($_GET["debug"])) {
		print_r($parsed);
	}
	print json_encode($parsed);
} else {
	print "error:no smb.conf finded in your device. Configure a samba share first.";
}
?>