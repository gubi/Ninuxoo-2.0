<?php
header("Content-type: text/plain");
require_once("Config/Lite.php");

$general_settings = new Config_Lite();
$general_settings->read("../../conf/general_settings.ini");
$general_settings->set("login", "session_length", $output["session_length"]);
$general_settings->set("login", "allow_user_registration",  ($output["allow_user_registration"] == "on") ? "true" : "false");
$general_settings->set("login", "allow_browser_save",  ($output["allow_browser_save"] == "on") ? "true" : "false");

$general_settings->set("file data", "scan_ebook_name_order", $output["data_scan_ebook"]);
$general_settings->set("file data", "scan_ebook_name_regex", $output["data_scan_ebook_personalized"]);
$general_settings->set("file data", "scan_audio_name_order", $output["data_scan_audio"]);
$general_settings->set("file data", "scan_audio_name_regex", $output["data_scan_audio_personalized"]);
$general_settings->set("file data", "scan_video_name_order", $output["data_scan_video"]);
$general_settings->set("file data", "scan_video_name_regex", $output["data_scan_video_personalized"]);

$general_settings->set("caching", "allow_caching", ($output["allow_caching"] == "on") ? "true" : "false");
$general_settings->set("caching", "save_semantic_data", ($output["save_semantic_data"] == "on") ? "true" : "false");
$general_settings->set("caching", "semantic_caching_refresh", $output["semantic_caching_refresh"]);
$general_settings->set("caching", "save_audio_spectum", ($output["save_audio_spectum"] == "on") ? "true" : "false");
$general_settings->save();

$conf = new Config_Lite();
$conf->read("../../conf/config.ini");
$conf->set("NAS", "nas_name", $output["nas_name"]);
$conf->set("NAS", "nas_description", $output["nas_description"]);
$conf->set("NAS", "http_root", $output["http_root"]);

$conf->set("Meteo", "station_active", $output["station_active"]);
$conf->set("Meteo", "station_name", $output["station_name"]);
$conf->set("Meteo", "show_ninux_nodes", ($output["show_ninux_nodes"] == "on") ? "true" : "false");
$conf->set("Meteo", "show_region_area", ($output["show_region_area"] == "on") ? "true" : "false");
$conf->set("Meteo", "refresh_interval", $output["meteo_refresh"]);

$conf->set("Meteo", "station_city", $output["meteo_city"]);
$conf->set("Meteo", "station_region", $output["meteo_region"]);
$conf->set("Meteo", "station_country", $output["meteo_country"]);

$conf->set("Meteo", "OpenWeatherID", $output["meteo_owid"]);
$conf->set("Meteo", "altitude_mt", $output["meteo_altitude_mt"]);
$conf->set("Meteo", "altitude_ft", $output["meteo_altitude_ft"]);
$conf->set("Meteo", "default_altitude_unit", $output["meteo_altitude_unit"]);
$conf->set("Meteo", "latitude", $output["meteo_lat"]);
$conf->set("Meteo", "longitude", $output["meteo_lng"]);
$conf->save();

print json_encode(array("data" => "ok"));
?>