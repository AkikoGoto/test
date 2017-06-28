var geocoder;
var map;
var mapBounds = new google.maps.LatLngBounds();
var marker = null;
var str_no = 0;
var destination_max;
var destinationIterator = 0;
var baseURL;
var destinations = new Array();
var driverPolylines = [];

// drawing line
var points = new Array();
var locationArray = new Array();
var rootPathArray = new Array();
var marker = null;
var polylineIterator = 0;

// root information
var whole_distance = 0;
var whole_time = 0;
var animationRoutes = new Array();

function shortestRootMapGeocode( destination_count ) {

	var loc = window.location.pathname;
	baseURL = loc.substring(0, window.location.pathname.lastIndexOf('/'));

	destination_max = destination_count;
	var geocoding_max = destination_max - 1;
	
	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(-34.397, 150.644);
	var myOptions = {
		zoom : 15,
		center : latlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	};
	
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	// geocoder.geocodeコールバック用関数の引数に「場所番号」、「問い合わせ住所」を追加
	function geocoder_callback(location_no, query_address) {
		return function(results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				
				destinationIterator++;
				if( destinationIterator <= destination_count ) {
					//住所、ポイントを地図上に表示
					get_destination_info(location_no, query_address, results, status);
				} else {
					// Need to resize
					resizeToFit();
				};
				
			} else {
				var over_limit = "OVER_QUERY_LIMIT";
				var target_status = " " + status + " "; 
				if (target_status.indexOf(" " + over_limit + " ") !== -1) {
					setTimeout( function (){
						geocoder.geocode({
							'address' : query_address
						}, geocoder_callback(location_no, query_address));
					}, 800);
					$('#result').text( '※住所の緯度経度を取得中...' );
				} else {
					$('#result').text( '※不適切な住所がルートに含まれているため、最短経路を検索できませんでした。' );
				}
				
			}
			
		};
		
	}
	
	for (var i = 0; i < destination_count; i = i +1){
		
		//IDごとに住所を取得
		var number = "#"+i;
		var each_address = $(number).find('.address_input');
				
		if(each_address[0]){
			var address = each_address[0].value;
					
			if(address){
				
				//非同期で実行
				geocoder.geocode({
					'address' : address
				}, geocoder_callback(i, address));
				
			}
		}
		//初期化
		address = null;
	}

}

function get_destination_info(location_no, query_address, results, status) {

	// html の表示
	if (status != google.maps.GeocoderStatus.OK) {
		alert("緯度経度の取得に失敗しました。");
		alert("「" + query_address + "」の緯度経度の取得に失敗しました。");
		return;
	}
	
	if(typeof console != 'undefined'){
		console.log('location:' + location_no + ' query:' + query_address);
		console.log(results);
	}
		
	var lng = results[0].geometry.location.lng();
	var lat = results[0].geometry.location.lat();
	
	var detail_number = "#" + location_no;
	$(detail_number).find('#lng').text(lng);
	$(detail_number).find('#lat').text(lat);
	
   	//フォームの中身を書き換え
   	$(detail_number).find('#latitude').val(lat);
   	$(detail_number).find('#longitude').val(lng);
	
   	//地図の表示領域調整
   	mapBounds.extend(results[0].geometry.location);
   	map.fitBounds(mapBounds);

	// 最短ルート取得
	var destination = { "lat":lat,
						"lng" : lng,
						"name": query_address };
	destinations.push( destination );
	
	if( destinationIterator >= destination_max ) {		
		foreachGetShortestRoute ( destinations );
		resizeToFit();
	};
}

//最短経路を取得
function get_shortest_root ( datas ) {

	console.log ( datas );
	
	var defer = $.Deferred();
    $.ajax({
    	type:"POST",
        url: baseURL+'/?action=/root_detail/get_shortest_root',
        data: { root_detail : datas },
        success: defer.resolve,
        error: defer.reject
    });
    return defer.promise();
    
}

