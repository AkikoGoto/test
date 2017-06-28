/**
 * Mapboxで線を表示させる
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @returns
 */


function addLineOnStyleLoad(positionDatas, line_color, source_id) {
	
	map.on('style.load', function() {
		addLine(positionDatas, line_color, source_id);
	});

}

function addLine(positionDatas, line_color, source_id){
	
	map.addSource(source_id, {
		"type" : "geojson",
		"data" : {
			
               "type": "Feature",
                "properties": {},
                "geometry": {
                    "type": "LineString",
                    "coordinates":  
                    	positionDatas
                    	
                	}
                }
	});
	
	map.addLayer({
		"id" : source_id,
		"type" : "line",
		"source" : source_id,
		"layout" : {
			"line-join" : "round",
			"line-cap" : "round"
		},
		"paint" : {
			"line-color" : line_color,
			"line-opacity" : 0.5,
			"line-width" : 8
		}
	});
	
}