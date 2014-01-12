<?php
header("Content-type: text/plain");
require_once("json_service.php");
require_once(str_replace("//", "/", dirname(__FILE__) . "/") . "../../classes/manage_conf_file.class.php");


$file_exists = false;
if(isset($otput["file"]) && trim($otput["file"]) !== ""){
	$config["file"][$kf] = str_replace("/smb.conf", "", ((substr($otput["file"], -1) == "/") ? substr($otput["file"], 0, -1) : $otput["file"])) . "/smb.conf";
	$file_exists = file_exists($config["file"][0]);
}
if(!$file_exists) {
	$cfile = trim(shell_exec('find / -type f -name "smb.conf" -print 2>/dev/null'));
	$config["file"] = explode("\n", $cfile);
}
if(count($config["file"]) > 0) {
	$parsed["error"] = false;
	foreach($config["file"] as $kf => $file) {
		$conf = new manage_conf_file();
		$samba_conf = $conf->parse($file, false);
		
		$parsed["smb"][$kf]["file"] = $file;
		$parsed["smb"][$kf]["valid_smb_conf"] = false;
		foreach($samba_conf as $section => $data) {
			if(strstr($section, "printer") === false) {
				foreach($data as $k => $v) {
					if($k == "path") {
						if(strpos($v, "%") === false && strpos($v, "printer") === false) {
							$parsed["smb"][$kf]["valid_smb_conf"] = true;
							$parsed["smb"][$kf]["path"] = str_replace("smb.conf", "", $file);
							
							$vinfo = pathinfo($v);
							$parsed["smb"][$kf]["smb_shares"][] = "/" . $vinfo["basename"];
							
							$parsed["smb"][$kf]["smb.conf"][$section] = $data;
						}
					}
				}
			}
		}
	}
	print json_encode($parsed);
} else {
	print json_encode(array("error" => "nessun file smb.conf rilevato in questo percorso. &Egrave; necessario configurare prima una condivisione samba."));
}
?>