/**
 * ポリゴンの円を描画する
 * @autor Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param area エリアの緯度経度情報, area_name　エリアの名前　重複不可, fillColor　ポリゴンの中を塗りつぶす色, outlineColor　ポリゴンの輪郭の色
 * @returns
 */

function addPolygon(area, area_name, fillColor, outlineColor){

	map.on('style.load', function() {
	
		map.addSource(area_name, 
				{
					"type" : "geojson",
					"data" : {
						"type" : "FeatureCollection",
						"features" : area
		
					}
				}
		);
	
		map.addLayer({
			"id" : area_name,
			"type" : "fill",
		     "source": area_name,
		     "paint": {
		    	 "fill-color": fillColor,
		    	 "fill-opacity": 0.5,
		    	 "fill-outline-color": outlineColor
	            }
	        });
	
	});
}