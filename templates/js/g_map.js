var locationArray = new Array();
var points = new Array();
var rootPathArray = new Array();
var old_time = 0;
var new_time = 0;
var map;

function setDatas ( root_locations ) {
	
	for( var key in root_locations ) {

		//緯度・経度がNULL、0でなければ線と点を表示
		var lat = root_locations[key]['latitude'];
		var lng = root_locations[key]['longitude'];
		if (lat > 0 && lng > 0 ) {

			//ドライバーステータスを日本語に変換
			var root = new google.maps.LatLng( lat, lng );
			var root_info = {
				"LatLng" : root,
				"destination_name" : root_locations[key]['destination_name'],
				"deliver_time" : root_locations[key]['deliver_time'],
				"address" : root_locations[key]['address']
			};
			
			locationArray.push(root_info);
			
			// Polyline配列
			
			if ( key > 0 ) {
				if ( root[key] != null ) {
					points[key] = [ root[ key - 1 ], root];
					
					var rootPath = new google.maps.Polyline({
						path: points[key],
						strokeColor: "#FF0000",
						strokeOpacity: 1.0,
						strokeWeight: 2
					});

					rootPathArray.push(rootPath);
				}
			} else {
				root[key] = null;
			}
			
		}
	}
	
}

	//空車検索用のJS
	var lat;
	var long;	
	var geocoder;
	var infowindow = new google.maps.InfoWindow();
	var marker_red = 'templates/image/map_marker_red.gif';
	var marker_blue = 'templates/image/map_marker_blue.gif';		
	var marker_green = 'templates/image/map_marker_green.gif';		
	var marker_purple = 'templates/image/map_marker_purple.gif';		
	var taxi_icons = new Array();
	var taxi_windows = new Array();
	
	function initialize( lat, long ) {
	
		//引数にJSONを渡そうとするとエラーが生じる
		//そのため、このファイルを読み込む前にsmartyのhtml側で、root_locationsを定義しておく
		setDatas( root_locations );
		
		geocoder = new google.maps.Geocoder();

		var myLatlng = new google.maps.LatLng( lat, long );
		var myOptions = {
		  zoom: 13,
		  center: myLatlng,
		  mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		
		//マーカーを表示
		var marker_list = new google.maps.MVCArray();
		var marker_points_for_root = new Array();
		for (var i = 0; i < locationArray.length; i++) {
		
			//マーカー生成
			var number = i + 1;
			var point_lat = locationArray[i].LatLng.lat();
			var point_lng = locationArray[i].LatLng.lng();
			var marker = get_marker_with_number( point_lat, point_lng, map, number );
			var root_point = { "lat" : point_lat, "lng" : point_lng, "name" : number };
			marker_points_for_root.push( root_point );
			
			/*名前とステータスをWindow表示*/
			
			//住所がnullの場合、空白を表示 	
			if(locationArray[i].address == null) {
				locationArray[i].address = '';
			} 
			 				
			var contentString = '<div id="content">'+ locationArray[i].address +'<br>'+locationArray[i].destination_name + '<br></div>';
			windows = new google.maps.InfoWindow({
				content: contentString
			});
	
			//タクシーアイコンへのマウスオーバーイベント追加
			attachInfoWindow( marker, windows, i );
	
			if(rootPathArray[i]){
				rootPathArray[i].setMap(map);
			}
			
			marker_list.push( marker );
	
		};
		
		// マーカーの設置
		marker_list.forEach(function(mkr, idx){
			mkr.setMap(map);
		});
		
		foreachDrawShortestRoute( marker_points_for_root );
		
		resizeToFit();
		
		function attachInfoWindow( icons, windows, i ) {
			//点画像へのマウスオーバーイベント追加　
			google.maps.event.addListener( icons, 'mouseover', function() {									
				windows.open( map, icons );
			});
	
		}
	}
			
	function resizeToFit() {
		if(locationArray.length > 0) {
			var mapBounds = new google.maps.LatLngBounds ();
			for(var i = 0; i < locationArray.length; i++) {
				mapBounds.extend(locationArray[i].LatLng);	
			}
			map.fitBounds (mapBounds);
		}
	}