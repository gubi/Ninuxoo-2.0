<?php
header("Content-type: text/plain");
// Scan samba shares directories
require_once("common/include/funcs/parse_conf_file.php");
require_once("common/include/lib/JSON.php");
if(!function_exists("json_encode")) {
	function json_encode($data) {
		$json = new Services_JSON();
		return $json->encode($data);
	}
}
if(!function_exists("json_decode")) {
	function json_decode($data) {
		$json = new Services_JSON();
		return $json->decode($data);
	}
}
function change_conf($Setting, $replace, $INI_PATH) {
	require_once("common/include/lib/PEAR/File/SearchReplace.php");
	
	$files_to_search = array($INI_PATH) ;
	$search_string  = "/" . $Setting . ".*/";
	if(!is_numeric($replace)){
		$replace = "\"" . $replace . "\"";
	}
	$replace_string = $Setting . " = " . $replace . "";
	 
	$snr = new File_SearchReplace($search_string,
				      $replace_string,
				      $files_to_search,
				      '', // directorie(s) to search
				      false) ;
	
	$snr->setSearchFunction("preg");
	$snr->doSearch();
}

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
// Check for osd.xml file
if (!file_exists("osd.xml")) {
	require_once("common/include/funcs/generate_osd.php");
}
// Moves to root
chdir("/");
// Read smb.conf file
$smb_conf = parse_conf_file($smb_conf_file);

// Match Samba shares
foreach($smb_conf as $conf => $conf_arr){
	foreach($conf_arr as $k => $v){
		if($k == "path" && !strstr($v, "%")){
			$paths[] = str_replace($conf_file["NAS"]["replace_remote_dir"], $conf_file["NAS"]["replace_remote_dir_with"], $v);
		}
	}
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
// Scan dirs
print str_repeat("-", 100) . "\n";
print "# NinuXoo Local scanning \n";
print "# " . date("Y-m-d H:i:s") . "\n";
print str_repeat("-", 100) . "\n";
print "Start scanning...\n";
print "This may take a bit of time. Take a coffee break... ;)\n";
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
		$cdir = trim(str_replace(array("./", $main_dir), "", substr(strrchr($scan_dir, "/"), 1)));
		$curr_dir[] = $cdir;
		$listing_list[$cdir] = implode("/", $share_);
		
		$ll = shell_exec("find " . str_replace(" ", "\ ", escapeshellcmd($scan_dir)) . " -mindepth 1");
		if(strlen($ll) > 3){
			$listing .= str_replace($conf_file["NAS"]["smb_conf_dir"], "", trim(str_replace("./", trim(str_replace(array("./", $main_dir), "", $scan_dir)) . "/", $ll))) . "\n";
			$c_c = pathinfo(str_replace($conf_file["NAS"]["smb_conf_dir"], "", trim(str_replace(array("./", ".\n", ".\r\n"), array(trim(str_replace(array("./", $main_dir), "", $scan_dir)) . "/", "{~}\n", "{~}\n"), $ll))));
			
			$listing_arr[$cdir] = str_replace($conf_file["NAS"]["smb_conf_dir"], "", trim(str_replace(array("./", ".\n", ".\r\n"), array(trim(str_replace(array("./", $main_dir), "", $scan_dir)) . "/", "{~}\n", "{~}\n"), $ll)));
		}
	}
}
print "\n" . count($share_dir) . " directories in samba shares:\n";
print " -> " . implode("\n -> ", $curr_dir) . "\n";
// listing.list file
foreach($listing_list as $type => $absolute_path){
	foreach(explode("\n", $listing_arr[$type]) as $kk => $vv){
		$scanned[] = $vv;
		$pth = pathinfo(str_replace("{~}", $listing_list[$type], $vv));
		if(!$pth["extension"]){
			$listing_list_file_arr[$type]["dirs"][$pth["dirname"]] = $pth["dirname"];
			$json[$pth["dirname"]]["dir"] = $pth["dirname"];
		} else {
			$listing_list_file_arr[$type]["files"][$pth["dirname"] . "/" . $pth["basename"]] = $pth["dirname"] . "/" . $pth["basename"];
			$json[$pth["dirname"]]["file"][] = $pth["basename"];
		}
	}
}
// Return to current dir
chdir($current_dir);
if (strlen($conf_content) > 0) {
	// Write parameters on Ninuxoo conf file
	$conf_content .= "listing_file_dir = \"" . getcwd() . "/API/\" ;Absolute directory where listing file will be placed\n\n";

	$conf_content .= ";Public shared directories array\n";
	foreach($share_dir as $n => $dir){
		$conf_content .= "smb_shares[] = \"" . $dir . "\"\n";
	}
	$conf_content .= "\n";
	$conf_content .= ";Auto updated data (do not edit)\n";
	$conf_content .= "last_scan_date = \"" . date("Y-m-d") . "\"\n";
	$conf_content .= "last_items_count = " . count($scanned) . "\n";
} else {
	chdir($current_dir);
	change_conf("last_scan_date", date("Y-m-d"), "config.ini");
	change_conf("last_items_count", count($scanned), "config.ini");
}

