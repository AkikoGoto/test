//ジオコーディング　ルート詳細の編集画面用

var geocoder;
var map;
var marker = null;
var j = 0;
var str_no = 0;

function doGeocode() {

	geocoder = new google.maps.Geocoder();
	var latlng = new google.maps.LatLng(-34.397, 150.644);
	var myOptions = {
		zoom : 15,
		center : latlng,
		mapTypeId : google.maps.MapTypeId.ROADMAP
	}
	map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

	for (var i = 0; i < 1; i = i +1){
		
		//IDごとに住所を取得
		var number = "#"+i;
		var each_address = $(number).find('.address_input').val();
		console.log(each_address);
		var each_lat = $(number).find('#lat').text();
		var each_lng = $(number).find('#lng').text();
		
		//住所がある場合		
		if(each_address != ''){
					
			if(each_address){
				
				//非同期で実行
				geocoder.geocode({
					
					'address' : each_address
					
				}, function(results, status) {
					
					if (status == google.maps.GeocoderStatus.OK) {

						print_address(results, status);
			
					} else {
						alert("Geocode was not successful for the following reason: "
								+ status);
					}
				});
			}
			

		}else{
			//住所がなくて緯度経度がある場合			
			if(each_lat && each_lng){
				
				latlng = new google.maps.LatLng(each_lat, each_lng); 

				map.setCenter(latlng);
                marker = new google.maps.Marker({
                    map: map, 
                    position: latlng,
            		draggable:true
                });
			}
			
		}
		//初期化
		address = null;
	}

	markerDragged();
}

function print_address(results, status) {

	if (status != google.maps.GeocoderStatus.OK) {
		
		alert("緯度経度の取得に失敗しました。");
	
	} else {
				
		var lng = results[0].geometry.location.lng();
		var lat = results[0].geometry.location.lat();
		
		$('#lng').text(lng);
		$('#lat').text(lat);
		
	   	//フォームの中身を書き換え
	   	$('#latitude').val(lat);
	   	$('#longitude').val(lng);
		
		j++;

	}
		
	map.setCenter(results[0].geometry.location);
	marker = new google.maps.Marker({
		map : map,
		position : results[0].geometry.location,
		draggable:true

	});
	markerDragged();
 
}

// マーカーをドラッグした場所の緯度経度に更新

function markerDragged(){
	google.maps.event.addListener(marker, "dragend", function(event) { 
	       
		   var lat = event.latLng.lat(); 
	    var lng = event.latLng.lng(); 
	    
	    //HTMLを書き換え
		   	$('#lat').text(lat);
		   	$('#lng').text(lng);
	
		   	//フォームの中身を書き換え
		   	$('#latitude').val(lat);
		   	$('#longitude').val(lng);
		
		   	// リバースジオコーディング
		   	geocoder = new google.maps.Geocoder();
		   	geocoder.geocode({
		   		'latLng' : event.latLng
		   	}, function(results, status) {
	
		   		if (status == google.maps.GeocoderStatus.OK) {
		
		   			var info_address = results[1].formatted_address.split(",", 2);
		   			
		   			$("#form_address").val(info_address[1]);
		   			$(".address_input").val(info_address[1]);
		
		   		} else {
		   			alert("Geocoder failed due to: " + status);
		   		}
		   	});
	      
	  });	
}

