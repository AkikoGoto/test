/**
 * Mapboxの地図上のクリックされた地点の緯度経度を取得し、マーカー画像をクリックした地点に置く
 * @author Akiko Goto
 * @since 2017/04/12
 * @version 2.0
 * @param 
 * @returns
 */

function getLatLngAndAddMarker(){
	map.on('click', function (e) {

		$("#long").val(e.lngLat.lng);
		$("#lat").val(e.lngLat.lat);
		
		addForcusMarkerAndWindow([e.lngLat.lng, e.lngLat.lat]); 
	});
     	
}