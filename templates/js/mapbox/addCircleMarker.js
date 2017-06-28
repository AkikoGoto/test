/**
 * 小さい円のマーカーを作る　ドライバーの移動記録用
 * @autor Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @param colorList
 * @returns
 */

function addCirleMarker(datas, source_id, colorList){
	map.on('style.load', function() {
	
			map.addSource(source_id, {
				"type" : "geojson",
				"data" : {
					"type" : "FeatureCollection",
					"features" : datas
	
				}
			});
	
			map.addLayer({
				"id" : source_id,
				"type" : "circle",
			     "source": source_id,
			     "paint": {
			    	'circle-stroke-color' : { 
	                    "property": "is_deviated",
	                    "type": "categorical",
	                    "stops": [
	                              ["0", "#f5f0f0"],
	                              ["1", "#FE2E2E"],
	                          ]
	                },
			    	'circle-stroke-width' : { 
	                    "property": "is_deviated",
	                    "type": "categorical",
	                    "stops": [
	                              ["0", 2],
	                              ["1", 5],
	                          ]
	                },
	                'circle-radius': 5,
	                'circle-color':  { 
	                    "property": "status",
	                    "type": "categorical",
	                    "stops": colorList                            
	                }
	            },
			}, 'destinations_symbol');
			
	
	});
}