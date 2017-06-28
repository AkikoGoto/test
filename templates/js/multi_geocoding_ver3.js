//ジオっコーディング　複数の住所のジオコード用

var geocoder;
var map;
var mapBounds = new google.maps.LatLngBounds();
var marker = null;
var str_no = 0;
var is_no_draggable = false;

function doGeocode() {

	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng( -34.397, 150.644 );
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

				print_address(location_no, query_address, results, status);
	
			} else {
				
				setTimeout( function (){
					geocoder.geocode({
						'address' : query_address
					}, geocoder_callback(location_no, query_address));
				}, 800);
//				alert("Geocode was not successful for the following reason: "
//						+ status + "  location no:"+location_no+"  address:"+query_address  );
				
			}

		};
		
	}
	
	for (var i = 0; i < 20; i = i +1){
		
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

function print_address(location_no, query_address, results, status, disable_to_draggable) {
	
	is_no_draggable = disable_to_draggable;
	
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
	
	
	//アイコンを数字にする
	 var icon = "http://gmaps-samples.googlecode.com/svn/trunk/markers/red/marker" + (location_no + 1) + ".png";
	
	//地図の表示領域調整
	//map.setCenter(results[0].geometry.location);
	 mapBounds.extend(results[0].geometry.location);
	 map.fitBounds(mapBounds);
	 
	 marker = new google.maps.Marker({
		 map : map,
		 position : results[0].geometry.location,
		 icon : icon,
		 draggable: !is_no_draggable,
		 my_id :location_no
	 });
	 is_no_draggable = false;
	 
	 // マーカーをドラッグした場所の緯度経度に更新
	 google.maps.event.addListener(marker, "dragend", function(event) { 
	   
		 var marker_id = this.my_id;
		 var lat = event.latLng.lat(); 
		 var lng = event.latLng.lng(); 
		 
		 var root_detail_id_no = marker_id;
		 str_no = "#" + root_detail_id_no;
		 //HTMLを書き換え
		 $(str_no).find('#lat').text(lat);
		 $(str_no).find('#lng').text(lng);
		 
		 //フォームの中身を書き換え
		 $(str_no).find('#latitude').val(lat);
		 $(str_no).find('#longitude').val(lng);
	
		 // リバースジオコーディング
	   	geocoder = new google.maps.Geocoder();
	   	geocoder.geocode({
	   		'latLng' : event.latLng
	   	}, function(results, status) {

	   		if (status == google.maps.GeocoderStatus.OK) {
	
	   			var info_address = results[1].formatted_address.split(",", 2);
	   			
	   			$(str_no).find("#form_address").val(info_address[1]);
	   			$(str_no).find(".address_input").val(info_address[1]);
	
	   		} else {
	   			alert("Geocoder failed due to: " + status);
	   		}
	   	});
         
     });
}
