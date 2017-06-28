/**
 * Mapboxのポップアップウィンドウを表示する
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param layer_id
 * @returns
 */
function makeMapboxPopup(layer_id){

	// ポップアップでの表示
	map.on('click', function (e) {
	
		   var features = map.queryRenderedFeatures(e.point, { layers: [layer_id] });
		
		   if (!features.length) {
		       return;
		   }
	
		   var feature = features[0];

		   //輸送ルートの場合は、一つ一つレイヤーの名称が違う　transportで始まること
		   if (layer_id.match(/^transport/)) {

				   var popup = new mapboxgl.Popup()
				       .setLngLat(e.lngLat)
				       .setHTML(feature.properties.name)
				       .addTo(map);
				
			}
		   
		   //ナビエリアの場合は、一つ一つレイヤーの名称が違う　areaで始まること
		   if (layer_id.match(/^navi_area/)) {

				   var popup = new mapboxgl.Popup()
				       .setLngLat(e.lngLat)
				       .setHTML(feature.properties.name)
				       .addTo(map);
				
			}
		   
		   switch (layer_id){		   
		   
			   case 'drivers':
				   var url = "";
				   var anchor = "";
				   if(feature.properties.url != ""){
					   url = feature.properties.url + encodeURIComponent(feature.properties.car_type);
					   anchor = '<a href="' + url + '"target="_blank">' + url + "</a></br>";
				   }
				   var popup = new mapboxgl.Popup()
			       .setLngLat(feature.geometry.coordinates)
			       .setHTML(feature.properties.name + "<br/>" + feature.properties.status + "<br/>" + anchor + feature.properties.created)
			       .addTo(map);
			     break;

			   case 'destinations':  
				   			   
				   var info = '';
				   if(feature.properties.information != "undefined"){
					   info = feature.properties.information;
				   }
				   
				   var popup = new mapboxgl.Popup()
			       .setLngLat(feature.geometry.coordinates)
			       .setHTML("<a href =\"" + feature.properties.url + "\">" + feature.properties.name + "</a><br>" + info)
			       .addTo(map);
				   
				break;   
				
			   case 'driver_records':
				   var deviatedMessage = "";
				   if(feature.properties.is_deviated == "1"){
					   deviatedMessage = '<span class="deviated">ルートを外れた位置情報です！</span><br>';
				   }
				   var popup = new mapboxgl.Popup()
				       .setLngLat(feature.geometry.coordinates)
				       .setHTML( deviatedMessage + feature.properties.status_ja + "<br>" + feature.properties.created)
				       .addTo(map);
				
				break;
		     
		   }
	
		});
}