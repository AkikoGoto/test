var geocoder;
var map;
var address;
var marker = null;

function doGeocode() {

	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(-34.397, 150.644);
	var myOptions = {
		zoom : 15,
		center : latlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	address = $("#geocode_address").text();

	geocoder.geocode({
		'address' : address
	}, function(results, status) {
		if (status == google.maps.GeocoderStatus.OK) {

			print_address(results);

		} else {
			alert("Geocode was not successful for the following reason: "
					+ status);
		}
	});

	// クリックした場所の緯度経度に更新
	google.maps.event.addListener(map, 'click', mylistener);

}

function print_address(results, status) {

	if (status == google.maps.GeocoderStatus.OK) {
		alert("緯度経度の取得に失敗しました。");
	} else {

		// location�I�u�W�F�N�g��Latlng�N���X
		var lng = results[0].geometry.location.lng();
		var lat = results[0].geometry.location.lat();
		// var lng=results[0].geometry.location.c;
		// var lat=results[0].geometry.location.b;

		var lng_box = document.getElementById("lng");
		var lat_box = document.getElementById("lat");

		var textNode_lng = document.createTextNode(lng);
		var textNode_lat = document.createTextNode(lat);

		var longitude = document.profile.longitude;
		var latitude = document.profile.latitude;

		lng_box.appendChild(textNode_lng);
		lat_box.appendChild(textNode_lat);

		longitude.value = lng;
		latitude.value = lat;

		map.setCenter(results[0].geometry.location);
		marker = new google.maps.Marker({
			map : map,
			position : results[0].geometry.location
		});

	}
}

// クリックした場所の緯度経度を取得
function mylistener(event) {

	// まず古いマーカーを削除
	marker.setMap(null);

	var lat = event.latLng.lat();
	var lng = event.latLng.lng();

	document.getElementById("lat").innerHTML = lat;
	document.getElementById("lng").innerHTML = lng;

	var longitude = document.profile.longitude;
	var latitude = document.profile.latitude;

	latitude.value = lat;
	longitude.value = lng;

	marker = new google.maps.Marker({
		map : map,
		position : event.latLng
	});

	// リバースジオコーディング
	geocoder = new google.maps.Geocoder();
	geocoder.geocode({
		'latLng' : event.latLng
	}, function(results, status) {

		if (status == google.maps.GeocoderStatus.OK) {

			var info_address = results[1].formatted_address.split(",", 2);

			var address = document.profile.address;
			address.value = info_address[1];

			$("#address").text(info_address[1]);
			$("#address_input").value = info_address[1];
			
		} else {
			alert("Geocoder failed due to: " + status);
		}
	});

}

function DisplayPropertyNames(place) {
	var names = "";
	for ( var name in place)
		names += name + "\n";
	alert(names);

}