// Create listing file
if(!file_exists("API")){
	print "\nThe 'API/' folder does not exists. Okay, I've created it.\n";
	if(mkdir("API")){
		chmod("API", 0777);
	}
}
if(!file_exists("API/~listing_history")){
	print "\nThe 'API/~listing_history/' folder does not exists. Okay, I've created it.\n";
	mkdir("API/~listing_history", 0777);
	chmod("API/~listing_history", 0777);
}

// Archive history
print "\nArchiving previous listing files...";
if (@copy("API/listing", "API/~listing_history/listing_" . date("Y-m-d_H:i:s"))) {
	@copy("API/listing.list", "API/~listing_history/listing_" . date("Y-m-d_H:i:s") . ".list");
	@copy("API/listing.json", "API/~listing_history/listing_" . date("Y-m-d_H:i:s") . ".json");
	if ($handle = opendir("API/~listing_history")) {
		while (false !== ($filename = readdir($handle))){ 
			//  Delete older than 1 week
			if ($filename != '.' &&  $filename != '..'  && eregi("listing", $filename) &&  filemtime("API/~listing_history/" . $filename) < strtotime("-1 week")){
				@unlink("API/~listing_history/" . $filename) ;
			}
		}
		closedir($handle); 
	}
}
print " done.\n";
// Simple listing file
$simple_listing_file = fopen("API/listing", "w+");
fwrite($simple_listing_file, implode("\n", $scanned));
fclose($simple_listing_file);
@chmod("API/listing", 0777);

foreach ($listing_list_file_arr as $type => $list){
	$listing_list_file .= "[" . $type . "]\n";
	$listing_list_file .= "dirs[] = \"" . implode("\"\ndirs[] = \"", $list["dirs"]);
	$listing_list_file .= implode("\"\nfiles[] = \"", $list["files"]) . "\"";
	$listing_list_file .= "\n";
}
$listing_file = fopen("API/listing.list", "w+");
fwrite($listing_file, $listing_list_file);
fclose($listing_file);
@chmod("API/listing.list", 0777);

// Json listing file
$json_listing_file = fopen("API/listing.json", "w+");
fwrite($json_listing_file, json_encode($json));
fclose($json_listing_file);
@chmod("API/listing.json", 0777);

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
		$conf_content .= "http_root = \"" . $http . $_SERVER["SERVER_NAME"] . "\" ;NAS web uri\n";
		// Write content in file
		fwrite($conf_file, $conf_content);
		fclose($conf_file);
		chmod("config.ini", 0777);
	} else {
		chdir($current_dir);
		change_conf("last_scanning_time", $totaltime, "config.ini");
	}
// Display output
print "\n" . count($scanned) . " files listed in:\n   - " . getcwd() . "/API/listing\n   - " . getcwd() . "/API/listing.list\n   - " . getcwd() . "/API/listing.json\n\nElapsed time: " . $totaltime . " seconds\n" . str_repeat("-", 100) . "\nGoodbye.\n\n";
?>