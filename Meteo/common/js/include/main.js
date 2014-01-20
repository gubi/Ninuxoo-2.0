function get_wind_direction(wind_deg, type){
	var wind_dir = "",
	wind_dir_description = "";
	
	if(wind_deg == 0){
		wind_dir = "";
		wind_dir_description = "";
	} else if(wind_deg >= 0 && wind_deg < 22.5){
		wind_dir = "N";
		wind_dir_description = "Tramontana";
	} else if (wind_deg >= 22.5 && wind_deg < 45){
		wind_dir = "NNE";
		wind_dir_description = "Bora";
	} else if (wind_deg >= 45 && wind_deg < 67.5){
		wind_dir = "NE";
		wind_dir_description = "Grecale";
	} else if (wind_deg >= 67.5 && wind_deg < 90){
		wind_dir = "ENE";
		wind_dir_description = "Schiavo";
	} else if (wind_deg >= 90 && wind_deg < 112.5){
		wind_dir = "E";
		wind_dir_description = "Levante";
	} else if (wind_deg >= 112.5 && wind_deg < 135){
		wind_dir = "ESE";
		wind_dir_description = "Solano";
	} else if (wind_deg >= 135 && wind_deg < 157.5){
		wind_dir = "SE";
		wind_dir_description = "Scirocco";
	} else if (wind_deg >= 157.5 && wind_deg < 180){
		wind_dir = "SSE";
		wind_dir_description = "Africo";
	} else if (wind_deg >= 180 && wind_deg < 202.5){
		wind_dir = "S";
		wind_dir_description = "Ostro";
	} else if (wind_deg >= 202.5 && wind_deg < 225){
		wind_dir = "SSO";
		wind_dir_description = "Cauro";
	} else if (wind_deg >= 225 && wind_deg < 247.5){
		wind_dir = "SO";
		wind_dir_description = "Libeccio";
	} else if (wind_deg >= 247.5 && wind_deg < 270){
		wind_dir = "OSO";
		wind_dir_description = "Etesia";
	} else if (wind_deg >= 270 && wind_deg < 292.5){
		wind_dir = "O";
		wind_dir_description = "Ponente";
	} else if (wind_deg >= 292.5 && wind_deg < 315){
		wind_dir = "ONO";
		wind_dir_description = "Traversone";
	} else if (wind_deg >= 315 && wind_deg < 337.5){
		wind_dir = "NO";
		wind_dir_description = "Maestrale";
	} else if (wind_deg >= 337.5 && wind_deg < 360){
		wind_dir = "NNO";
		wind_dir_description = "Zefiro";
	}
	if(type == "direction"){
		return wind_dir;
	} else {
		return wind_dir_description;
	}
}
function rotate(degree) {
	$("#arrow").animate({textIndent: degree}, {
		step: function(now, fx) {
			$(this).css("-moz-transform", "rotate(" + now + "deg)");
		},
		duration: "slow"
	}, "linear");
}
function place_data(title, data, temp_measure){
	switch (temp_measure){
		case "k":
			var cent = Math.round((data.main.temp-273.15)*100)/100;
			break;
		case "c":
			var cent = data.main.temp;
			break;
		case "f":
		default:
			var cent = Math.round((data.main.temp-32)*5/9*100)/100;
			break;
	}
	var hum = data.main.humidity,
	press = Math.round(data.main.pressure*100)/100,
	wind_speed = Math.round((data.wind.speed * 1.85200) * 100)/100,
	temperature = '<div class="temperature">' + cent + "<sup>&#8451;<sup></div>",
	pressure = '<div><span class="humidity">' + hum + '</span><sup>%</sup> - <span class="pressure">' + press + "</span><sup>hPa</sup></div>",
	wind = '<div id="wind"><span id="arrow">&#10148;</span><span>' + wind_speed + '</span><sup>Km/h</sup></div>';
	wind_desc = '<div id="wind_desc">&#8220;' + get_wind_direction(data.wind.deg) + "&#8221;</div>",
	data_source = '<div class="OWStation">Dati OpenWeatherChannel</div>';
	
	if($("#temp").html().length == 0) {
		$("#temp").html(title + data_source + temperature + pressure + wind + wind_desc).fadeIn(600);
	} else {
		$(".temperature").html(cent + "<sup>&#8451;<sup>");
		$(".humidity").html(hum);
		$(".pressure").html(press);
		$("#wind").html('<span id="arrow">&#10148;</span><span>' + wind_speed + '<sup>Km/h</sup>');
		$("#wind_desc").html("&#8220;" + get_wind_direction(data.wind.deg) + "&#8221;");
	}
	rotate(data.wind.deg);
}
function refresh(layers) {
	for (i = 0; i < layers.length; i++){
		var layer = layers[i];
		layer.moveTo(layer.map.getExtent(), true);
	}
}
function recenter_map(map) {
	if($("#lat").val().length > 0 && $("#lon").val().length > 0){
		var center = new OpenLayers.LonLat($("#lon").val(), $("#lat").val()).transform(
			new OpenLayers.Projection("EPSG:4326"),
			map.getProjectionObject()
		);
		map.setCenter(center, 12);
	}
}
function registerEvents(layer) {
	layer.events.register("loadend", layer, function() {
		alert("ok");
		this.logEvent("Load End. Grid:" + this.grid.length + "x" + this.grid[0].length);
	});
	map.addLayer(layer);
}