$.extend({
	showBarsDouble: function(chartName, forecast, callback) {
		var tmp_min_max = new Array();
		var tmp = new Array();
		var tmp_max = new Array();
		var tmp_min = new Array();
		var categories = new Array();
		//var categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		for(var i = 0; i <  forecast.length; i ++){
			categories.push(forecast[i]['dt'] * 1000 + time_zone);
			tmp.push(
				//forecast[i]['dt'] * 1000,
				Math.round((forecast[i]['main']['temp']-273.15) * 100) / 100 
			);
			tmp_min_max.push(
				[
					//forecast[i]['dt'] * 1000,
					Math.round((forecast[i]['main']['temp_min']-273.15) * 100) / 100,
					Math.round((forecast[i]['main']['temp_max']-273.15) * 100) / 100 
				]
			);

			tmp_min.push([
					//forecast[i]['dt'] * 1000,
					Math.round((forecast[i]['main']['temp_min']-273.15) * 100) / 100,
					Math.round((forecast[i]['main']['temp']-273.15) * 100) / 100 
			]);
			tmp_max.push([
				//forecast[i]['dt'] * 1000,
				Math.round((forecast[i]['main']['temp']-273.15) * 100) / 100,
				Math.round((forecast[i]['main']['temp_max']-273.15) * 100) / 100 
			]);
		}
		//console.log(tmp);
		window.chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'columnrange',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				} 
				//inverted: true
			},
			title: {
				//text: 'Variazione oraria delle temperature'
				text: null
			},
			subtitle: {
				text: null
			},
			xAxis: {
				type: 'datetime',
				categories:  categories,
				tickInterval: 8,
				labels: {
					formatter: function() {
						return Highcharts.dateFormat('%H:00', this.value);
					}
				}
			},
			yAxis: {
				title: {
					text: 'Temperature (°C)'
				}
			},
			tooltip: {
				valueSuffix: '°C'
			},
			plotOptions: {
				columnrange: {
					dataLabels: {
						enabled: true,
						formatter: function () {
							return this.y + '°C';
						},
						y: 0
					}
				}
			},
			legend: {
				enabled: true
			},
			series: [{
				name: 'Soglia (min e max)',
				data:  tmp_min_max,
			},{
				name: 'Temperatura registrata',
				data:  tmp,
				type: 'spline'
			}]
		});
	}
});
$.extend({
	showBars: function(chartName, forecast, callback) {
		var tmp = new Array();
		var categories = new Array();
		//var categories = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
		for(var i = 0; i <  forecast.length; i ++){
			categories.push(forecast[i]['dt'] * 1000);
			tmp.push([
				forecast[i]['dt'] * 1000,
				Math.round((forecast[i]['main']['temp_min']-273.15) * 100) / 100,
				Math.round((forecast[i]['main']['temp_max']-273.15) * 100) / 100 
			])
		}
		//console.log(tmp);
		window.chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'columnrange',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				} 
				//inverted: true
			},
			title: {
				text: 'Variazione oraria delle temperature'
			},	
			subtitle: {
				text: null
			},
			xAxis: {
				type: 'datetime'
				//categories:  categories
			},
			yAxis: {
				title: {
					text: 'Temperature (°C)'
				}
			},
			tooltip: {
				valueSuffix: '°C'
			},
			/*
			plotOptions: {
				columnrange: {
					dataLabels: {
						enabled: true,
						formatter: function () {
							return this.y + '°C';
						},
						y: 0
					}
				}
			},
			*/
			legend: {
				enabled: false
			},
			series: [{
				name: 'Temperature (°C)',
				data:  tmp
			}]
		});
	}
});
$.extend({
	showPolarSpeed: function(chartName, forecast, callback) {
		var options = {
			xAxis: {
				categories:["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSO", "SO", "OSO", "O", "ONO", "NO", "NNO"], 
				type:(void 0)
			}, 
			series:[
				{name:"&gt; 10 m/s", data:[0, 0, 0, 0, 0, 0, 0.13, 0, 0, 0, 0, 0, 0, 0, 0.03, 0.07]}, 
				{name:"8-10 m/s",data:[0, 0, 0, 0, 0, 0, 0.39, 0.49, 0, 0, 0, 0, 0.1, 0, 0.69, 0.13]}, 
				{name:"6-8 m/s", data:[0, 0, 0, 0, 0, 0.13, 1.74, 0.53, 0, 0, 0.13, 0.3, 0.26, 0.33, 0.66, 0.23]}, 
				{name:"4-6 m/s", data:[0, 0, 0, 0, 0, 0.3, 2.14, 0.86, 0, 0, 0.49, 0.79, 1.45, 1.61, 0.76, 0.13]}, 
				{name:"2-4 m/s", data:[0.16, 0, 0.07, 0.07, 0.49, 1.55, 2.37, 1.97, 0.43, 0.26, 1.22, 1.97, 0.92, 0.99, 1.28, 1.32]},
				{name:"0.5-2 m/s", data:[1.78, 1.09, 0.82, 1.22, 2.2, 2.01, 3.06, 3.42, 4.74, 4.14, 4.01, 2.66, 1.71, 2.4, 4.28, 5]},
				{name:"&lt; 0.5 m/s", data:[1.81, 0.62, 0.82, 0.59, 0.62, 1.22, 1.61, 2.04, 2.66, 2.96, 2.53, 1.97, 1.64, 1.32, 1.58, 1.51]}
			]
		};
		var n = ["N", "NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S", "SSO", "SO", "OSO", "O", "ONO", "NO", "NNO"]; 
		var tmp = [
			{name:"&gt; 10 m/s", data:[]},
			{name:"8-10 m/s",data:[]}, 
			{name:"6-8 m/s", data:[]}, 
			{name:"4-6 m/s", data:[]}, 
			{name:"2-4 m/s", data:[]},
			{name:"0.5-2 m/s", data:[]},
			{name:"&lt; 0.5 m/s", data:[]}
		];
		//console.log(tmp);	
		for(var i in n)	for(var g = 0; g <  7; g ++) tmp[g]['data'][i] = 0;
		//console.log(tmp);
		for(var i = 0; i <  forecast.length; i ++){
			var deg = forecast[i]['wind']['deg'] 
			var s = forecast[i]['wind']['speed'];
			var step = 24;
			for(var l = 0; l <  16; l ++) { 
				if(deg >= l*step && deg < (l+1)*step)	{
					break;
				}
			}
			if(s >= 0 && s < 0.5)	{tmp[6]['data'][l] ++; }
			if(s >= 0.5 && s < 2)	{tmp[5]['data'][l] ++; }
			if(s >= 2 && s < 4)	{tmp[4]['data'][l] ++; }
			if(s >= 4 && s < 6)	{tmp[3]['data'][l] ++; }
			if(s >= 6 && s < 8)	{tmp[2]['data'][l] ++; }
			if(s >= 8 && s < 10)	{tmp[1]['data'][l] ++; }
			if(s >= 10)			{tmp[0]['data'][l] ++; }
		}
		var fl= forecast.length;
		for(var i in n) {
			for(var g = 0; g <  7; g ++) {
				tmp[g]['data'][i] = Math.round(100 * tmp[g]['data'][i] / fl);
			}
		}
		//console.log(tmp);
		options = {
			xAxis: {
				categories: [
					"NORD<br /><span style=\"color: #666;\">Tramontana</span>", 
					"NNE<br /><span style=\"color: #666;\">Bora</span>", 
					"NE<br /><span style=\"color: #666;\">Grecale</span>", 
					"ENE<br /><span style=\"color: #666;\">Schiavo</span>", 
					"EST<br /><span style=\"color: #666;\">Levante</span>", 
					"ESE<br /><span style=\"color: #666;\">Solano</span>", 
					"SE<br /><span style=\"color: #666;\">Scirocco</span>", 
					"SSE<br /><span style=\"color: #666;\">Africo</span>", 
					"SUD<br /><span style=\"color: #666;\">Ostro</span>", 
					"SSO<br /><span style=\"color: #666;\">Cauro</span>", 
					"SO<br /><span style=\"color: #666;\">Libeccio</span>", 
					"OSO<br /><span style=\"color: #666;\">Etesia</span>", 
					"OVEST<br /><span style=\"color: #666;\">Ponente</span>", 
					"ONO<br /><span style=\"color: #666;\">Traversone</span>", 
					"NO<br /><span style=\"color: #666;\">Maestrale</span>", 
					"NNO<br /><span style=\"color: #666;\">Zefiro</span>"
				], 
				type:(void 0)
			}, 
			series: tmp
		};
		// Create the chart
		window.chart = new Highcharts.Chart(Highcharts.merge(options, {
			chart: {
				renderTo: chartName,
				polar: true,
				type: 'column',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				} 
			},
			title: {
				text: 'Rosa dei venti'
			},
			subtitle: {
				text: 'Fonte: <a href="http://openweathermap.org">openweathermap.org</a>'
			},
			pane: {
				size: '85%'
			},
			legend: {
				reversed: true,
				align: 'right',
				verticalAlign: 'top',
				y: 100,
				layout: 'vertical'
			},
			xAxis: {
				tickmarkPlacement: 'on'
			},
			yAxis: {
				min: 0,
				endOnTick: false,
				showLastLabel: true,
				title: {
					text: 'Frequenza'
				},
				labels: {
					formatter: function () {
						return this.value + '%';
					}
				}
			},
			tooltip: {
				valueSuffix: '%'
			},
			plotOptions: {
				series: {
					stacking: 'normal',
					shadow: false,
					groupPadding: 0,
					pointPlacement: 'on'
				}
			}
		}));
	}
});
$.extend({
	showPolar: function(chartName, forecast, callback) {
		var n = {'N':{cn:0},'NNE':{cn:0},'NE':{cn:0},'ENE':{cn:0},'E':{cn:0},'ESE':{cn:0},'SE':{cn:0},'SSE':{cn:0}, 'S':{cn:0},'SSO':{cn:0}, 'SO':{cn:0},'OSO':{cn:0},'O':{cn:0},'ONO':{cn:0},'NO':{cn:0},'NNO':{cn:0}};
		var sSpeed = new Array();
		var sCnt = new Array();
		var sGust = new Array();
	}
});
$.extend({
	chartDoublePress: function(chartName, forecast, callback) {
		var chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'gauge',
				alignTicks: false,
				plotBackgroundColor: null,
				plotBackgroundImage: null,
				plotBorderWidth: 0,
				plotShadow: false,
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: {
				text: 'Pressione'
			},
			pane: {
				startAngle: -150,
				endAngle: 150
			},		
			yAxis: [{
				min: 980,
				max: 1050,
				lineColor: '#339',
				tickColor: '#339',
				minorTickColor: '#339',
				offset: -25,
				lineWidth: 2,
				labels: {
					distance: -20,
					rotation: 'auto'
				},
				tickLength: 5,
				minorTickLength: 5,
				endOnTick: false
			}, {
				min: 735,
				max: 787,
				tickPosition: 'outside',
				lineColor: '#933',
				lineWidth: 2,
				minorTickPosition: 'outside',
				tickColor: '#933',
				minorTickColor: '#933',
				tickLength: 5,
				minorTickLength: 5,
				labels: {
					distance: 12,
					rotation: 'auto'
				},
				offset: -20,
				endOnTick: false
			}],	
			series: [{
				name: 'Speed',
				data: [1015],
				dataLabels: {
					formatter: function () {
						var pa = this.y,
						mm = Math.round(pa / 1.3333);
						inh = Math.round(pa / 33.8653);
						return '<span style="color:#339">'+ pa + ' hPa</span><br>' + '<span style="color:#933">' + mm + ' mmHg</span>'; 
					},
					backgroundColor: {
						linearGradient: {
							x1: 0,
							y1: 0,
							x2: 0,
							y2: 1
						},
						stops: [
							[0, '#DDD'],
							[1, '#FFF']
						]
					}
				}
			}]
		});
	}
});
$.extend({
	chartSpeed: function(chartName, forecast, callback) {
		var chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'gauge',
				plotBackgroundColor: null,
				plotBackgroundImage: null,
				plotBorderWidth: 0,
				plotShadow: false,
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: {
				text: 'Pressione'
			},
			pane: {
				startAngle: -150,
				endAngle: 150,
				background: [{
					backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
						stops: [
							[0, '#FFF'],
							[1, '#333']
						]
					},
					borderWidth: 0,
					outerRadius: '109%'
				}, {
					backgroundColor: {
						linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
						stops: [
							[0, '#333'],
							[1, '#FFF']
						]
					},
					borderWidth: 1,
					outerRadius: '107%'
				}, {
					// default background
				}, {
					backgroundColor: '#DDD',
					borderWidth: 0,
					outerRadius: '105%',
					innerRadius: '103%'
				}]
			},
			// the value axis
			yAxis: {
				min: 950,
				max: 1050,
				minorTickInterval: 'auto',
				minorTickWidth: 1,
				minorTickLength: 10,
				minorTickPosition: 'inside',
				minorTickColor: '#666',
				tickPixelInterval: 30,
				tickWidth: 2,
				tickPosition: 'inside',
				tickLength: 10,
				tickColor: '#666',
				labels: {
					step: 2,
					rotation: 'auto'
				},
				title: {
					text: 'hPa'
				},
				plotBands: [{
					from: 950,
					to: 990,
					color: '#55BF3B' // green
				}, {
					from: 990,
					to: 1030,
					color: '#DDDF0D' // yellow
				}, {
					from: 1030,
					to: 1050,
					color: '#DF5353' // red
				}]	
			},
			series: [{
				name: 'Speed',
				data: [1020],
				tooltip: {
					valueSuffix: ' km/h'
				}
			}]
		});
	}
});
$.extend({
	showTempMinMax: function(chartName, forecast, callback) {
		var tmp = new Array();
		for(var i = 0; i <  forecast.length; i ++){
			tmp.push([
				forecast[i]['dt'] * 1000,
				Math.round((forecast[i]['main']['temp_min']-273.15) * 100) / 100,
				Math.round((forecast[i]['main']['temp_max']-273.15) * 100) / 100 
			])
		}
		//console.log(tmp);
		chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'arearange',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: {
				//text: 'Variazione delle temperature'
				text: null
			},
			xAxis: {
				type: 'datetime'
			},
			yAxis: {
				title: {
					text: 'Temperature (°C)'
				}
			},
			tooltip: {
				crosshairs: true,
				shared: true,
				valueSuffix: '°C'
			},
			
			legend: {
				enabled: false
			},
			series: [{
				name: 'Temperature',
				data: tmp
			}]
		});
	}
});
$.extend({
	showIconsChart: function(chartName, forecast, callback) {
		var tmp = new Array();
		var tm = new Array();
		var j=0;

		for(var i = 0; i <  forecast.length; i ++){
			var t = Math.round((forecast[i]['main']['temp']-273.15) * 100) / 100 ;
			if(j==8){
				if(forecast[i]['weather']) {
					var url = 'http://openweathermap.org/img/w/' + forecast[i]['weather'][0]['icon'] + '.png';
					t = {
						y: t,
						marker: {
							symbol: 'url('+url+')',
						}
					};
				}
				j=0;
			}
			tmp.push(t);
			tm.push(new Date(forecast[i]['dt'] * 1000 + time_zone));
			j++;
		}
		chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'spline',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: {
				text: 'Variazione oraria delle temperature'
			},
			yAxis: {
				title: {
					text: 'Temperature (°C)'
				}
			},
			xAxis: {
				type: 'datetime',
				categories: tm,
				tickInterval: 8,
				labels: {
					formatter: function() {
						return Highcharts.dateFormat('%H:00', this.value);
					}
				}
			},
			series: [{
				name: 'Temperatura',
				type: 'spline',
				data: tmp
			}]
		});
	}
});
$.extend({
	showSimpleChart: function(chartName, forecast, callback) {
		var tmp = new Array();
		var tm = new Array();
		var rain = 0;
		for(var i = 0; i <  forecast.length; i ++){
			if(forecast[i]['rain'] != undefined && forecast[i]['rain'] != null){
				if(forecast[i]['rain']["1h"] != undefined && forecast[i]['rain']["1h"] != null){
					rain = forecast[i]['rain']["1h"];
				} else {
					rain = 0;
				}
			} else {
				rain = 0;
			}
			//console.log(rain);
			tmp.push(rain);
			tm.push(new Date(forecast[i]['dt'] * 1000 + time_zone));
		}
		chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'spline',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: { text: 'Pioggia' },
			yAxis: {
				title: {
					text: 'Piogge (mm)'
				},
				min: 0,
				minorGridLineWidth: 0,
				gridLineWidth: 0,
				alternateGridColor: null,
				plotBands: [{ // Light air
					from: 0,
					to: 1,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Pioviggine',
						style: {
							color: '#606060'
						}
					}
				}, { // Light breeze
					from: 1,
					to: 2,
					color: 'rgba(0, 0, 0, 0)',
					label: {
						text: 'Pioggia debole',
						style: {
							color: '#606060'
						}
					}
				}, { // Gentle breeze
					from: 2,
					to: 6,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Pioggia moderata',
						style: {
							color: '#606060'
						}
					}
				}, { // Moderate breeze
					from: 6,
					to: 10,
					color: 'rgba(0, 0, 0, 0)',
					label: {
						text: 'Pioggia forte',
						style: {
							color: '#606060'
						}
					}
				}, { // Fresh breeze
					from: 10,
					to: 30,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Rovescio',
						style: {
							color: '#606060'
						}
					}
				}, { // Strong breeze
					from: 30,
					to: 50,
					color: 'rgba(0, 0, 0, 0)',
					label: {
						text: 'Nubifragio',
						style: {
							color: '#606060'
						}
					}
				}]
			},
			xAxis: {
				type: 'datetime',
				categories: tm,
				tickInterval: 8,
				labels: {
					formatter: function() {
						return Highcharts.dateFormat('%H:00', this.value);
					}
				}
			},
			series: [{
				name: 'Pioggia (mm)',
				type: 'spline',
				data: tmp
			}]
		});
	}
});
$.extend({
	showWind: function(chartName, forecast, callback) {
		var wind = new Array();
		var gust = new Array();
		var tm = new Array();
		for(var i = 0; i <  forecast.length; i ++){
			wind.push(1.0 * forecast[i]['wind']['speed']);
			if (forecast[i]['wind']['gust'] == undefined || forecast[i]['wind']['gust'] == null){
				forecast[i]['wind']['gust'] = 0;
			}
			gust.push(forecast[i]['wind']['gust']);
			tm.push(forecast[i]['dt'] * 1000 + time_zone);
		}
		chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'spline',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: {
				text: 'Velocità del vento'
			},
			subtitle: {
				text: 'Fonte: <a href="http://openweathermap.org">openweathermap.org</a>'
			},
			xAxis: [{
				type: 'datetime'
				//categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
			}],
			yAxis: {
				title: {
					text: 'Velocità del vento (m/s)'
				},
				min: 0,
				minorGridLineWidth: 0,
				gridLineWidth: 0,
				alternateGridColor: null,
				plotBands: [{ // Light air
					from: 0.3,
					to: 1.5,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Alito di vento',
						style: {
							color: '#606060'
						}
					}
				}, { // Light breeze
					from: 1.5,
					to: 3.3,
					color: 'rgba(0, 0, 0, 0)',
					label: {
						text: 'Brezza leggera',
						style: {
							color: '#606060'
						}
					}
				}, { // Gentle breeze
					from: 3.3,
					to: 5.5,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Brezza gentile',
						style: {
							color: '#606060'
						}
					}
				}, { // Moderate breeze
					from: 5.5,
					to: 8,
					color: 'rgba(0, 0, 0, 0)',
					label: {
						text: 'Brezza moderata',
						style: {
							color: '#606060'
						}
					}
				}, { // Fresh breeze
					from: 8,
					to: 11,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Vento moderato',
						style: {
							color: '#606060'
						}
					}
				}, { // Strong breeze
					from: 11,
					to: 14,
					color: 'rgba(0, 0, 0, 0)',
					label: {
						text: 'Vento forte',
						style: {
							color: '#606060'
						}
					}
				}, { // High wind
					from: 14,
					to: 15,
					color: 'rgba(68, 170, 213, 0.1)',
					label: {
						text: 'Vento molto forte',
						style: {
							color: '#606060'
						}
					}
				}]
			},
			tooltip: {
				formatter: function() {
					return Highcharts.dateFormat('%d/%m/%Y, %H:00', this.x) + ': '+ this.y +' m/s';
				}
			},
			plotOptions: {
				spline: {
					lineWidth: 2,
					states: {
						hover: {
						lineWidth: 5
						}
					},
					marker: {
						enabled: false,
						states: {
							hover: {
								enabled: true,
								symbol: 'circle',
								radius: 5,
								lineWidth: 1
							}
						}
					},
					pointInterval: 3600000, // one hour
					pointStart: tm[0]
				}
			},
			series: [{
				name: 'Vento',
				data: wind
			}, {
				name: 'Raffica',
				data: gust
			}],
			navigation: {
				menuItemStyle: {
					fontSize: '10px'
				}
			}
		});
	}
});
$.extend({
	showTemp: function(chartName, forecast, callback) {
		var tmp = new Array();
		var tmin = new Array();
		var tmax = new Array();
		var tm = new Array();
		for(var i = 0; i <  forecast.length; i ++){
			tmp.push(Math.round((forecast[i]['main']['temp'] -273.15) * 100) / 100);
			tmin.push(Math.round((forecast[i]['main']['temp_min'] -273.15) * 100) / 100);
			tmax.push(Math.round((forecast[i]['main']['temp_max'] -273.15) * 100) / 100);
			tm.push(new Date(forecast[i]['dt'] * 1000 + time_zone));
		}
		chart = new Highcharts.Chart({
			chart: {
				renderTo: chartName,
				type: 'spline',
				zoomType: 'xy',
				events: {
					load: function(event) {
						if(typeof callback == 'function'){
							setTimeout(function(){
								callback.call(this, event);
							}, 1000);
						}
					}
				}
			},
			title: {
				text: 'Temperatura',
				x: -20 //center
			},
			tooltip: {
				formatter: function() {
					return '<b>'+ this.series.name +"</b> <br><p>"+ Highcharts.dateFormat('%d/%m/%Y %H:%M', this.x)+' '+this.y +'°C </p>';
				}
			},
			xAxis: {
				type: 'datetime',
				categories: tm,
				tickInterval: 8,
				labels: {
				formatter: function() {
					if(Highcharts.dateFormat('%H', this.value) == '00')
						return Highcharts.dateFormat('%d %m', this.value);
					return Highcharts.dateFormat('%H:00', this.value);
					},
					align: 'right',
					style: {
						font: 'normal 13px Verdana, sans-serif'
					}
				}
			},
			yAxis: {
				title: {
					text: 'Temperatura (°C)'
				},
				plotLines: [{
					value: 0,
					width: 1,
					color: '#808080'
				}]
			},
			plotOptions: {
				spline: {
					lineWidth: 4,
					states: {
						hover: {
							lineWidth: 5
						}
					},
					marker: {
						enabled: false,
						states: {
							hover: {
								enabled: true,
								symbol: 'circle',
								radius: 5,
								lineWidth: 1
							}
						}
					}
				}
			},
			series: [{
				name: 'Temperatura min',
				type: 'spline',
				data: tmin
			}, {
				name: 'Temperatura max',
				data: tmax
			}]
		});
	}
});