function foreachGetShortestRoute( destinations ) {

	$('#result').text( '最短ルートの経路を取得中...' );
	get_shortest_root( destinations )
		.done(function(data){

			console.log( data );
			$('#result').text( '' );
			var datas = JSON.parse( data );
			var max = 0;
			if ( datas.status != 'CANNOT_SEARCH_ROUTE') {
				
				roots = datas;
				foreachDrawShortestRoute ( roots );
				max = roots.length - 1;
				
			} else {
				
				$('#result').text( '※このルートは現在のところ、ルート検索の対象圏外です。' );
				roots = datas.origin_route;
				max = roots.length;
				
			}
				
			//マーカー
			var marker_list = new google.maps.MVCArray();
			//表の行に設定するための値の配列
console.log("MAX=!!!!!!!!!!!!!"+max);
			var values_by_line = new Array();
			for ( var i = 0; i < max; i++ ) {

				var location = roots[i];
				var lat = location['lat'];
				var lng = location['lng'];
				var address = location['name'];
				
				//マーカーの生成
				var number = i + 1;
				var marker = get_marker_with_number( lat, lng, map, number );
				marker_list.push( marker );
				
				//表の際表示
				//同じ行の要素の値も取得したいため、name属性でまずは取得
				var detail_number_table = document.getElementsByName( address )[0];
				var pre_id = detail_number_table.id;
				var pre_id_val = "#" + pre_id;
				var display_destination = $(pre_id_val).find('#display_destination').text();
				var display_information = $(pre_id_val).find('#display_information').text();
				var hour = $(pre_id_val).find('#hour_input').val();
				var minutes = $(pre_id_val).find('#minutes_input').val();
				
				var values = {
						"display_destination" : display_destination,
						"address" : address,
						"lat" : lat,
						"lng" : lng,
						"display_information" : display_information,
						"hour" : hour,
						"minutes" : minutes
						};
				
				values_by_line.push( values );

				console.log( i );
			}
			
			// マーカーの設置
			marker_list.forEach(function(mkr, idx){
				mkr.setMap(map);
			});
			
			//表の書き換え
			for ( var i = 0; i < values_by_line.length; i++ ) {
				var values = values_by_line[i];
				var lat = values['lat'];
				var lng = values['lng'];
				var display_destination = values['display_destination'];
				var address = values['address'];
				var display_information = values['display_information'];
				var hour = values['hour'];
				var minutes = values['minutes'];
				var detail_number = "#" + i;
				$(detail_number).find('#lat').text(lat);
				$(detail_number).find('#lng').text(lng);
				$(detail_number).find('#hour').text(hour+'時');
				$(detail_number).find('#minutes').text(minutes+'分');
				var address_input = $(detail_number).find(".address_input");
				$(address_input).val( address );
				$(detail_number).find('#display_destination').text(display_destination);
				$(detail_number).find('#display_information').text(display_information);
				$(detail_number).find('#latitude').val(lat);
				$(detail_number).find('#longitude').val(lng);
				$(detail_number).find('#hour_input').val(hour);
				$(detail_number).find('#minutes_input').val(minutes);
				$(detail_number).find("#form_address").val( address );
				$(detail_number).find('#destination_name').val(display_destination);
				$(detail_number).find('#information').val(display_information);
			}
			
	})
	.fail(function(data){
		console.log ( data );
		if(destinationIterator < destination_max) {
			foreachDrawShortestRoute(destinations);
		}
	});
}

//マーカーのリストを返す
function get_marker_with_number ( lat, lng, map, number ) {
	
	//マーカーの生成
	var latlng = new google.maps.LatLng( lat, lng );
	var icon = "http://gmaps-samples.googlecode.com/svn/trunk/markers/red/marker" + ( number ) + ".png";
	var marker = new google.maps.Marker({
		map : map,
		position : latlng,
		icon : icon,
		draggable: false,
		my_id : number
	});
	
	return marker;
}

//ルートを描画
function draw_root ( datas ) {
	
	if ( !baseURL ) {
		var loc = window.location.pathname;
		baseURL = loc.substring(0, window.location.pathname.lastIndexOf('/'));
	}
	
	var defer = $.Deferred();
    $.ajax({
    	type:"POST",
        url: baseURL+'/?action=/root_detail/get_root_path',
        data: { root : datas },
        success: defer.resolve,
        error: defer.reject
    });
    return defer.promise();
    
}

//var lineSymbol = null;
//var rootPaths = new Array();
//var rootPathIndicater = 0;
function foreachDrawShortestRoute( roots ) {
	
//	var decoded_roots = JSON.parse( roots );
	var root_details = new Array();
	var max = roots.length - 1;
	for( var i = 0; i < max; i++ ) {
		
		var nextI = i + 1;
		var latLng = { 'name': roots[i]['name'], 'lat': roots[i]['lat'], 'lng': roots[i]['lng'] };
		var nextLatLng = { 'name': roots[nextI]['name'], 'lat': roots[nextI]['lat'], 'lng': roots[nextI]['lng'] };
		var root = [latLng, nextLatLng];
		console.log( root );
		root_details.push( root );
		
	}
	
	for ( var j = 0; j < root_details.length; j++ ) {

		var root = root_details[j];
		console.log(root);
		
		draw_root( root_details[j] )
		.done(function(data){

			console.log ( data );
			var path = data['paths'][0];
			var time = path['time'];
			var distance = path['distance'];
			var coordinates = path['points']['coordinates'];
			
			var points = Array();
			var rootCoordinates = new Array();
			
			for ( var i = 0; i < coordinates.length; i++ ) {
				var root_info = new Array();
				var longitude = coordinates[i][0];
				var latitude = coordinates[i][1];
				rootCoordinates[i] = new google.maps.LatLng(latitude, longitude);
			}
//			animationRoutes.push( rootCoordinates );
			
			var mapOptions = {
				zoom:3,
				center: new google.maps.LatLng(0,-180),
				mapTypeId : google.maps.MapTypeId.TERRAIN
			};
			
			/*
			if ( lineSymbol == null ) {

				lineSymbol = {
					path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
					scale: 3,
					strokeColor: '#19283c'
				};
				
			}
			*/
			var rootPath = new google.maps.Polyline( {
				path : rootCoordinates,
				geodesic : true,
				strokeColor : '#FF0000',
				strokeOpacity : 1.0,
				strokeWeight : 2/*,
				icons: [{
					icon: lineSymbol,
					offset: '100%'
				}]*/
			});
			rootPath.setMap(map);
//			rootPaths.push( rootPath );
			
			var datetime = time*1000;
			console.log( 'time is ' + time );
			console.log( 'distance is ' + distance );
			console.log('drawing polilynes is success');
			
			whole_distance += distance;
			whole_time += time;
			
			var whole_kilo_distance = Math.round( whole_distance / 100 ) / 10;
			console.log("whole distance is "+whole_kilo_distance);
			console.log("whole time is "+whole_time);
			$('#root_kilo_distance_result').text( whole_kilo_distance + "km" );
				
		})
		.fail(function(data){
			console.log ( data );
			if(destinationIterator < destination_max) {
				foreachDrawShortestRoute();
			}
		});
		
	}
	
}
