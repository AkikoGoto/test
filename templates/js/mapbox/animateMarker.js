/**
 * マーカーのアニメーション
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @returns
 */

var intervalID;
var marker_index = 0;
var datetime_popup;

function animateMarker(datas) {

	//一番最初に再生を押されたときだけ、レイヤーを追加する
	if (marker_index == 0) {
		map.addSource('point', {
			"type" : "geojson",
			"data" : datas[0]
		});

		map.addLayer({
			"id" : "point",
			"source" : "point",
			"type" : "circle",
			"paint" : {
				"circle-radius" : 10,
				"circle-color" : "#007cbf"
			}
		});
	}

	var data_number = datas.length - 1;
	
	if(marker_index == 0){
		marker_index = data_number;
	}

	function moveMarker() {

		if (marker_index >= 0) {
			
			map.getSource('point').setData(datas[marker_index]);
			map.jumpTo({
				center : datas[marker_index].geometry.coordinates,
			});

			if(marker_index ==data_number || marker_index % 10 == 0){
			
				
				if(datetime_popup != null && datetime_popup.isOpen){
					datetime_popup.remove();
				}	

				
			   datetime_popup = new mapboxgl.Popup()
			       .setLngLat(datas[marker_index].geometry.coordinates)
			       .setHTML(datas[marker_index].properties.created)
			       .addTo(map);
				}
			
			marker_index--;

		} else {

			clearInterval(intervalID);
			
			//データのカウンターを戻す
			marker_index = data_number;

		}

	}

	intervalID = window.setInterval(moveMarker, 500);

}

function pointOnCircle(angle) {

	var radius = 20;
	return {
		"type" : "Point",
		"coordinates" : [ Math.cos(angle) * radius, Math.sin(angle) * radius ]
	};
}

function stopRouteAnimation() {

	clearTimeout(intervalID);

}