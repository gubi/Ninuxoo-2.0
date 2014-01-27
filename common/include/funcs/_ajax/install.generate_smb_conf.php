<?php
header("Content-type: text/plain");
require_once("../../classes/logging.class.php");
require_once("../../classes/mdns.class.php");
require_once("../../classes/rsa.class.php");

$log = new Logging();
$log->file("../../log/ninuxoo.log");
$log->write("notice", "[install] Started");

$output["root_share_dir"] = str_replace("//", "/", $output["root_share_dir"] . "/");
$output["server_root"] = str_replace("//", "/", $output["server_root"] . "/");
$output["api_dir"] = str_replace("//", "/", $output["api_dir"] . "/");


	$mdns = new mdns();
	$owner_key = $mdns->get_owner_key($output["user_username"]);
	$owner = $mdns->get_owner($owner_key);
	
$user_conf = '[User]' . "\n";
$user_conf .= 'name = "' . $owner["name"] . '"' . "\n";
$user_conf .= 'key = "' . $owner_key . '"' . "\n";
$user_conf .= 'email = "' . $output["user_username"] . '"' . "\n";
$user_conf .= 'pass = "' . sha1($output["user_password"]) . '"' . "\n\n";
$user_conf .= 'use_editor_always = "false"' . "\n";
$user_conf .= 'editor_theme = "default"' . "\n\n";
$user_conf .= '[Notification]' . "\n";
$user_conf .= 'new_files = "true"' . "\n";
$user_conf .= 'new_chat_messages = "true"' . "\n\n";
$user_conf .= '[Chat]' . "\n";
$user_conf .= 'show_ip = "false"' . "\n";
$user_conf .= 'refresh_interval = 30000' . "\n";

$general_settings = '[login]' . "\n";
$general_settings .= 'session_length = 3600' . "\n";
$general_settings .= 'allow_user_registration = "true"' . "\n\n";
$general_settings .= 'admin[] = "' . sha1($output["user_username"]) . '"' . "\n\n";
$general_settings .= '[searches]' . "\n";
$general_settings .= 'show_ip = "false"' . "\n";
$general_settings .= 'allow_advanced_research = "true"' . "\n";
$general_settings .= 'research_type = "query"' . "\n";
$general_settings .= 'research_results = 200' . "\n\n";
$general_settings .= '[file data]' . "\n";
$general_settings .= 'scan_ebook_name_order = "title_author_editor"' . "\n";
$general_settings .= 'scan_ebook_name_regex = ""' . "\n\n";
$general_settings .= 'scan_audio_name_order = "no_track_artist_album"' . "\n";
$general_settings .= 'scan_audio_name_regex = ""' . "\n\n";
$general_settings .= 'scan_video_name_order = "title_year_director"' . "\n";
$general_settings .= 'scan_video_name_regex = ""' . "\n";


