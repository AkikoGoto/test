/**
 * Mapboxで画像のマーカーを表示させる
 * @author Akiko Goto
 * @since 2017/03/29
 * @version 2.1
 * @param datas
 * @returns
 */

function addImageMarker(datas){
// 画像がある場合は、画像をマーカーにする
		datas.forEach(function(marker) {

			if(marker.properties.image != null){
			    // create a DOM element for the marker
			    var el = document.createElement('div');
			    el.className = 'marker';
			    el.style.backgroundImage = 'url('+ marker.properties.image +')';
			    el.style.backgroundRepeat = 'no-repeat'; 
			    el.style.width = '60px';
			    el.style.height = '45px';
	
	
			    // add marker to map
			    new mapboxgl.Marker(el, {offset: [-60 / 2, -50 / 2]})
			        .setLngLat(marker.geometry.coordinates)
			        .addTo(map);
			}
		});	
		
}