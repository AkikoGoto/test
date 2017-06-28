/**
 * 
 */

var companyID = 0;
var allDrivers = [];
var driverIterator = 0;
var timeFrom = '';
var timeTo = '';
var map;
var driverPolylines = [];
var selectedPolyLine = null;
var color;
var pointMarkers = new Array();
var working_status;

var baseURL = '';
function initializeMap(lat, lng, company_id, time_from, time_to, all_drivers, project_id, delivered_working_status, access_token) {

	working_status = delivered_working_status;
	
	L.mapbox.accessToken = access_token;
	
	map = L.mapbox.map('map', project_id).setView([lat, lng], 10);

	var loc = window.location.pathname;
	baseURL = loc.substring(0, window.location.pathname.lastIndexOf('/'));

	companyID = company_id;
	drivers = $.parseJSON(all_drivers);
	timeFrom = time_from;
	timeTo = time_to;
	
	foreachDriverDrawRoute();

}

function getDriverStatus(driver_id) {
	var defer = $.Deferred();
    $.ajax({
        url: baseURL,
        data: {
        	action: 'driver_status_history_json',
        	company_id: companyID,
            driver_id: driver_id,
            time_from: timeFrom,
            time_to: timeTo
        },
        dataType: 'json',
        success: defer.resolve,
        error: defer.reject
    });
    return defer.promise();
}

function foreachDriverDrawRoute() {
	var driver = drivers[driverIterator];
	getDriverStatus(driver.id)
		.done(function(data){
			
			    var driver = drivers[driverIterator];
				console.log(driverIterator+':'+driver.id+': success');
				
				// start loading next driver status while drawing.
				driverIterator++;
				if(driverIterator < drivers.length) {
					foreachDriverDrawRoute();
				} else {
					// Need to resize
					// if count of drivers is 1 then no lines shows on map with javascript error
					if (drivers.length != 1)
						resizeToFit();
				};
				
				/***************** マウスオーバーしたところの緯度経度を取得 ***************/
				/*
				var click = document.getElementById('click'),
			    mousemove = document.getElementById('mousemove');
				map.on('mousemove', function(e) {
				    console.log(e.containerPoint.toString() + ', ' + e.latlng.toString());
				    var mapMarker = L.marker([e.latlng.lat, e.latlng.lng], {
					    icon: L.icon({
					    	'iconUrl': 'templates/image/transparent_icon.png'
					    })
					});
		            mapMarker.addTo(map);
		            mapMarker.bindPopup('<p><b>'+e.containerPoint.toString() + ', ' + e.latlng.toString()+'</b></p>');
		            mapMarker.openPopup();
				});
				*/
				/***************** すべてにマーカーを設置 ***************/
				/*
				var points = Array();
				data.forEach(function(driver_status) {
					
					// Marker For Popup of driver status
					var mapMarker = L.marker([driver_status.latitude, driver_status.longitude], {
					    icon: L.icon({
					    	'iconUrl': 'templates/image/transparent_icon.png'
					    })
					});
		            mapMarker.addTo(map);
		            mapMarker.bindPopup('<p><b>'+driver_status.created+'</b></p><p>'+driver_status.address+'</p>');
		            mapMarker.on('mouseover', function(m) {
		            	mapMarker.openPopup();
		            });
					
		            // points for Polyline 
		            points.push(L.latLng(driver_status.latitude, driver_status.longitude) );
					var point = L.point();
				});
				//console.log(points);
				console.log(driver);
				driverPolylines[driver.id] = L.polyline(points, {color: '#'+driver.color, opacity:0.5}).addTo(map);
				*/
				/****************************************************/

				/**************************** サーバーにあるものと同じ ****************************/
				//console.log(data);
				var points = Array();
				data['points'].forEach(function(driver_status) {
					//console.log(driver_status);
					points.push(L.latLng(driver_status.latitude, driver_status.longitude) );
					
					var point = L.point();
				});
				//console.log(points);
				console.log(driver);
				var polyLine = L.polyline(points, {color: '#'+driver.color, opacity:0.5});
				polyLine.addTo(map);
				polyLine.on('mouseover', function(e) {
					console.log(e);
				});
				driverPolylines[driver.id] = polyLine;
				/****************************************************************************/
				
				// Even if count of drivers is 1 , call resizeToFit() without error
				if (drivers.length == 1)
					resizeToFit( driver.id );
				
		})
		.fail(function(data){
			console.log(driverIterator+':'+driver.id+': failed');
			console.log(data);
			driverIterator++;
			if(driverIterator < drivers.length) {
				foreachDriverDrawRoute();
			}
		});
}

