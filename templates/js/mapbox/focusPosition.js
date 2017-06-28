/**
 * ピンで指定した場所に地図上で移動
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @returns
 */


function focusPosition(position_id) {

	var focus_point = driver_info_consecutive_id[position_id];
	
	map.setCenter(focus_point.geometry.coordinates);
	map.zoomTo(16);
	
	addForcusMarkerAndWindow(focus_point.geometry.coordinates); 
	

}


//ピンの画像は、常に1個しか表示しないので、ここで定義
var focusMarker;


function addForcusMarkerAndWindow(coordinates) {

	if(focusMarker != null){
		focusMarker.remove();
	}
	
    // create a DOM element for the marker
    var el = document.createElement('div');
    el.className = 'marker';
    el.style.backgroundImage = 'url(templates/image/marker.png)';
    el.style.backgroundRepeat = "no-repeat";
    el.style.width = '20px';
    el.style.height = '47px';


    // add marker to map
    focusMarker = new mapboxgl.Marker(el, {offset: [-20, -47]})
        .setLngLat(coordinates)
        .addTo(map);
	
	
}