$config_ini = '; NINUXOO CONFIGURATION FILE' . "\n\n";
$config_ini .= '[Ninux node]' . "\n";
$config_ini .= 'name = "' . $output["node_name"] . '"' . "\n";
$config_ini .= 'map_server_uri = "' . $output["node_map"] . '"' . "\n";
$config_ini .= 'node_type = "' . $output["node_type"] . '" ;Use "hotspot" or "active"' . "\n\n";
$config_ini .= '[NAS]' . "\n";
$config_ini .= 'name = "' . $output["nas_name"] . '" ;NAS name' . "\n";
$config_ini .= 'description = "' . $output["nas_description"] . '" ;NAS Description' . "\n";
$config_ini .= 'http_root = "' . $output["uri_address"] . '"' . "\n\n";
$config_ini .= 'root_share_dir = "' . $output["root_share_dir"] . '"' . "\n";
$config_ini .= 'root_dir = "' . $output["server_root"] . '"' . "\n";
$config_ini .= 'listing_file_dir = "' . $output["api_dir"] . '"' . "\n\n";
$config_ini .= ';Public shared directories array' . "\n";
$shared_dirs = explode("\n", trim($output["shared_paths"]));
foreach($shared_dirs as $kshared => $shared) {
	$info = pathinfo($shared);
	$config_ini .= 'nas_shares[] = "/' . trim($info["basename"]) . '"' . "\n" . (($kshared == (count($shared_dirs) - 1)) ? "\n" : "");
}
$config_ini .= ';Auto updated data (do not edit)' . "\n";
$config_ini .= 'last_scan_date = "' . date("Y-m-d") . '"' . "\n";
$config_ini .= 'last_items_count = 0' . "\n";
$config_ini .= 'last_scanning_time = 0' . "\n\n";
$config_ini .= '[Mail]' . "\n";
$config_ini .= ';Program that send e-mail' . "\n";
$config_ini .= ';Use "server" for Sendmail or Postfix' . "\n";
$config_ini .= ';If you have problem use "program" for use your own smtp data.' . "\n";
$config_ini .= 'mail_program = "server"' . "\n\n";
$config_ini .= 'sendmail_path = "/usr/lib/sendmail"' . "\n";
$config_ini .= 'host = ""' . "\n";
$config_ini .= 'port = "587"' . "\n";
$config_ini .= 'auth = "true"' . "\n";
$config_ini .= 'username = ""' . "\n";
$config_ini .= 'password = ""' . "\n\n";
$config_ini .= '[Meteo]' . "\n";
$config_ini .= 'station_active = "' . (($output["install_meteo"] == "on") ? "true" : "false") . '"' . "\n";
$config_ini .= 'station_working = "' . ((isset($output["mysql_db_name"]) && trim($output["mysql_db_name"]) !== "") ? "true" : "false") . '"' . "\n\n";
$config_ini .= 'station_name = "' . $output["meteo_name"] . '"' . "\n\n";
$config_ini .= ';Set the center of map and retrieve its borders' . "\n";
$config_ini .= 'station_city = "' . $output["meteo_city"] . '"' . "\n";
$config_ini .= 'station_region = "' . $output["meteo_region"] . '"' . "\n";
$config_ini .= 'station_country = "' . $output["meteo_country"] . '"' . "\n";
$config_ini .= 'OpenWeatherID = ' . $output["meteo_owid"] . ' ;OpenWeatherMap city ID. Find yours to http://openweathermap.org/data/2.1/find/name?q=CITY_NAME' . "\n";
$config_ini .= 'source_data_uri = "' . $output["api_dir"] . 'station.php"' . "\n";
$config_ini .= 'http_root = "' . $output["uri_address"] . '/Meteo/" ;NAS Meteo Station web uri' . "\n\n"; 
$config_ini .= ';You can retrieve following data from http://www.earthtools.org/' . "\n";
$config_ini .= 'altitude_mt = ' . $output["meteo_altitude_mt"] . "\n";
$config_ini .= 'altitude_ft = ' . $output["meteo_altitude_ft"] . "\n";
$config_ini .= 'default_altitude_unit = "' . $output["meteo_altitude_unit"] . '"' . "\n";
$config_ini .= 'latitude = ' . $output["meteo_lat"] . "\n";
$config_ini .= 'longitude = ' . $output["meteo_lng"] . "\n\n";
$config_ini .= 'show_ninux_nodes = "true"' . "\n";
$config_ini .= 'show_region_area = "true"' . "\n";
$config_ini .= 'refresh_interval = 48000' . "\n";

$db_conf = "[database]\n";
$db_conf .= 'type = "' . $output["db_type"] . '" ;use "mysql", "sqlite" or "postgresql"' . "\n\n";
$db_conf .= 'host = "' . $output["mysql_host"] . '"' . "\n";
$db_conf .= 'db_name = "' . $output["mysql_db_name"] . '"' . "\n";
$db_conf .= 'username = "' . $output["mysql_username"] . '"' . "\n";
$db_conf .= 'password = "' . $output["mysql_password"] . '"' . "\n";
$db_conf .= 'table = "' . $output["mysql_db_table"] . '"' . "\n";

$avahi_service = '<?xml version="0.0" standalone="no"?>' . "\n";
$avahi_service .= '<!DOCTYPE service-group SYSTEM "avahi-service.dtd">' . "\n";
$avahi_service .= '<service-group>' . "\n";
$avahi_service .= '	<name replace-wildcards="yes">' . $output["nas_name"] . '</name>' . "\n";
$avahi_service .= '	<service>' . "\n";
$avahi_service .= '    	<type>_dns-sd._udp</type>' . "\n";
$avahi_service .= '		<port>64689</port>' . "\n";
	$rsa = new rsa();
	$token = $rsa->get_token(file_get_contents($output["server_root"] . "common/include/conf/rsa_2048_pub.pem"));
$avahi_service .= ' 		<txt-record>Hello guys I\'m a Ninuxoo device:' . base64_encode($owner_key . "::" . $output["meteo_city"] . " ~ " . $output["meteo_zone"] . "::" . $token) . '</txt-record>' . "\n";
$avahi_service .= '	</service>' . "\n";
$avahi_service .= '</service-group>';

