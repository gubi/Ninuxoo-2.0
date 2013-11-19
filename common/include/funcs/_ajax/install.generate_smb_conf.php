<?php
header("Content-type: text/plain");

$output["smb_conf_dir"] = str_replace("//", "/", $output["smb_conf_dir"] . "/");
$output["server_root"] = str_replace("//", "/", $output["server_root"] . "/");
$output["api_dir"] = str_replace("//", "/", $output["api_dir"] . "/");

$smb_conf = '; NINUXOO CONFIGURATION FILE' . "\n\n";

$smb_conf .= '[Ninux node]' . "\n";
$smb_conf .= 'name = "' . $output["node_name"] . '"' . "\n";
$smb_conf .= 'map_server_uri = "' . $output["node_map"] . '"' . "\n";
$smb_conf .= 'node_type = "' . $output["node_type"] . '" ;Use "hotspot", "active" or "potential"' . "\n\n";

$smb_conf .= '[NAS]' . "\n";
$smb_conf .= 'name = "' . $output["nas_name"] . '" ;NAS name' . "\n";
$smb_conf .= 'description = "' . $output["nas_description"] . '" ;NAS Description' . "\n";
$smb_conf .= 'http_root = "' . $output["uri_address"] . '"' . "\n\n";

$smb_conf .= 'smb_conf_dir = "' . $output["smb_conf_dir"] . '"' . "\n";
$smb_conf .= 'root_dir = "' . $output["server_root"] . '"' . "\n";
$smb_conf .= 'listing_file_dir = "' . $output["api_dir"] . '"' . "\n\n";

$smb_conf .= ';Public shared directories array' . "\n";
$shared_dirs = explode("\n", trim($output["smb_conf_paths"]));
foreach($shared_dirs as $kshared => $shared) {
	$info = pathinfo($shared);
	$smb_conf .= 'smb_shares[] = "/' . trim($info["basename"]) . '"' . "\n" . (($kshared == (count($shared_dirs) - 1)) ? "\n" : "");
}

$smb_conf .= ';Auto updated data (do not edit)' . "\n";
$smb_conf .= 'last_scan_date = "' . date("Y-m-d") . '"' . "\n";
$smb_conf .= 'last_items_count = 0' . "\n";
$smb_conf .= 'last_scanning_time = 0' . "\n\n";

$smb_conf .= '[Meteo Station]' . "\n";
$smb_conf .= 'active = "' . (($output["install_meteo"] == "on") ? "true" : "false") . '"' . "\n";
$smb_conf .= 'working = "' . ((isset($output["mysql_db_name"]) && trim($output["mysql_db_name"]) !== "") ? "true" : "false") . '"' . "\n\n";
$smb_conf .= 'name = "' . $output["meteo_name"] . '"' . "\n";
$smb_conf .= ';Set the center of map and retrieve its borders' . "\n";
$smb_conf .= 'city = "' . $output["meteo_city"] . '"' . "\n";
$smb_conf .= 'region = "' . $output["meteo_region"] . '"' . "\n";
$smb_conf .= 'country = "' . $output["meteo_country"] . '"' . "\n";
$smb_conf .= 'OpenWeatherID = ' . $output["meteo_owid"] . ' ;OpenWeatherMap city ID. Find yours to http://openweathermap.org/data/2.1/find/name?q=CITY_NAME' . "\n";
$smb_conf .= 'source_data_uri = "' . $output["api_dir"] . 'station.php"' . "\n";
$smb_conf .= 'http_root = "' . $output["uri_address"] . '/Meteo/" ;NAS Meteo Station web uri' . "\n\n"; 

$smb_conf .= ';If you want to get this data, please visit http://www.earthtools.org/' . "\n";
$smb_conf .= 'altitude_mt = ' . $output["meteo_altitude_mt"] . "\n";
$smb_conf .= 'altitude_ft = ' . $output["meteo_altitude_ft"] . "\n";
$smb_conf .= 'default_altitude_unit = "' . $output["meteo_altitude_unit"] . '"' . "\n";
$smb_conf .= 'latitude = ' . $output["meteo_lat"] . "\n";
$smb_conf .= 'longitude = ' . $output["meteo_lng"] . "\n";

$mysql_conf = "[database]\n";
$mysql_conf .= 'host = "' . $output["mysql_host"] . '"' . "\n";
$mysql_conf .= 'db_name = "' . $output["mysql_db_name"] . '"' . "\n";
$mysql_conf .= 'username = "' . $output["mysql_username"] . '"' . "\n";
$mysql_conf .= 'password = "' . $output["mysql_password"] . '"' . "\n";
$mysql_conf .= 'tables = "' . $output["mysql_db_table"] . '"';


// Files creation
// Main config file
if($fp = fopen($output["server_root"] . "common/include/conf/config.ini", "w")) {
	fwrite($fp, $smb_conf . PHP_EOL);
	fclose($fp);
	
	// Database config file
	if($fdb = fopen($output["server_root"] . "common/include/conf/.db.conf", "w")) {
		fwrite($fdb, $mysql_conf . PHP_EOL);
		fclose($fdb);
		
		// Crontab
		$fc = fopen($output["server_root"] . "crontab", "w+");
		if(fwrite($fc, "# Ninuxoo Local scan job\n00 */6 * * * root /usr/bin/php " . $output["server_root"] . "scan.php" . PHP_EOL) === false) {
			$data = "error::Sono stati riscontrati dei problemi nella creazione del crontab.\nInstallazione avvenuta con successo.\nInstallare il cronjob manualmente.\nConsultare la documentazione per maggiori informazioni.";
		} else {
			fclose($fc);
			exec("crontab " . $output["server_root"] . "crontab");
			
			$data = "ok";
		}
		
		// Symbolic link for http (securely) shares
		exec("ln -s " . $output["smb_conf_dir"] . " " . $output["server_root"] . "shared/");
	} else {
		$data = "error::Non si gode dei permessi sufficienti per creare il file di config per il database MySQL.\nInstallazione parzialmente riuscita :/";
	}
} else {
	$data = "error::Non si gode dei permessi sufficienti per creare il file di config.\nInstallazione non riuscita :(";
}

print json_encode(array("data" => $data));
?>