/*
 * 地図下の名前をクリックされたドライバーの緯度経度を取得し、透明マーカーを設置
 */
function getSelectedDriverPoints( driver_id ) {

	for (var i = 0; i < pointMarkers.length; i++){
		map.removeLayer(pointMarkers[i]); 
	}
	pointMarkers.length = 0;
	
	getDriverStatus(driver_id)
		.done(function(data){
				
			/***************** マーカーを設置 ***************/
			var driver_name = data['driver_name'];
			 data['points'].forEach(function(driver_status) {
				// Marker For Popup of driver status
				var mapMarker = L.marker([driver_status.latitude, driver_status.longitude], {
				    icon: L.icon({
				    	'iconUrl': 'templates/image/transparent_icon.png'
				    })
				});
				
				var status_ja;
				if ( driver_status.status == 1 ) {
					status_ja = working_status.action_1;
				} else if ( driver_status.status == 2 ) {
					status_ja = working_status.action_2;
				} else if ( driver_status.status == 3 ) {
					status_ja = working_status.action_3;
				} else if ( driver_status.status == 4 ) {
					status_ja = working_status.action_4;
				}
				
	            mapMarker.addTo(map);
	            mapMarker.bindPopup('<p><b>'+driver_status.created+'</b></p><p>'+driver_name+'</p><p>'+status_ja+'</p><p>'+driver_status.address+'</p>');
	            mapMarker.on('mouseover', function(m) {
	            	mapMarker.openPopup();
	            });
				
	            // points for Polyline 
	            pointMarkers.push( mapMarker );
			});
			/****************************************************/
		})
		.fail(function(data){
			console.log(driverIterator+':'+driver.id+': failed');
			console.log(data);
			driverIterator++;
			if(driverIterator < drivers.length) {
				foreachDriverDrawRoute();
			}
		});
}

function resizeToFitAll() {
	var mapBounds = _.reduce(driverPolylines, function(bounds, polyline) {
		if(!polyline) return bounds;
		
		var polylineBounds = polyline.getBounds();
		if(!bounds) {
			return polylineBounds;
		} else if(bounds.contains(polylineBounds)){
			return bounds;
		} else {
			return bounds.extend(polylineBounds);
		}
	});
	map.fitBounds(mapBounds);
}

function resizeToFit(driver_id) {
	if(driver_id == undefined) {
		resizeToFitAll();
		return;
	}
	if(selectedPolyLine) {
		selectedPolyLine.setStyle({opacity:0.5, weight:5, dasharray:null});
	}
	var polyline = driverPolylines[driver_id];
	polyline.bringToFront();
	console.log(polyline);
	var color = polyline.options.color;
	var bounds = driverPolylines[driver_id].getBounds();
	console.log(bounds);
	if(bounds.getNorth() != bounds.getSouth() || bounds.getWest() != bounds.getEast()) map.fitBounds(bounds);
	//polyline.setStyle({opacity:1, weight:10, color:"#FFFFFF"});
	
	selectedPolyLine = polyline;
	window.setTimeout(function(){
		selectedPolyLine.setStyle({weight:14, opacity:1});
		animateShrinkPolylineToDefault(selectedPolyLine);
	}, 100);
	
	getSelectedDriverPoints( driver_id );
}

function animateShrinkPolylineToDefault(polyline) {
	window.setTimeout(function(){
		var weight = polyline.options.weight;
		if(weight > 5) {
			weight-=2;
			polyline.setStyle({weight: weight, dasharray:null});
			animateShrinkPolylineToDefault(polyline);
		}
	}, 100);
}