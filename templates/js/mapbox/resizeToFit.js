/**
 * 地図にアイコンの全部が入るように表示する
 * 普通にboundsを取って地図を表示すると、アイコンが見づらいので、少し余裕をもって表示する
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param layer_id
 * @returns
 */

function resizeToFit(datas) {

	var bounds = new mapboxgl.LngLatBounds();

	if(datas.length > 0 ){

		datas.forEach(function(data) {
	    bounds.extend(data.geometry.coordinates);
		});
		
		map.fitBounds(bounds, {padding: 30});

	}

	
}