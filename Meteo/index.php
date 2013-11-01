<?php
if(!file_exists("../config.ini")) {
	require_once("../common/tpl/no_config.ini.tpl");
	exit();
}
$config = parse_ini_file("../config.ini", 1);
if(!is_array($config["Meteo Station"])){
	require_once("../common/tpl/no_conf.tpl");
	exit();
}
$NAS_absolute_uri = $config["NAS"]["http_root"] . "/";
$NAS_station_uri = $NAS_absolute_uri . "Meteo/";
if(isset($_GET["m"]) && trim($_GET["m"]) !== "") {
	switch($_GET["m"]){
		case "Giornaliero":
			$title_add = " ~ Dati giornalieri";
			$title_content_subpage = "Statistiche Meteo giornaliere";
			break;
		case "Settimanale":
			$title_add = " ~ Dati settimanali";
			$title_content_subpage = "Statistiche Meteo settimanali";
			break;
		case "Mensile":
			$title_add = " ~ Dati mensili";
			$title_content_subpage = "Statistiche Meteo mensili";
			break;
		case "Annuale":
			$title_add = " ~ Dati annuali";
			$title_content_subpage = "Statistiche Meteo annuali";
			break;
		case "Situazione_attuale":
		default:
			$title_add = " ~ Dati attuali";
			$title_content_subpage = "Statistiche Meteo attuali";
			break;
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Meteo <?php print $config["Meteo Station"]["name"] . $title_add; ?> | NinuXoo! (<?php print $config["NAS"]["name"]; ?>) - Ninux.org</title>
	
	<base href="./" />
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta name="author" content="Ninux.org Community - the Ninux Software Team" />
	<meta name="description" content="Ninux.org <?php print $config["Meteo Station"]["name"]; ?>" />
	
	<link rel="shortcut icon" href="<?php print $NAS_absolute_uri; ?>common/media/favicon.ico" type="image/x-icon" />
	<link rel="stylesheet" href="<?php print $NAS_absolute_uri; ?>common/css/main.css" type="text/css" media="screen" />
	
	<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/include/main.js"></script>
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>common/js/jquery-1.7.2.min.js"></script>
	<!-- qTip -->
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>common/js/jquery.qtip-1.0.0-rc3.min.js"></script>
	<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>common/js/qtip-integration.js"></script>
	<?php if(!isset($_GET["m"]) || trim($_GET["m"]) == "") { ?>
		<link rel="stylesheet" href="<?php print $NAS_station_uri; ?>common/css/meteo.css" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>common/js/jquery.highlight-4.js"></script>
		<!-- OpenData -->
		<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/OpenLayers-2.12/OpenLayers.js"></script>
		<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/openweathermap_forecast_ct.js"></script>
		<script type="text/javascript">
		function update(){
			var title = "<cite>Meteo <?php print $config["Meteo Station"]["name"]; ?></cite>";
			
			$.ajax({
				// If JSON reads not work
				url: "<?php print $NAS_absolute_uri; ?>common/include/funcs/_ajax/read_json.php?uri=<?php print $config["Meteo Station"]["source_data_uri"]; ?>",
				//url: "<?php print $config["Meteo Station"]["source_data_uri"]; ?>",
				dataType: 'jsonp',
				async: false,
				jsonpCallback: 'jqueryCallback',
				success: function(data){
					if(data.main.temp) {
						place_data(title, data, "f");
						
						$.ajax({
							// If JSON reads not work
							url: "<?php print $NAS_absolute_uri; ?>common/include/funcs/_ajax/read_json.php?uri=http://openweathermap.org/data/2.1/find/name?q=<?php print $config["Meteo Station"]["city"]; ?>",
							//url: "http://openweathermap.org/data/2.1/find/name?q=<?php print $config["Meteo Station"]["city"]; ?>",
							dataType: 'jsonp',
							async: false,
							jsonpCallback: 'jqueryCallback',
							success: function(data){
								if(data.list[0].weather[0]) {
									var icon_id = data.list[0].weather[0].icon;
									$("#forecast").html('<img src="<?php print $NAS_station_uri; ?>common/media/img/Realll/' + conversion_table[icon_id].image + '" /><cite>' + conversion_table[icon_id].description + '</cite>').fadeIn(600);
									$("#text_lat").val(data.main.coord["lat"]);
									$("#text_lon").val(data.main.coord["lon"]);
									$("#city").html("<?php print $config["Meteo Station"]["city"]; ?><sup>" + data.main.sys["population"] + "ab.</sup>");
									
								}
							},
							async: true
						});
					} else {
						$("#weekly_stat").css("display", "none");
						$("#monthly_stat").css("display", "none");
						$("#yearly_stat").css("display", "none");
						$("#station_status").addClass("error").text("Non attiva");
						$("#temp").html(title + '<div class="error">Dati stazione meteo locale<br />non disponibili</div>').fadeIn(600);
					}
				},
				error: function(){
					$("#weekly_stat").css("display", "none");
					$("#monthly_stat").css("display", "none");
					$("#yearly_stat").css("display", "none");
					$("#station_status").addClass("error").text("Non attiva");
					
					$.ajax({
						// If JSON reads not work
						url: "<?php print $NAS_absolute_uri; ?>common/include/funcs/_ajax/read_json.php?uri=http://openweathermap.org/data/2.1/find/name?q=<?php print $config["Meteo Station"]["city"]; ?>",
						//url: "http://openweathermap.org/data/2.1/find/name?q=<?php print $config["Meteo Station"]["city"]; ?>",
						dataType: 'jsonp',
						async: false,
						jsonpCallback: 'jqueryCallback',
						success: function(data){
								console.log(data);
							if(data.list[0].main.temp) {
								var icon_id = data.list[0].weather[0].icon;
								$("#forecast").html('<img src="<?php print $NAS_station_uri; ?>common/media/img/Realll/' + conversion_table[icon_id].image + '" /><cite>' + conversion_table[icon_id].description + '</cite>').fadeIn(600);
								place_data(title, data.list[0], "k");
								
								$("#lat").val(data.list[0].coord["lat"]);
								$("#lon").val(data.list[0].coord["lon"]);
								$("#text_lat").val(data.list[0].coord["lat"]);
								$("#text_lon").val(data.list[0].coord["lon"]);
								$("#city").html("<?php print $config["Meteo Station"]["city"]; ?><sup>" + data.list[0].sys["population"] + "ab.</sup>");
							} else {
								$("#temp").html(title + '<div class="error">Dati stazione meteo locale<br />non disponibili</div>').fadeIn(600);
							}
						},
						error: function(error) {
							console.log(error);
						}
					});
				},
				async: false
			});
		}
		function update_ninux(map, vectors, markers, lon, lat){
			var fromProjection = new OpenLayers.Projection("EPSG:4326"),
			toProjection = new OpenLayers.Projection("EPSG:900913"),
			position = new OpenLayers.LonLat(lon, lat).transform(fromProjection, toProjection),
			size = new OpenLayers.Size(16, 25),
			offset = new OpenLayers.Pixel(-(size.w/2), -size.h),
			aicon = new OpenLayers.Icon("<?php print $NAS_station_uri; ?>common/media/img/marker_active.png", size, offset),
			hicon = new OpenLayers.Icon("<?php print $NAS_station_uri; ?>common/media/img/marker_hotspot.png", size, offset),
			redicon = new OpenLayers.Icon("<?php print $NAS_station_uri; ?>common/media/img/marker_new.png", size, offset),
			line_color = "",
			z_index = "",
			feature;
			
			$.ajax({
				// If JSON reads not work
				url: "<?php print $NAS_absolute_uri; ?>common/include/funcs/_ajax/read_json.php?uri=http://map.ninux.org/nodes.json",
				//url: "http://map.ninux.org/nodes.json",
				dataType: 'jsonp',
				async: false,
				jsonpCallback: 'jqueryCallback',
				success: function(data){
					if(data.active) {
						if(vectors){
							vectors.removeFeatures(vectors.features);
						}
						if(markers){
							markers.clearMarkers();
						}
						$.each(data, function(item, value){
							if(item != "potential"){ 
								$.each(value, function(node, ovalue){
									if(item == "links"){
										if(ovalue.etx <= 1.5){
											line_color = "#0f7f14";
											z_index = 1;
										} else if(ovalue.etx > 1.5 && ovalue.etx < 3){
											line_color = "#ffa200";
											z_index = 2;
										} else if(ovalue.etx >= 3){
											line_color = "#ff0000";
											z_index = 3;
										}
										vectors.addFeatures([new OpenLayers.Feature.Vector(
											new OpenLayers.Geometry.LineString([
												new OpenLayers.Geometry.Point(ovalue.from_lng, ovalue.from_lat),
												new OpenLayers.Geometry.Point(ovalue.to_lng, ovalue.to_lat)
											]).transform(new OpenLayers.Projection("EPSG:4326"),
											new OpenLayers.Projection("EPSG:900913")),
											null, {
												strokeColor: line_color, 
												strokeOpacity: 0.5, 
												strokeWidth: 5
											}
										)]);
									} else {
										if(ovalue.name != "<?php print $config["Ninux node"]["name"]; ?>"){
											position = new OpenLayers.LonLat(ovalue.lng, ovalue.lat).transform(fromProjection, toProjection);
											if(item == "active"){
												marker = new OpenLayers.Marker(position, aicon.clone());
											} else {
												marker = new OpenLayers.Marker(position, hicon.clone());
											}
											feature = new OpenLayers.Feature(markers, position);
											feature.data.popupContentHTML = ovalue.name;
										} else {
											position = new OpenLayers.LonLat(lon, lat).transform(fromProjection, toProjection);
											feature = new OpenLayers.Feature(markers, position);
											feature.data.popupContentHTML = ovalue.name;
											marker = new OpenLayers.Marker(position, redicon);
										}
										feature.popupClass = OpenLayers.Class(OpenLayers.Popup.Anchored, {
											"autoSize": true,
											"minSize": new OpenLayers.Size(20, 0),
											"maxSize": new OpenLayers.Size(400, 25),
											"keepInMap": true
										});
										marker.feature = feature;
										var markerClick = function(evt) {
											if (this.popup == null) {
												this.popup = this.createPopup(this.closeBox);
												map.addPopup(this.popup);
												this.popup.show();
											} else {
												this.popup.toggle();
											}
											OpenLayers.Event.stop(evt);
										};
										marker.events.register("mouseover", feature, markerClick);
										marker.events.register("mouseout", feature, markerClick);
										markers.addMarker(marker);
									}
								});
							}
						});
					}
				},
				async: true
			});
		}
		$(document).ready(function() {
			$(".filetree a[title]").qtip({style: {border: {width: 2, radius: 3}, color: "white", name: "dark", textAlign: "center", tip: true}, position: {corner: {target: "topMiddle", tooltip: "bottomMiddle"}}});
			$(".treecontrol a[title]").qtip({style: {border: {width: 2, radius: 3}, color: "white", name: "dark", textAlign: "center", tip: true}, position: {corner: {target: "topRight", tooltip: "bottomLeft"}}});
			
			//Center of map
			var lat = <?php print $config["Meteo Station"]["latitude"]; ?>; 
			var lon = <?php print $config["Meteo Station"]["longitude"]; ?>;
			var lonlat = new OpenLayers.LonLat(lon, lat);
			
			var opencyclemap = new OpenLayers.Layer.XYZ(
				"opencyclemap",
				"http://mt1.google.com/vt/lyrs=p&x=${x}&y=${y}&z=${z}", {
					isBaseLayer: false,
					numZoomLevels: 13,
					sphericalMercator: true,
					opacity: 0
				}
			);
			var google_satellite = new OpenLayers.Layer.XYZ(
				"opencyclemap",
				"http://mt1.google.com/vt/lyrs=y&x=${x}&y=${y}&z=${z}", {
					isBaseLayer: false,
					numZoomLevels: 19,
					sphericalMercator: true,
					opacity: 0
				}
			);
			var clouds = new OpenLayers.Layer.XYZ(
				"layer clouds",
				"http://${s}.tile.openweathermap.org/map/clouds/${z}/${x}/${y}.png", {
					isBaseLayer: false,
					numZoomLevels: 7,
					opacity: 0.75,
					sphericalMercator: true

				}
			);
			var pressure = new OpenLayers.Layer.XYZ(
				"layer pressure_cntr",
				"http://${s}.tile.openweathermap.org/map/pressure_cntr/${z}/${x}/${y}.png", {
					isBaseLayer: false,
					numZoomLevels: 7, 
					opacity: 0.2,
					sphericalMercator: true

				}
			);
			var map = new OpenLayers.Map("map");
			OpenLayers.Util.onImageLoadError = function() {
				this.src = "<?php print $NAS_station_uri; ?>common/media/img/blank.png";
				format : "image/jpg";
			};
			
			var country_border = new OpenLayers.Layer.Vector("KML", {
				projection: map.displayProjection,
				strategies: [new OpenLayers.Strategy.Fixed()],
				protocol: new OpenLayers.Protocol.HTTP({
					url: "<?php print $NAS_station_uri; ?>common/media/kml/Comuni/<?php print $config["Meteo Station"]["city"]; ?>.kml",
					format: new OpenLayers.Format.KML({
						extractStyles: true,
						extractAttributes: true
					})
				})
			});
			// Markers and lines
			var markers = new OpenLayers.Layer.Markers("Markers");
			var vectors = new OpenLayers.Layer.Vector("Links");
			
			//  OSM
			var mapnik = new OpenLayers.Layer.OSM("OpenCycleMap", ['http://a.tile3.opencyclemap.org/landscape/${z}/${x}/${y}.png',
															      'http://b.tile3.opencyclemap.org/landscape/${z}/${x}/${y}.png',
															      'http://c.tile3.opencyclemap.org/landscape/${z}/${x}/${y}.png']);
			//Addind maps
			map.addLayers([mapnik, google_satellite, country_border, vectors, markers, clouds, pressure]);
				country_border.setZIndex(1003);
				vectors.setZIndex(1004);
				markers.setZIndex(1005);
				clouds.setZIndex(1007);
				pressure.setZIndex(1008);
				opencyclemap.events.register("loadend", opencyclemap, function() {
					opencyclemap.setOpacity(1);
				});
				map.addLayer(opencyclemap);
				opencyclemap.setZIndex(1001);
				google_satellite.setZIndex(1002);
			
			var center = new OpenLayers.LonLat(lon, lat).transform(
				new OpenLayers.Projection("EPSG:4326"),
				map.getProjectionObject()
			);
			var mousewheel = new OpenLayers.Control();
			OpenLayers.Util.extend(mousewheel, {
				// The draw method is called when the control is initialized
				draw: function () {
					this.mouse = new OpenLayers.Handler.MouseWheel(mousewheel, {"up": 
						mouseFunc, "down": mouseFunc});
					this.mouse.activate();
				}
			});
			map.div.oncontextmenu = function noContextMenu(e) {return false;};
			var layers = map.getLayersBy("visibility", true);
			var activeLayer = null;
			
			map.addControl(mousewheel);
			function mouseFunc(){
				if (map.getZoom() >= 15){
					google_satellite.setOpacity(0.5);
				} else {
					google_satellite.setOpacity(0);
				}
			}
			
			map.setCenter(center, 12);
			var layers = [pressure, clouds];
			
			update();
			update_ninux(map, vectors, markers, lon, lat);
			refresh(layers);
			recenter_map(map);
			setInterval(function() {
				update();
				update_ninux(map, vectors, markers, lon, lat);
				refresh(layers);
			}, <?php print $config["Meteo Station"]["update_interval"]; ?>000);
		});
		</script>
	<?php } else { ?>
		<!-- Zoombox -->
		<link href="<?php print $NAS_absolute_uri; ?>common/js/zoombox/zoombox.css" rel="stylesheet" type="text/css" media="screen" />
		<script type="text/javascript" src="<?php print $NAS_absolute_uri; ?>common/js/zoombox/zoombox.js"></script>
		<link rel="stylesheet" href="<?php print $NAS_station_uri; ?>common/css/charts.css" type="text/css" media="screen" />
		<!-- Highcharts -->
		<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/Highcharts-2.3.5/js/highcharts.js"></script>
		<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/Highcharts-2.3.5/js/highcharts-more.js"></script>
		<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/Highcharts-2.3.5/js/modules/exporting.js"></script>
		<script type="text/javascript" src="<?php print $NAS_station_uri; ?>common/js/charts.js"></script>
		<script type="text/javascript">
		var time_zone = 1000 * (new Date().getTimezoneOffset())*(-60);
		function  errorHandler(e) {
			console.log(e.status +' '+e.statusText);
		}
		function daysInMonth(month,year) {
			return new Date(year, month, 0).getDate();
		}
		function requestData(refresh) {
			var station_status = "false";
			if(refresh == "undefined" || refresh == null){
				refresh = "";
			} else {
				setInterval(requestData, refresh);
			}
			
			$.ajax({
				// If JSON reads not work
				url: "<?php print $NAS_absolute_uri; ?>common/include/funcs/_ajax/read_json.php?uri=<?php print $config["Meteo Station"]["source_data_uri"]; ?>",
				//url: "<?php print $config["Meteo Station"]["source_data_uri"]; ?>",
				dataType: 'jsonp',
				async: false,
				jsonpCallback: 'jqueryCallback',
				success: function(data){
					if(data.list) {
						station_status = "true";
					} else {
						station_status = "false";
					}
				}
			});
			if(station_status == "false"){
				$("#weekly_stat").css("display", "none");
				$("#monthly_stat").css("display", "none");
				$("#yearly_stat").css("display", "none");
				
				var curd = new Date(),
				d = new Date(2012, 6, curd.getDate()),
				s = 0,
				type = "",
				cnt = 80;
				switch ("<?php print $_GET["m"]; ?>"){
					case "Giornaliero":
						s = Math.round(( d.getTime() ) /1000) - 3600*24;
						type = "hour";
						break;
					case "Settimanale":
						s = Math.round(( d.getTime() ) /1000) - (3600*(24 * 7));
						cnt = 7;
						type = "day";
						break;
					case "Mensile":
						s = Math.round(( d.getTime() ) /1000) - (3600*(24 * daysInMonth(curd.getMonth(), curd.getDate())));
						cnt = daysInMonth(curd.getMonth(), curd.getDate());
						type = "day";
						break;
					case "Annuale":
						s = Math.round(( d.getTime() ) /1000) - (3600*(24 * 365));
						cnt = 365;
						type = "day";
						break;
				}
				$.ajax({
					// If JSON reads not work
					url: "<?php print $NAS_absolute_uri; ?>common/include/funcs/_ajax/read_json.php?uri=http://openweathermap.org/data/2.1/history/city/?id=<?php print $config["Meteo Station"]["OpenWeatherID"]; ?>&cnt=" + cnt + "&type=" + type,
					//url: "http://openweathermap.org/data/2.1/history/city/?id=<?php print $config["Meteo Station"]["OpenWeatherID"]; ?>&cnt=" + cnt + "&type=" + type,
					dataType: "json",
					async: false,
					jsonpCallback: 'jqueryCallback',
					success: function(data) {
						$.showPolarSpeed("chart-wind", data.list, function(){
							$.showWind("chart-wind-d", data.list, function(){
								//$.showIconsChart("chart-temp", data.list, function(){
									$.showBarsDouble("chart-temp-a", data.list, function(){
										$.showSimpleChart("chart-temp-d", data.list, function(){
											$("#loader").fadeOut(300);
										});
									});
								//});
							});
						});
					},
					cache: false
				});
			} else {
			}
		}
		$(document).ready(function() {
			$("a.zoombox").zoombox({
				theme: 'zoombox',
				opacity: 0.9,
				duration: 900,
				animation: true,
				gallery: true,
				autoplay: false
			});
			$("#loader").fadeIn(300);
			requestData();
		});
		</script>
	<?php } ?>
</head>
<body>
	<input type="hidden" id="lat" value="" />
	<input type="hidden" id="lon" value="" />
	<div id="top_menu">
		<table>
			<tr>
				<td>
					<ul>
						<li><a href="<?php print $NAS_absolute_uri; ?>" id="" title="Ninuxoo">Home</a></li>
						<li><a href="javascript:void(0);" id="whatsnew" title="Nuovi files indicizzati">Novit&agrave;</a></li>
						<li><a href="http://10.168.177.178:8888/" title="Ascolta la musica in rete">Juke Box</a></li>
						<li><a href="http://ninuxoo.ninux.org/cgi-bin/proxy_wiki.cgi?url=Elenco_Telefonico_rete_VoIP_di_ninux.org" title="Elenco telefonico"><acronym title="Voice over IP">VoIP</acronym></a></li>
						<li><a href="<?php print $NAS_absolute_uri; ?>Meteo" title="Visualizza dati meteo in tempo reale">Meteo</a></li>
						<li class="separator">&nbsp;</li>
						<li><a href="http://blog.ninux.org/" title="Vai al nostro Blog">Blog</a></li>
						<li><a href="http://wiki.ninux.org/" title="Vai al nostro Wiki">Wiki</a></li>
					</ul>
				</td>
				<td>
					<ul id="second_menu">
						<li><a href="<?php print $NAS_station_uri; ?>" title="Grafici in tempo reale" <?php print $selected_second_menu_now; ?>>Situazione attuale</a></li>
						<li class="separator">&nbsp;</li>
						<li><a id="daily_stat" href="<?php print $NAS_station_uri; ?>Giornaliero" title="Statistiche giornaliere" <?php print $selected_second_menu_day; ?>>Giornaliero</a></li>
						<li><a id="weekly_stat" href="<?php print $NAS_station_uri; ?>Settimanale" title="Statistiche settimanali" <?php print $selected_second_menu_week; ?>>Settimanale</a></li>
						<li><a id="monthly_stat" href="<?php print $NAS_station_uri; ?>Mensile" title="Statistiche mensili" <?php print $selected_second_menu_month; ?>>Mensile</a></li>
						<li><a id="yearly_stat" href="<?php print $NAS_station_uri; ?>Annuale" title="Statistiche annuali" <?php print $selected_second_menu_year; ?>>Annuale</a></li>
					</ul>
				</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
		</table>
	</div>
	<?php if(!isset($_GET["m"]) || trim($_GET["m"]) == "") { ?>
		<div id="map"></div>
		<div id="forecast"></div>
		<div id="temp"></div>
		<div id="station_data">
			<h1>Dati Stazione Meteo:</h1>
			<ul>
				<li>Citt&agrave;: <span id="city"><?php print $config["Meteo Station"]["city"]; ?></span></li>
				<li>Quota: <span id="quota"><?php print $config["Meteo Station"]["altitude_" . $config["Meteo Station"]["default_altitude_unit"]]; ?><sup><?php print $config["Meteo Station"]["default_altitude_unit"]; ?></sup></span></li>
				<li>Latitudine: <span id="text_lat"><?php print $config["Meteo Station"]["latitude"]; ?></span></li>
				<li>Longitudine: <span id="text_lon"><?php print $config["Meteo Station"]["longitude"]; ?></span></li>
				<li>Stato: <span id="station_status"></span></li>
			</ul>
		</div>
	<?php } else { ?>
		<div id="main_container">
			<div id="header">
				<table>
					<tr>
						<td>
							<a href="">
								<img alt="Logo Ninuxoo" src="https://192.168.36.200/common/media/img/logo.png">
							</a>
							<h1>Meteo <?php print $config["Meteo Station"]["name"]; ?></h1>
						</td>
					</tr>
				</table>
			</div>
			<div id="container">
				<div id="content">
					<div id="container">
						<div id="page_content">
							<h1>Dati Meteo alla mano</h1>
							<h2><?php print $title_content_subpage; ?></h2>
							<div id="right_menu">
								<h3>STAZIONE<span>di <?php print $config["Meteo Station"]["city"]; ?></span></h3>
								<ul>
									<li class="exactresults">
										Quota: <a><?php print $config["Meteo Station"]["altitude_" . $config["Meteo Station"]["default_altitude_unit"]]; ?><sup><?php print $config["Meteo Station"]["default_altitude_unit"]; ?></sup></a>
										<span>Sul livello del mare</span>
									</li>
									<li>Latitudine: <a><?php print $config["Meteo Station"]["latitude"]; ?></a></li>
									<li>Longitudine: <a><?php print $config["Meteo Station"]["longitude"]; ?></a></li>
								</ul>
								<ul>
									<li>
										<a class="zoombox" href="http://realtime.rete.meteonetwork.it/cem/virtual_sat/nuvolosita_mov.gif">
											<img src="http://realtime.rete.meteonetwork.it/cem/virtual_sat/nuvolosita_mov_thumb.gif" style="width: 100%;" />
										</a>
									</li>
									<li>
										<a class="zoombox" href="http://realtime.rete.meteonetwork.it/cem/full_size/<?php print strtolower($config["Meteo Station"]["region"]); ?>_pres_colori/mov.gif">
											<img src="http://realtime.rete.meteonetwork.it/cem/thumb/realtime_<?php print strtolower($config["Meteo Station"]["region"]); ?>_pres_colori.png" style="width: 100%;" />
										</a>
									</li>
								</ul>
							</div>
							<div class="search_results">
								<div id="loader"></div>
								<div class="row">
									<div class="span6">
										<div id="chart-wind"></div>
									</div>
									<div class="span6">
										<div id="chart-wind-d"></div>
									</div>
								</div>
								<div class="row">
									<div class="span12">
										<div id="chart-temp"></div>
									</div>
								</div>
								<div class="row">
									<div class="span12">
										<div id="chart-temp-a"></div>
									</div>
								</div>
								<div class="row">
									<div class="span12">
										<div id="chart-temp-d"></div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<?php } ?>
</html>
</body>