// Files creation
// Main config file
if($fp = @fopen($output["server_root"] . "common/include/conf/config.ini", "w")) {
	fwrite($fp, $config_ini . PHP_EOL);
	fclose($fp);
	$log->write("notice", "[install] The new file 'config.ini' is located in 'common/include/conf/'");
	
	// Main config file
	if($fg = @fopen($output["server_root"] . "common/include/conf/general_settings.ini", "w")) {
		fwrite($fg, $general_settings . PHP_EOL);
		fclose($fg);
		$log->write("notice", "[install] The new file 'general_settings.ini' is located in 'common/include/conf/'");
		
		// User login file
		if(!file_exists($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]))) {
			mkdir($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]) . "/");
			chmod($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]) . "/", 0777);
			mkdir($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]) . "/configs/");
			chmod($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]) . "/configs/", 0777);
		}
		if($fu = @fopen($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]) . "/user.conf", "w")) {
			fwrite($fu, $user_conf . PHP_EOL);
			fclose($fu);
			$log->write("notice", "[install] The new file 'user.conf' is located in 'common/include/conf/user/" . sha1($output["user_username"]) . "/'");
			
			// User public PGP file
			if($fpgp = @fopen($output["server_root"] . "common/include/conf/user/" . sha1($output["user_username"]) . "/pubkey.asc", "w")) {
				fwrite($fpgp, $output["pgp_pubkey"] . PHP_EOL);
				fclose($fpgp);
				$log->write("notice", "[install] The new file 'pubkey.asc' is located in 'common/include/conf/user/" . sha1($output["user_username"]) . "/'");
				
				// Avahi service
				if($fas = @fopen($output["server_root"] . "common/include/conf/ninuxoo.service", "w")) {
					fwrite($fas, $avahi_service . PHP_EOL);
					fclose($fas);
					$log->write("notice", "[install] The new file 'ninuxoo.service' is located in 'common/include/conf/'");
					
					// Database config file
					if($fdb = @fopen($output["server_root"] . "common/include/conf/db.ini", "w")) {
						fwrite($fdb, $db_conf . PHP_EOL);
						fclose($fdb);
						$log->write("notice", "[install] The new file 'db.ini' is located in 'common/include/conf/'");
						
						// Crontab
						if($fc = @fopen($output["server_root"] . "crontab", "w+")) {
							fwrite($fc, "# Ninuxoo Local scan job\n00 */6 * * * root /usr/bin/php " . $output["server_root"] . "scan.php" . PHP_EOL);
							fclose($fc);
							
							if(!exec("crontab " . $output["server_root"] . "crontab")) {
								$log->write("notice", "[warning] A problem has occurred during installation of crontab. Please open and copy '" . $output["server_root"] . "crontab' and paste in '$ (sudo) crontab -e' manually.");
							}
							mail($output["user_username"], "Benvenuto in Ninuxoo!", "Prova", "From: ninuxoo@ninux.org");
							$data = "ok";
						} else {
							$log->write("error", "[install] Can't create 'crontab' file in '" . $output["server_root"] . "'. Installation malformed");
							$data = "error::Sono stati riscontrati dei problemi nella creazione del crontab.\nInstallazione avvenuta con successo.\nInstallare il cronjob manualmente.\nConsultare la documentazione per maggiori informazioni.";
						}
					} else {
						$log->write("error", "[install] Can't create 'db.ini' in 'common/include/conf/'. Installation malformed");
						$data = "error::Non si gode dei permessi sufficienti per salvare la chiave PGP dell'utente.\nInstallazione parzialmente riuscita :/";
					}
				} else {
					$log->write("error", "[install] Can't create 'ninuxoo.service' in 'common/include/conf/'. Installation malformed");
					$data = "error::Non si gode dei permessi sufficienti per salvare il file per annunciare il NAS in rete.\nSar&agrave; necessario generarlo manualmente. Per maggiori info seguire la documentazione.\nInstallazione parzialmente riuscita :/";
				}
			} else {
				$log->write("error", "[install] Can't create 'pubkey.asc' in 'common/include/conf/user/" . sha1($output["user_username"]) . "/'. Installation malformed");
				$data = "error::Non si gode dei permessi sufficienti per creare il file di config per il database MySQL.\nInstallazione parzialmente riuscita :/";
			}
		} else {
			$log->write("error", "[install] Can't create 'user.conf' in 'common/include/conf/" . sha1($output["user_username"]) . "/'. Installation malformed");
			$data = "error::Non si gode dei permessi sufficienti per creare il file di config per il database MySQL.\nInstallazione parzialmente riuscita :/";
		}
	} else {
		$log->write("error", "[install] Can't create 'general_settings.ini' in 'common/include/conf/'. Installation malformed");
		$data = "error::Non si gode dei permessi sufficienti per creare il file delle credenziali di accesso.\nInstallazione parzialmente riuscita :/";
	}
} else {
	$log->write("error", "[install] Can't create 'config.ini' in 'common/include/conf/'. Install aborted");
	$data = "error::Non si gode dei permessi sufficienti per creare il file di config.\nInstallazione non riuscita :(";
}

print json_encode(array("data" => $data));
?>

