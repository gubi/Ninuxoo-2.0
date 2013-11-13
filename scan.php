<?php
require("php_error.php");
if (function_exists('\php_error\reportErrors')) {
	\php_error\reportErrors();
}
header("Content-type: text/plain");
// Scan samba shares directories
set_include_path("/var/www/common/include/funcs/_ajax");
ini_set("include_path", "/var/www/common/include/funcs/_ajax");
require_once("json_service.php");
require_once("common/include/classes/manage_conf_file.class.php");

$conf = new manage_conf_file();
// Start execution time statistics
$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$starttime = $mtime;

// Get current dir
$current_dir = str_replace("scan.php", "", $_SERVER["SCRIPT_FILENAME"]);

// Check for Ninuxoo conf file
if (!file_exists("config.ini")) {
	print "Error: no `config.ini` file.\nPlease, run setup before.\n\nNothing to scan.\nExit";
	exit();
} else {
	// Get data from Ninuxoo conf file
	$conf_file = parse_ini_file("config.ini", true);
	$smb_conf_file = trim(preg_replace("/;(.*?)$/i", "", $conf_file["NAS"]["smb_conf_dir"])) . "smb.conf";
}
// Moves to root
chdir("/");
// Read smb.conf file
$smb_conf = $conf->parse($smb_conf_file);

// Match Samba shares
foreach($conf_file["NAS"]["smb_shares"] as $v) {
	$info = pathinfo($v);
	$paths[] = trim(shell_exec('find / -name "' . $info["basename"] . '" -type d -print 2>/dev/null -quit'));
}
// - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - 
// Scan dirs
if(!isset($_GET["ajax"])) {
	print str_repeat("-", 100) . "\n";
	print "# NinuXoo Local scanning \n";
	print "# " . date("Y-m-d H:i:s") . "\n";
	print str_repeat("-", 100) . "\n";
	print "Start scanning...\n";
	print "This may take a bit of time. Please, take a coffee break... ;)\n";
}
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
		$replace_dir = str_replace($cdir, "", implode("/", $share_));
		$curr_dir[] = $cdir;
		$listing_list[$cdir] = implode("/", $share_);
		
		$ll = shell_exec("find " . str_replace(" ", "\ ", escapeshellcmd($scan_dir)) . " -mindepth 1");
		if(strlen($ll) > 3){
			$listing .= "/" . str_replace($replace_dir, "", trim(str_replace("./", trim(str_replace(array("./", $main_dir), "", $scan_dir)) . "/", $ll))) . "\n";
			$listing_arr[$cdir] = str_replace($replace_dir, "", trim(str_replace(array("./", ".\n", ".\r\n"), array(trim(str_replace(array("./", $main_dir), "", $scan_dir)) . "/", "{~}\n", "{~}\n"), $ll)));
		}
	}
}
if(!isset($_GET["ajax"])) {
	print "\n" . count($share_dir) . " directories in samba shares:\n";
	print " -> " . implode("\n -> ", $curr_dir) . "\n";
}
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
	$conf->conf_replace("last_scan_date", date("Y-m-d"), "config.ini");
	$conf->conf_replace("last_items_count", count($scanned), "config.ini");
}

// Create listing file
if(!file_exists("API")){
	if(!isset($_GET["ajax"])) {
		print "\nThe 'API/' folder does not exists. Okay, I've created it.\n";
	}
	if(mkdir("API")){
		chmod("API", 0777);
	}
}
if(!file_exists("API/~listing_history")){
	if(!isset($_GET["ajax"])) {
		print "\nThe 'API/~listing_history/' folder does not exists. Okay, I've created it.\n";
	}
	mkdir("API/~listing_history", 0777);
	chmod("API/~listing_history", 0777);
}

function mostRecentModifiedFileTime($dirName, $doRecursive) {
	$d = dir($dirName);
	$lastModified = 0;
	while($entry = $d->read()) {
		if ($entry != "." && $entry != "..") {
			if (!is_dir($dirName."/".$entry)) {
				$currentModified = filemtime($dirName."/".$entry);
			} else if ($doRecursive && is_dir($dirName."/".$entry)) {
				$currentModified = mostRecentModifiedFileTime($dirName."/".$entry,true);
			}
			if ($currentModified > $lastModified){
				$lastModified = $currentModified;
			}
		}
	}
	$d->close();
	return $entry;
}
// Archive history
if(!isset($_GET["ajax"])) {
	print "\nArchiving previous listing files...";
}
if ($handle = opendir("API/~listing_history")) {
	while (false !== ($filename = readdir($handle))){ 
		//  Delete older files
		if ($filename != '.' &&  $filename != '..'  && preg_match("/listing/", $filename)){
			@unlink("API/~listing_history/" . $filename) ;
		}
	}
	closedir($handle); 
}
// Copy the last version of files
@copy("API/listing", "API/~listing_history/listing_" . date("Y-m-d_H:i:s"));
@copy("API/listing.list", "API/~listing_history/listing_" . date("Y-m-d_H:i:s") . ".list");
@copy("API/listing.json", "API/~listing_history/listing_" . date("Y-m-d_H:i:s") . ".json");

/*
SAVE LISTINGS
*/

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
		$conf->conf_replace("last_scanning_time", $totaltime, "config.ini");
	}
// Display output
if(!isset($_GET["ajax"])) {
	print "\n" . count($scanned) . " files listed in:\n   - " . getcwd() . "/API/listing\n   - " . getcwd() . "/API/listing.list\n   - " . getcwd() . "/API/listing.json\n\nElapsed time: " . $totaltime . " seconds\n" . str_repeat("-", 100) . "\nGoodbye.\n\n";
} else {
	print " done.\n";
}
?>