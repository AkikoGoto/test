/**
 * エリアのポリゴンを地図に描画とエリアの名前をポップアップウィンドウで表示する
 * @author Akiko Goto
 * @since 2017/04/27
 * @version 2.1
 * @param coordinates 緯度経度の配列, radius　半径（メートル）, area_name　エリアの名前, color　色, area_id ナビエリアのレイヤーID　重複しない、navi_areaから始まるString
 * @returns
 */

function addAreaPolygonAndPopup(coordinates, radius, area_name, color, area_id){
	var area = {
			  type: 'Feature',
			  geometry: {
			    type: 'Point',
			    coordinates: coordinates
			  },
			  properties: {
			    name: area_name
			  }
			};
	
	var area_polygon = turf.buffer(area, radius, 'meters');
	
	area_polygon.features[0].properties.name = area_name;
	
	addPolygon(area_polygon.features, area_id, color, color);
	
	makeMapboxPopup(area_id);
	
}