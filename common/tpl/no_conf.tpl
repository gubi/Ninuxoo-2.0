<?php
$ip = $_SERVER["SERVER_ADDR"];
	$body = <<<Body
<div style="font-family: Arial, Helvetica; margin-bottom: 15px;"><h1>Hello guy!</h1>
You need to configure your Meteo Station.<br />
Please, open config.ini file in the parent folder and add these lines:
</div>
<div style="background: #fafafa; border: #ccc 1px solid; padding: 15px; font-family: monospace;">
[Meteo Station]<br />
name = "Your Meteo Station name"<br />
<br />
;Set the center of map and retrieve its borders<br />
city = "Your city name" ;Note: this is important to set the center and Country borders of a map<br />
region = "Your Region"<br />
region_area = "Your Region area"<br />
country = "Your Country"<br />
OpenWeatherID = xxxxxxx ;OpenWeatherMap city ID. Find it to <a href="http://openweathermap.org/data/2.1/find/name?q=CITY_NAME" target="_blank">http://openweathermap.org/data/2.1/find/name?q=CITY_NAME</a><br />
source_data_uri = "https://192.168.36.100/Meteo/API/station.php" ;Your local Station data source<br />
http_root = "https://$ip/Meteo/" ;NAS Meteo Station web uri<br />
<br />
;; More data for this Station<br />
;If you want to get this data, please visit http://www.earthtools.org/<br />
altitude_mt = xxx<br />
altitude_ft = xxxx<br />
default_altitude_unit = "mt"<br />
latitude = 41.8<br />
longitude = 12.8<br />
</div>
Body;
print $body;
?>