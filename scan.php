<?php
header("Content-type: text/plain");
// Scan samba shares directories
require_once("common/include/funcs/parse_conf_file.php");

// Start execution time statistics
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

// Get current dir
$current_dir = str_replace("scan.php", "", $_SERVER["SCRIPT_FILENAME"]);

// Check for Ninuxoo conf file
if (!file_exists("config.ini")) {
	// Search for samba conf file
	$smb_conf_file = trim(shell_exec('find / -maxdepth 3 -name "smb.conf"')) . "smb.conf";
	// Write Ninuxoo conf file
	$conf_file = fopen("config.ini", "w+");
	// Content of file
	$conf_content = "; Ninuxoo Configuration file\n";
	$conf_content .= ";;NOTE: Remember that comment hash is \"\;\" not \"#\" \;)\n\n";
	$conf_content .= "[NAS]\n";
	$conf_content .= "smb_conf_dir = \"" . trim(str_replace("smb.conf", "", $smb_conf_file)) . " ;Absolute directory where smb.conf file is placed\"\n";
} else {
	// Get data from Ninuxoo conf file
	$conf_file = parse_ini_file("config.ini", true);
	$smb_conf_file = trim(preg_replace("/;(.*?)$/i", "", $conf_file["NAS"]["smb_conf_dir"])) . "smb.conf";
	//unlink("config.ini");
}
// Moves to root
chdir("/");
// Read smb.conf file
$smb_conf = parse_conf_file($smb_conf_file);
// Match Samba shares
foreach($smb_conf as $conf => $conf_arr){
	foreach($conf_arr as $k => $v){
		if($k == "path" && !strstr($v, "%")){
			$paths[] = $v;
		}
	}
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
// Scan dirs
print str_repeat("-", 100) . "\n";
print "# Local NinuXoo scanning \n";
print "# " . date("Y-m-d H:i:s") . "\n";
print str_repeat("-", 100) . "\n";
print "Start scanning...\n";
foreach($paths as $scan_dir){
	$share_ = array();
	if(strpos($scan_dir, "%") === false){
		$main_dir_arr = explode("/", trim(str_replace("./", "", $scan_dir)));
		foreach($main_dir_arr as $kmdir => $mdir){
			if(!strstr($mdir, "%")){
				$share_[] = $mdir;
				if($kmdir < count($main_dir_arr)){
					$main_dir .= $mdir . "/";
				}
			}
		}
		$share_dir[] = implode("/", $share_);
		$curr_dir[] = trim(str_replace(array("./", $main_dir), "", substr(strrchr($scan_dir, "/"), 1)));
		
		//Move to directory to scan
		@chdir($scan_dir);
		$listing .= trim(str_replace("./", trim(str_replace(array("./", $main_dir), "", $scan_dir)) . "/", shell_exec("find | sort")));
	}
}
print "\n" . count($share_dir) . " directories in samba shares:\n";
print " -> " . implode("\n -> ", $curr_dir) . "\n";
$scanned = explode("\n", $listing);
foreach($scanned as $kl => $list){
	if(strlen($list) == 1){
		unset($scanned[$kl]);
	}
}
// Return to current dir
chdir($current_dir);
	if (strlen($conf_content) > 0) {
		// Write parameters on Ninuxoo conf file
		$conf_content .= "listing_file_dir = \"" . getcwd() . "/\" ;Absolute directory where listing file will be placed\n\n";

		$conf_content .= ";Public shared directories array\n";
		foreach($share_dir as $n => $dir){
			$conf_content .= "smb_shares[] = \"" . $dir . "\"\n";
		}
		$conf_content .= "\n";
		$conf_content .= ";Auto updated data (do not edit)\n";
		$conf_content .= "last_scan_date = \"" . date("Y-m-d") . "\"\n";
		$conf_content .= "last_items_count = " . count($scanned) . "\n";
	}
// Create listing file
if(!is_dir("~listing_history")){
	mkdir("~listing_history", 0777);
	chmod("~listing_history", 0777);
}
// Archive history
if (copy("listing", "~listing_history/listing_" . date("Y-m-d_H:i:s"))) {
	@unlink("listing");
	if ($handle = opendir("~listing_history")) {
		while (false !== ($filename = readdir($handle))){ 
			//  Delete older than 1 week
			if ($filename != '.' &&  $filename != '..'  && eregi("listing", $filename) &&  filemtime("~listing_history/" . $filename) < strtotime("-1 week")){
				@unlink("~listing_history/" . $filename) ;
			}
		}
		closedir($handle); 
	}
} else {
	print "no";
}
$listing_file = fopen("listing", "w+");
fwrite($listing_file, implode("\n", $scanned));
fclose($listing_file);
chmod("listing", 0777);

// End execution time statistics
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$endtime = $mtime;
$totaltime = round($endtime - $starttime, 5);
	if (strlen($conf_content) > 0) {
		// Write parameter on Ninuxoo conf file
		$conf_content .= "last_scanning_time = " . $totaltime . "\n\n";

		$conf_content .= "name = \"" . $smb_conf["global"]["netbios name"] . "\" ;NAS name\n";
		$conf_content .= "description = \"" . $smb_conf["global"]["comment"] . "\" ;NAS description\n";
		$conf_content .= "ipv4 = \"" . $_SERVER["SERVER_ADDR"] . "\" ;NAS IPv4 address\n";
		$conf_content .= "ipv6 = \"" . shell_exec("ip addr show dev eth0 | sed -e's/^.*inet6 \([^ ]*\)\/.*$/\1/;t;d'") . "\" ;NAS IPv6 address\n";
		if($_SERVER["HTTPS"]){
			$http = "https://";
		} else {
			$http = "http://";
		}
		$conf_content .= "http_root = \"" . $http . $_SERVER["SERVER_NAME"] . "/\" ;NAS web uri\n";
		// Write content in file
		fwrite($conf_file, $conf_content);
		fclose($conf_file);
		chmod("config.ini", 0777);
	}
// Display output
print "\n" . count($scanned) . " files listed in " . getcwd() . "/listing in " . $totaltime . " seconds\n" . str_repeat("-", 100) . "\n";
?>