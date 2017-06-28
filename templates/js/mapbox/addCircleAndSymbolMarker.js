/**
 * Mapboxでシンボル（アイコン）と背景が色付きの円のマーカーを表示させる 主に配送先用
 * @author Akiko Goto
 * @since 6.0
 * @param destinations
 * @param source_id
 * @param colorList
 * @returns
 */

function addCircleAndSymbolMarker(destinations, source_id, colorList) {

	if(source_id != null){
		source_id = source_id.toString();
	}
	
	map.on('style.load', function() {

		map.addSource(source_id, {
			"type" : "geojson",
			"data" : {
				"type" : "FeatureCollection",
				"features" : destinations

			}
		});

		map.addLayer({
			"id" : source_id,
			"type" : "circle",
		     "source": source_id,
		     "paint": {
		    	'circle-stroke-color' : "#f5f0f0",
		    	'circle-stroke-width' : 2,
                'circle-radius': 12,
                'circle-color':  { 
                    "property": "id",
                    "type": "categorical",
                    "stops": colorList                            
                }
            },
		}, 'destinations_symbol');
		
		map.addLayer({
			"id" : source_id + "_symbol",
			"type" : "symbol",
			"source" : source_id,
			"layout" : {
				"icon-image" : "building-15",
				"text-field" : "{name}",
				"text-font" : [ "Open Sans Semibold",
						"Arial Unicode MS Bold" ],
				"text-offset" : [ 0, 1 ],
				"text-size" : 13,
				"text-anchor" : "top"
			},
            "paint": {
                "text-color": "#023295",
                "text-halo-color" : "#ffffff",
                "text-halo-width" : 2
            },
		});
		
	});

}