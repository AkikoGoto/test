/**
 * Mapboxのシンボル（アイコンつきのマーカー）を表示させる
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @returns
 */

function addSymbolMarker(datas){

	map.on('style.load', function() {
	
			map.addSource("drivers", {
				"type" : "geojson",
				"data" : {
					"type" : "FeatureCollection",
					"features" : datas
	
				}
			});
			
			map.addLayer({
						"id" : "drivers",
						"type" : "symbol",
						"source" : "drivers",
						"layout" : {
							"icon-image" : "{marker-symbol}",
							"text-field" : "{name}",
							"text-font" : [ "Open Sans Semibold",
									"Arial Unicode MS Bold" ],
							"text-offset" : [ 0, 2 ],
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